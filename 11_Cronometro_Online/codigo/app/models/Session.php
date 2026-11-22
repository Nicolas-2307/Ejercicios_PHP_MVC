<?php
/**
 * Modelo Session: sesiones de cronometraje guardadas con sus vueltas.
 * Campos: id, name, total_ms, laps: [ms de cada vuelta], date
 */
class Session
{
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('sessions');
    }

    /** Formatea milisegundos como hh:mm:ss.cc */
    public static function format(int $ms): string
    {
        $cs = intdiv($ms % 1000, 10);
        $s  = intdiv($ms, 1000);
        return sprintf('%02d:%02d:%02d.%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60, $cs);
    }

    public function validate(string $name, int $total, array $laps): array
    {
        $e = [];
        if ($total <= 0) $e[] = 'El cronómetro no ha registrado tiempo.';
        if (mb_strlen($name) > 50) $e[] = 'El nombre es muy largo.';
        foreach ($laps as $l) {
            if (!is_numeric($l) || $l < 0) { $e[] = 'Vueltas no válidas.'; break; }
        }
        return $e;
    }

    public function create(string $name, int $total, array $laps): array
    {
        return $this->db->insert([
            'name'     => $name ?: 'Sesión ' . date('d/m H:i'),
            'total_ms' => $total,
            'laps'     => array_map('intval', $laps),
            'date'     => date('Y-m-d H:i'),
        ]);
    }

    /** Estadísticas de las vueltas: mejor, peor y promedio */
    public static function stats(array $laps): array
    {
        if (!$laps) return ['best' => null, 'worst' => null, 'avg' => null];
        return ['best' => min($laps), 'worst' => max($laps), 'avg' => (int)(array_sum($laps) / count($laps))];
    }

    public function all(): array
    {
        return array_reverse($this->db->all());
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
