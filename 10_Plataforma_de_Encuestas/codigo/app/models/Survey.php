<?php
/**
 * Modelo Survey: encuestas con varias preguntas de opción múltiple.
 * Estructura: id, title, description, author, created_at,
 *             questions: [ {text, options: [..]} ]
 */
class Survey
{
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('surveys');
    }

    public function all(): array
    {
        return array_reverse($this->db->all());
    }

    public function find(int $id): ?array
    {
        return $this->db->find($id);
    }

    /** Valida y normaliza los datos del formulario de creación */
    public function build(string $title, string $description, string $author, array $questions, array $options): array
    {
        $errors = [];
        $clean = [];
        if (mb_strlen($title) < 3) $errors[] = 'El título debe tener al menos 3 caracteres.';
        foreach ($questions as $i => $q) {
            $q = trim($q);
            $opts = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $options[$i] ?? ''))));
            if ($q === '' && !$opts) continue; // bloque vacío
            if ($q === '') { $errors[] = 'La pregunta ' . ($i + 1) . ' no tiene texto.'; continue; }
            if (count($opts) < 2) { $errors[] = "La pregunta “{$q}” necesita al menos 2 opciones."; continue; }
            $clean[] = ['text' => $q, 'options' => array_values(array_unique($opts))];
        }
        if (!$clean && !$errors) $errors[] = 'Agrega al menos una pregunta.';
        return [$errors, [
            'title' => $title, 'description' => $description,
            'author' => $author ?: 'Anónimo', 'created_at' => date('Y-m-d H:i'), 'questions' => $clean,
        ]];
    }

    public function create(array $survey): array
    {
        return $this->db->insert($survey);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
        (new Response())->deleteBySurvey($id);
    }
}
