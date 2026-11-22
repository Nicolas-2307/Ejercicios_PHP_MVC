<?php
/**
 * Modelo Event: eventos del calendario con recordatorio.
 * Campos: id, title, description, date, time, category, reminder (minutos antes)
 */
class Event
{
    public const CATEGORIES = [
        'academico' => ['Académico', '#2563eb'],
        'trabajo'   => ['Trabajo', '#7c3aed'],
        'personal'  => ['Personal', '#16a34a'],
        'salud'     => ['Salud', '#ea580c'],
        'otro'      => ['Otro', '#64748b'],
    ];
    public const REMINDERS = [0 => 'Sin recordatorio', 10 => '10 minutos antes', 30 => '30 minutos antes',
        60 => '1 hora antes', 1440 => '1 día antes', 10080 => '1 semana antes'];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('events');
    }

    /** Eventos de un mes agrupados por fecha */
    public function byMonth(int $year, int $month): array
    {
        $prefix = sprintf('%04d-%02d', $year, $month);
        $out = [];
        foreach ($this->db->all() as $ev) {
            if (str_starts_with($ev['date'], $prefix)) {
                $out[$ev['date']][] = $ev;
            }
        }
        foreach ($out as &$list) {
            usort($list, fn($a, $b) => $a['time'] <=> $b['time']);
        }
        return $out;
    }

    /** Eventos futuros ordenados */
    public function upcoming(int $limit = 50): array
    {
        $now = date('Y-m-d H:i');
        $rows = array_filter($this->db->all(), fn($e) => "{$e['date']} {$e['time']}" >= $now);
        usort($rows, fn($a, $b) => "{$a['date']} {$a['time']}" <=> "{$b['date']} {$b['time']}");
        return array_slice($rows, 0, $limit);
    }

    /** Eventos cuyo recordatorio ya está activo (dentro de la ventana de aviso) */
    public function activeReminders(): array
    {
        $now = time();
        $out = [];
        foreach ($this->upcoming() as $e) {
            if (!$e['reminder']) continue;
            $start = strtotime("{$e['date']} {$e['time']}");
            if ($now >= $start - $e['reminder'] * 60) {
                $e['minutes_left'] = (int)round(($start - $now) / 60);
                $out[] = $e;
            }
        }
        return $out;
    }

    public function find(int $id): ?array
    {
        return $this->db->find($id);
    }

    public function validate(array $d): array
    {
        $e = [];
        if ($d['title'] === '') $e[] = 'El título es obligatorio.';
        if (!DateTime::createFromFormat('Y-m-d', $d['date'])) $e[] = 'Fecha no válida.';
        if (!preg_match('/^\d{2}:\d{2}$/', $d['time'])) $e[] = 'Hora no válida.';
        if (!isset(self::CATEGORIES[$d['category']])) $e[] = 'Categoría no válida.';
        if (!isset(self::REMINDERS[$d['reminder']])) $e[] = 'Recordatorio no válido.';
        return $e;
    }

    public function save(array $d, ?int $id = null): void
    {
        $id ? $this->db->update($id, $d) : $this->db->insert($d);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
