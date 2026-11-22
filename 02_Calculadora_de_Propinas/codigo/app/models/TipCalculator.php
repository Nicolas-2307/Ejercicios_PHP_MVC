<?php
/**
 * Modelo TipCalculator: contiene la lógica de negocio del cálculo de
 * propinas y guarda un historial de los cálculos realizados.
 */
class TipCalculator
{
    public const PERCENTAGES = [5, 10, 15, 18, 20, 25];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('history');
    }

    /** Valida los datos de entrada. Devuelve array de errores */
    public function validate(float $amount, float $percent, int $people): array
    {
        $errors = [];
        if ($amount <= 0) {
            $errors[] = 'El monto de la cuenta debe ser mayor que cero.';
        }
        if ($percent < 0 || $percent > 100) {
            $errors[] = 'El porcentaje debe estar entre 0 y 100.';
        }
        if ($people < 1) {
            $errors[] = 'El número de personas debe ser al menos 1.';
        }
        return $errors;
    }

    /** Calcula la propina, el total y el valor por persona */
    public function calculate(float $amount, float $percent, int $people): array
    {
        $tip   = round($amount * $percent / 100, 2);
        $total = $amount + $tip;
        return [
            'amount'          => $amount,
            'percent'         => $percent,
            'people'          => $people,
            'tip'             => $tip,
            'total'           => $total,
            'tip_per_person'  => round($tip / $people, 2),
            'per_person'      => round($total / $people, 2),
        ];
    }

    public function saveHistory(array $result): void
    {
        $this->db->insert($result + ['date' => date('Y-m-d H:i')]);
    }

    public function history(): array
    {
        return array_reverse($this->db->all());
    }

    public function clearHistory(): void
    {
        $this->db->save([]);
    }
}
