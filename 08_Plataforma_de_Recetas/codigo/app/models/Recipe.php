<?php
/**
 * Modelo Recipe: recetas de cocina.
 * Campos: id, name, type, difficulty, time, servings, ingredients[], steps[], author, emoji, saves
 */
class Recipe
{
    public const TYPES = ['Desayuno', 'Almuerzo', 'Cena', 'Postre', 'Bebida', 'Snack'];
    public const DIFFICULTIES = ['Fácil', 'Media', 'Difícil'];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('recipes');
        if (!$this->db->all()) {
            $this->seed();
        }
    }

    /**
     * Búsqueda con filtros.
     * @param string $q          Texto en el nombre
     * @param string $type       Tipo de comida
     * @param string $ingredient Ingrediente que debe contener
     * @param int    $maxTime    Tiempo máximo en minutos (0 = sin límite)
     */
    public function search(string $q = '', string $type = '', string $ingredient = '', int $maxTime = 0, string $difficulty = ''): array
    {
        $q = mb_strtolower($q);
        $ingredient = mb_strtolower($ingredient);
        $rows = array_filter($this->db->all(), function ($r) use ($q, $type, $ingredient, $maxTime, $difficulty) {
            if ($q !== '' && !str_contains(mb_strtolower($r['name']), $q)) return false;
            if ($type !== '' && $r['type'] !== $type) return false;
            if ($difficulty !== '' && $r['difficulty'] !== $difficulty) return false;
            if ($maxTime > 0 && $r['time'] > $maxTime) return false;
            if ($ingredient !== '') {
                $ings = mb_strtolower(implode(' ', $r['ingredients']));
                foreach (array_filter(array_map('trim', explode(',', $ingredient))) as $ing) {
                    if (!str_contains($ings, $ing)) return false;
                }
            }
            return true;
        });
        usort($rows, fn($a, $b) => $b['id'] <=> $a['id']);
        return $rows;
    }

    public function find(int $id): ?array
    {
        return $this->db->find($id);
    }

    public function validate(array $d): array
    {
        $e = [];
        if (mb_strlen($d['name']) < 3) $e[] = 'El nombre debe tener al menos 3 caracteres.';
        if (!in_array($d['type'], self::TYPES, true)) $e[] = 'Tipo de comida no válido.';
        if (!in_array($d['difficulty'], self::DIFFICULTIES, true)) $e[] = 'Dificultad no válida.';
        if ($d['time'] < 1) $e[] = 'El tiempo debe ser mayor que cero.';
        if (count($d['ingredients']) < 2) $e[] = 'Agrega al menos 2 ingredientes (uno por línea).';
        if (count($d['steps']) < 1) $e[] = 'Agrega al menos un paso de preparación.';
        if ($d['author'] === '') $e[] = 'Escribe tu nombre como autor.';
        return $e;
    }

    public function create(array $d): array
    {
        return $this->db->insert($d + ['saves' => 0, 'created_at' => date('Y-m-d')]);
    }

    public function changeSaves(int $id, int $delta): void
    {
        $r = $this->db->find($id);
        if ($r) $this->db->update($id, ['saves' => max(0, $r['saves'] + $delta)]);
    }

    /** Divide un texto en líneas no vacías */
    public static function lines(string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $text))));
    }

    private function seed(): void
    {
        $seed = [
            ['Arepas de queso', 'Desayuno', 'Fácil', 20, 4, '🫓', ['2 tazas de harina de maíz precocida', '2 tazas de agua tibia', '1 taza de queso costeño rallado', '1 cucharadita de sal', 'Mantequilla'], ['Mezcla la harina con el agua y la sal.', 'Agrega el queso y amasa hasta obtener una masa suave.', 'Forma las arepas y ásalas en un sartén con mantequilla 5 minutos por lado.']],
            ['Bandeja paisa', 'Almuerzo', 'Difícil', 120, 4, '🍛', ['500 g de fríjoles cargamanto', '400 g de carne molida', '4 huevos', '1 plátano maduro', '300 g de chicharrón', '2 tazas de arroz', '1 aguacate', '4 arepas'], ['Cocina los fríjoles con anticipación.', 'Prepara la carne molida sofrita.', 'Fríe el chicharrón, el plátano y los huevos.', 'Sirve todo en una bandeja con arroz, aguacate y arepa.']],
            ['Ajiaco santafereño', 'Almuerzo', 'Media', 90, 6, '🍲', ['1 pechuga de pollo', '1 kg de papa criolla', '500 g de papa pastusa', '3 mazorcas', 'Guascas', 'Crema de leche', 'Alcaparras'], ['Cocina el pollo con las mazorcas.', 'Agrega las papas y las guascas y cocina hasta espesar.', 'Desmecha el pollo y sirve con crema y alcaparras.']],
            ['Ensalada César', 'Cena', 'Fácil', 15, 2, '🥗', ['1 lechuga romana', '1 pechuga de pollo a la plancha', 'Crutones', 'Queso parmesano', 'Salsa César'], ['Lava y corta la lechuga.', 'Corta el pollo en tiras.', 'Mezcla todo con la salsa y el queso.']],
            ['Postre de natas', 'Postre', 'Media', 60, 6, '🍮', ['1 litro de leche entera', '4 yemas de huevo', '1 taza de azúcar', 'Uvas pasas', 'Canela'], ['Hierve la leche y retira las natas.', 'Mezcla las natas con las yemas y el azúcar.', 'Cocina a fuego lento con canela y pasas hasta espesar.']],
            ['Limonada de coco', 'Bebida', 'Fácil', 10, 4, '🥥', ['4 limones', '1 taza de leche de coco', '1/2 taza de azúcar', 'Hielo'], ['Exprime los limones.', 'Licúa todo con el hielo hasta que esté cremoso.']],
            ['Pancakes de banano', 'Desayuno', 'Fácil', 15, 2, '🥞', ['2 bananos maduros', '2 huevos', '1/2 taza de avena', 'Canela', 'Miel'], ['Tritura los bananos y mezcla con los huevos y la avena.', 'Cocina porciones pequeñas en un sartén antiadherente.', 'Sirve con miel y canela.']],
            ['Patacones con hogao', 'Snack', 'Fácil', 25, 4, '🍌', ['2 plátanos verdes', '2 tomates', '1 cebolla larga', 'Aceite', 'Sal'], ['Pela y corta los plátanos, fríelos y aplástalos.', 'Fríelos de nuevo hasta dorar.', 'Prepara el hogao sofriendo tomate y cebolla.', 'Sirve los patacones con el hogao encima.']],
        ];
        foreach ($seed as [$name, $type, $diff, $time, $serv, $emoji, $ings, $steps]) {
            $this->create(['name' => $name, 'type' => $type, 'difficulty' => $diff, 'time' => $time, 'servings' => $serv,
                'emoji' => $emoji, 'ingredients' => $ings, 'steps' => $steps, 'author' => 'Chef ITM']);
        }
    }
}
