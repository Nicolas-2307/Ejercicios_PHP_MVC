<?php
/**
 * JsonStore
 * Capa de persistencia sencilla basada en archivos JSON (carpeta /data).
 * Permite que los modelos guarden y lean información sin necesidad de
 * instalar un motor de base de datos.
 */
class JsonStore
{
    private string $file;

    public function __construct(string $name)
    {
        $dir = dirname(__DIR__) . '/data';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $this->file = $dir . '/' . $name . '.json';
        if (!file_exists($this->file)) {
            file_put_contents($this->file, '[]');
        }
    }

    /** Devuelve todos los registros */
    public function all(): array
    {
        $data = json_decode(file_get_contents($this->file), true);
        return is_array($data) ? $data : [];
    }

    /** Sobrescribe todos los registros */
    public function save(array $rows): void
    {
        file_put_contents(
            $this->file,
            json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }

    /** Busca un registro por id */
    public function find(int $id): ?array
    {
        foreach ($this->all() as $row) {
            if ((int)$row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    /** Inserta un registro y le asigna un id autoincremental */
    public function insert(array $row): array
    {
        $rows = $this->all();
        $max = 0;
        foreach ($rows as $r) {
            $max = max($max, (int)$r['id']);
        }
        $row = ['id' => $max + 1] + $row;
        $rows[] = $row;
        $this->save($rows);
        return $row;
    }

    /** Actualiza los campos de un registro */
    public function update(int $id, array $fields): void
    {
        $rows = $this->all();
        foreach ($rows as &$r) {
            if ((int)$r['id'] === $id) {
                $r = array_merge($r, $fields);
            }
        }
        $this->save($rows);
    }

    /** Elimina un registro */
    public function delete(int $id): void
    {
        $rows = array_filter($this->all(), fn($r) => (int)$r['id'] !== $id);
        $this->save($rows);
    }
}
