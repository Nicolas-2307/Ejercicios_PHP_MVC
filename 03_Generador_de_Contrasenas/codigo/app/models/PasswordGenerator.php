<?php
/**
 * Modelo PasswordGenerator: genera contraseñas aleatorias seguras usando
 * random_int() (generador criptográficamente seguro de PHP) y evalúa su fortaleza.
 */
class PasswordGenerator
{
    public const MIN = 4;
    public const MAX = 64;

    private const SETS = [
        'upper'   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'lower'   => 'abcdefghijklmnopqrstuvwxyz',
        'digits'  => '0123456789',
        'symbols' => '!@#$%&*()-_=+[]{};:,.?/',
    ];
    private const AMBIGUOUS = 'O0oIl1|';

    /**
     * Genera una contraseña.
     * @param int   $length  Longitud deseada
     * @param array $types   Tipos de caracteres: upper, lower, digits, symbols
     * @param bool  $noAmbiguous Excluir caracteres que se confunden (O/0, l/1...)
     */
    public function generate(int $length, array $types, bool $noAmbiguous = false): string
    {
        $length = max(self::MIN, min(self::MAX, $length));
        $sets = [];
        foreach ($types as $t) {
            if (isset(self::SETS[$t])) {
                $chars = self::SETS[$t];
                if ($noAmbiguous) {
                    $chars = str_replace(str_split(self::AMBIGUOUS), '', $chars);
                }
                $sets[] = $chars;
            }
        }
        if (!$sets) {
            throw new InvalidArgumentException('Selecciona al menos un tipo de carácter.');
        }

        // Garantiza al menos un carácter de cada tipo seleccionado
        $password = [];
        foreach ($sets as $set) {
            $password[] = $set[random_int(0, strlen($set) - 1)];
        }
        $all = implode('', $sets);
        while (count($password) < $length) {
            $password[] = $all[random_int(0, strlen($all) - 1)];
        }
        // Mezcla con Fisher-Yates usando random_int
        for ($i = count($password) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$password[$i], $password[$j]] = [$password[$j], $password[$i]];
        }
        return implode('', array_slice($password, 0, $length));
    }

    /** Calcula la entropía (bits) y una etiqueta de fortaleza */
    public function strength(string $password): array
    {
        $pool = 0;
        if (preg_match('/[A-Z]/', $password)) $pool += 26;
        if (preg_match('/[a-z]/', $password)) $pool += 26;
        if (preg_match('/[0-9]/', $password)) $pool += 10;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $pool += 23;
        $bits = $pool ? round(strlen($password) * log($pool, 2)) : 0;

        if ($bits < 40)      [$label, $level] = ['Débil', 1];
        elseif ($bits < 60)  [$label, $level] = ['Aceptable', 2];
        elseif ($bits < 80)  [$label, $level] = ['Fuerte', 3];
        else                 [$label, $level] = ['Muy fuerte', 4];

        return ['bits' => $bits, 'label' => $label, 'level' => $level];
    }
}
