<?php
/**
 * Modelo Note: notas organizadas por categoría.
 * Campos: id, title, content, category, color, pinned, created_at, updated_at
 */
class Note
{
    public const CATEGORIES = ['Personal', 'Universidad', 'Trabajo', 'Ideas', 'Compras'];
    public const COLORS = ['#fef9c3', '#dbeafe', '#dcfce7', '#fce7f3', '#ede9fe', '#ffedd5'];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('notes');
    }

    /** Busca notas por texto (título o contenido) y filtra por categoría */
    public function search(string $q = '', string $category = ''): array
    {
        $q = mb_strtolower($q);
        $rows = array_filter($this->db->all(), function ($n) use ($q, $category) {
            $matchText = $q === '' || str_contains(mb_strtolower($n['title'] . ' ' . $n['content']), $q);
            $matchCat  = $category === '' || $n['category'] === $category;
            return $matchText && $matchCat;
        });
        usort($rows, fn($a, $b) => [$b['pinned'], $b['updated_at']] <=> [$a['pinned'], $a['updated_at']]);
        return $rows;
    }

    /** Cantidad de notas por categoría */
    public function countByCategory(): array
    {
        $out = array_fill_keys(self::CATEGORIES, 0);
        foreach ($this->db->all() as $n) {
            $out[$n['category']] = ($out[$n['category']] ?? 0) + 1;
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
        if ($d['content'] === '') $e[] = 'El contenido no puede estar vacío.';
        if (!in_array($d['category'], self::CATEGORIES, true)) $e[] = 'Categoría no válida.';
        return $e;
    }

    public function save(array $d, ?int $id = null): void
    {
        $fields = [
            'title'      => $d['title'],
            'content'    => $d['content'],
            'category'   => $d['category'],
            'color'      => in_array($d['color'], self::COLORS, true) ? $d['color'] : self::COLORS[0],
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($id) {
            $this->db->update($id, $fields);
        } else {
            $this->db->insert($fields + ['pinned' => false, 'created_at' => date('Y-m-d H:i:s')]);
        }
    }

    public function togglePin(int $id): void
    {
        $n = $this->db->find($id);
        if ($n) $this->db->update($id, ['pinned' => !$n['pinned']]);
    }

    public function delete(int $id): void
    {
        $this->db->delete($id);
    }
}
