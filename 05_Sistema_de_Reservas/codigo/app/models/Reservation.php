<?php
/**
 * Modelo Reservation: reservas de citas.
 * Estados: pendiente -> confirmada | cancelada
 */
class Reservation
{
    private JsonStore $db;
    private Service $services;

    public function __construct()
    {
        $this->db = new JsonStore('reservations');
        $this->services = new Service();
    }

    /** Horarios ocupados (reservas no canceladas) para un servicio y una fecha */
    public function takenSlots(int $serviceId, string $date): array
    {
        $taken = [];
        foreach ($this->db->all() as $r) {
            if ($r['service_id'] === $serviceId && $r['date'] === $date && $r['status'] !== 'cancelada') {
                $taken[] = $r['time'];
            }
        }
        return $taken;
    }

    /** Disponibilidad: [hora => true|false] */
    public function availability(int $serviceId, string $date): array
    {
        $taken = $this->takenSlots($serviceId, $date);
        $out = [];
        $now = new DateTime();
        foreach ($this->services->slots() as $slot) {
            $past = new DateTime("$date $slot") <= $now;
            $out[$slot] = !in_array($slot, $taken, true) && !$past;
        }
        return $out;
    }

    public function validate(array $d): array
    {
        $errors = [];
        if (!$this->services->find($d['service_id'])) $errors[] = 'Servicio no válido.';
        $date = DateTime::createFromFormat('Y-m-d', $d['date']);
        if (!$date) $errors[] = 'Fecha no válida.';
        elseif ($d['date'] < date('Y-m-d')) $errors[] = 'No puedes reservar en una fecha pasada.';
        elseif ((int)$date->format('N') === 7) $errors[] = 'No se atiende los domingos.';
        if (!in_array($d['time'], $this->services->slots(), true)) $errors[] = 'Selecciona un horario.';
        if (mb_strlen($d['name']) < 3) $errors[] = 'Escribe tu nombre completo.';
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico no válido.';
        if (!$errors && !($this->availability($d['service_id'], $d['date'])[$d['time']] ?? false)) {
            $errors[] = 'Ese horario ya no está disponible. Elige otro.';
        }
        return $errors;
    }

    /** Crea la reserva en estado "pendiente" y genera un código */
    public function create(array $d): array
    {
        return $this->db->insert([
            'code'       => strtoupper(substr(bin2hex(random_bytes(4)), 0, 6)),
            'service_id' => $d['service_id'],
            'date'       => $d['date'],
            'time'       => $d['time'],
            'name'       => $d['name'],
            'email'      => $d['email'],
            'phone'      => $d['phone'],
            'status'     => 'pendiente',
            'created_at' => date('Y-m-d H:i'),
        ]);
    }

    public function find(int $id): ?array
    {
        return $this->db->find($id);
    }

    public function setStatus(int $id, string $status): void
    {
        $this->db->update($id, ['status' => $status]);
    }

    public function all(): array
    {
        $rows = $this->db->all();
        usort($rows, fn($a, $b) => [$a['date'], $a['time']] <=> [$b['date'], $b['time']]);
        return $rows;
    }
}
