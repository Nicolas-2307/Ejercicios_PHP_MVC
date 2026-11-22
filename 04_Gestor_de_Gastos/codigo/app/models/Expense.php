<?php
/**
 * Modelo Expense: gastos diarios con categoría.
 * Campos: id, date (Y-m-d), description, category, amount
 */
class Expense
{
    public const CATEGORIES = [
        'alimentacion' => ['Alimentación', '🍔', '#f97316'],
        'transporte'   => ['Transporte', '🚌', '#0ea5e9'],
        'vivienda'     => ['Vivienda', '🏠', '#8b5cf6'],
        'servicios'    => ['Servicios', '💡', '#eab308'],
        'salud'        => ['Salud', '💊', '#ef4444'],
        'educacion'    => ['Educación', '📚', '#22c55e'],
        'ocio'         => ['Ocio', '🎮', '#ec4899'],
        'otros'        => ['Otros', '📦', '#64748b'],
    ];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('expenses');
    }

    /** Gastos de un mes (formato Y-m), opcionalmente filtrados por categoría */
    public function byMonth(string $month, string $category = ''): array
    {
        $rows = array_filter($this->db->all(), fn($r) => str_starts_with($r['date'], $month)
            && ($category === '' || $r['category'] === $category));
        usort($rows, fn($a, $b) => [$b['date'], $b['id']] <=> [$a['date'], $a['id']]);
        return $rows;
    }

    public function validate(array $d): array
    {
        $errors = [];
        if (!DateTime::createFromFormat('Y-m-d', $d['date'])) $errors[] = 'La fecha no es válida.';
        if ($d['description'] === '') $errors[] = 'La descripción es obligatoria.';
        if (!isset(self::CATEGORIES[$d['category']])) $errors[] = 'Selecciona una categoría válida.';
        if (!is_numeric($d['amount']) || (float)$d['amount'] <= 0) $errors[] = 'El monto debe ser un número mayor que cero.';
        return $errors;
    }

    public function create(array $d): void
    {
        $this->db->insert([
            'date'        => $d['date'],
            'description' => $d['description'],
            'category'    => $d['category'],
            'amount'      => (float)$d['amount'],
        ]);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }

    /** Resumen de un mes: total, por categoría, por día y promedio diario */
    public function summary(string $month): array
    {
        $rows = $this->byMonth($month);
        $total = array_sum(array_column($rows, 'amount'));
        $byCat = [];
        foreach ($rows as $r) {
            $byCat[$r['category']] = ($byCat[$r['category']] ?? 0) + $r['amount'];
        }
        arsort($byCat);
        $byDay = [];
        foreach ($rows as $r) {
            $byDay[$r['date']] = ($byDay[$r['date']] ?? 0) + $r['amount'];
        }
        ksort($byDay);
        $days = (int)date('t', strtotime($month . '-01'));
        return [
            'total'   => $total,
            'count'   => count($rows),
            'byCat'   => $byCat,
            'byDay'   => $byDay,
            'avg'     => $days ? $total / $days : 0,
            'max'     => $rows ? max(array_column($rows, 'amount')) : 0,
            'days'    => $days,
        ];
    }

    /** Totales de los últimos N meses (para comparar) */
    public function monthlyTotals(int $n = 6): array
    {
        $out = [];
        for ($i = $n - 1; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("first day of -$i month"));
            $out[$m] = array_sum(array_column($this->byMonth($m), 'amount'));
        }
        return $out;
    }
}
