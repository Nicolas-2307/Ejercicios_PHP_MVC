<?php
/**
 * Modelo Task: representa una tarea de la lista.
 * Campos: id, title, priority (alta|media|baja), done (bool), created_at
 */
class Task
{
    private JsonStore $db;
    public const PRIORITIES = ['alta' => 'Alta', 'media' => 'Media', 'baja' => 'Baja'];

    public function __construct()
    {
        $this->db = new JsonStore('tasks');
    }

    /** Lista de tareas filtrada por estado: all | pending | done */
    public function all(string $filter = 'all'): array
    {
        $tasks = $this->db->all();
        if ($filter === 'pending') {
            $tasks = array_filter($tasks, fn($t) => !$t['done']);
        } elseif ($filter === 'done') {
            $tasks = array_filter($tasks, fn($t) => $t['done']);
        }
        // Pendientes primero, luego por prioridad
        $order = ['alta' => 0, 'media' => 1, 'baja' => 2];
        usort($tasks, fn($a, $b) => [$a['done'], $order[$a['priority']], $a['id']] <=> [$b['done'], $order[$b['priority']], $b['id']]);
        return $tasks;
    }

    public function stats(): array
    {
        $all = $this->db->all();
        $done = count(array_filter($all, fn($t) => $t['done']));
        return ['total' => count($all), 'done' => $done, 'pending' => count($all) - $done];
    }

    /** Valida y crea una tarea. Devuelve un mensaje de error o null */
    public function create(string $title, string $priority): ?string
    {
        if ($title === '') {
            return 'El título de la tarea no puede estar vacío.';
        }
        if (mb_strlen($title) > 120) {
            return 'El título no puede superar 120 caracteres.';
        }
        if (!isset(self::PRIORITIES[$priority])) {
            $priority = 'media';
        }
        $this->db->insert([
            'title'      => $title,
            'priority'   => $priority,
            'done'       => false,
            'created_at' => date('Y-m-d H:i'),
        ]);
        return null;
    }

    /** Cambia el estado completada / pendiente */
    public function toggle(int $id): void
    {
        $task = $this->db->find($id);
        if ($task) {
            $this->db->update($id, ['done' => !$task['done']]);
        }
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }

    public function clearCompleted(): void
    {
        $this->db->save(array_filter($this->db->all(), fn($t) => !$t['done']));
    }
}
