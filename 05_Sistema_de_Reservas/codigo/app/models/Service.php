<?php
/**
 * Modelo Service: catálogo de servicios que se pueden reservar.
 */
class Service
{
    private const SERVICES = [
        1 => ['name' => 'Corte de cabello',      'icon' => '💇', 'duration' => 60, 'price' => 25000],
        2 => ['name' => 'Consulta médica general','icon' => '🩺', 'duration' => 60, 'price' => 60000],
        3 => ['name' => 'Masaje relajante',      'icon' => '💆', 'duration' => 60, 'price' => 80000],
        4 => ['name' => 'Asesoría académica',    'icon' => '🎓', 'duration' => 60, 'price' => 35000],
    ];

    public function all(): array
    {
        return self::SERVICES;
    }

    public function find(int $id): ?array
    {
        return self::SERVICES[$id] ?? null;
    }

    /** Horarios de atención: 8:00 a 17:00 (bloques de 1 hora, sin la hora de almuerzo) */
    public function slots(): array
    {
        return ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
    }
}
