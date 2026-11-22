<?php
/**
 * Modelo MemoryGame: niveles de dificultad, generación del tablero,
 * cálculo del puntaje y tabla de mejores puntajes.
 */
class MemoryGame
{
    public const LEVELS = [
        'facil'   => ['name' => 'Fácil',   'pairs' => 6,  'cols' => 4, 'time' => 60,  'mult' => 1],
        'medio'   => ['name' => 'Medio',   'pairs' => 8,  'cols' => 4, 'time' => 90,  'mult' => 2],
        'dificil' => ['name' => 'Difícil', 'pairs' => 12, 'cols' => 6, 'time' => 120, 'mult' => 3],
    ];
    private const IMAGES = ['🐶', '🐱', '🦊', '🐼', '🐸', '🦁', '🐵', '🐧', '🦄', '🐙', '🦋', '🐢', '🐝', '🦉', '🐬'];
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('scores');
    }

    /** Genera el tablero barajado para un nivel */
    public function board(string $level): array
    {
        $cfg = self::LEVELS[$level];
        $images = self::IMAGES;
        shuffle($images);
        $cards = array_merge(array_slice($images, 0, $cfg['pairs']), array_slice($images, 0, $cfg['pairs']));
        shuffle($cards);
        return $cards;
    }

    /**
     * Puntaje = (100 por pareja − 10 por movimiento extra − 2 por segundo
     *           + 10 por cada segundo por debajo del tiempo objetivo) × multiplicador del nivel
     */
    public function score(string $level, int $moves, int $seconds): int
    {
        $cfg = self::LEVELS[$level];
        $extraMoves = max(0, $moves - $cfg['pairs']);
        $timeBonus = max(0, $cfg['time'] - $seconds) * 10;
        $base = $cfg['pairs'] * 100 - $extraMoves * 10 - $seconds * 2 + $timeBonus;
        return max(0, $base) * $cfg['mult'];
    }

    public function saveScore(string $player, string $level, int $moves, int $seconds): array
    {
        $row = [
            'player'  => mb_substr($player ?: 'Anónimo', 0, 20),
            'level'   => $level,
            'moves'   => $moves,
            'seconds' => $seconds,
            'score'   => $this->score($level, $moves, $seconds),
            'date'    => date('Y-m-d H:i'),
        ];
        $saved = $this->db->insert($row);
        $saved['rank'] = array_search($saved['id'], array_column($this->top($level, 1000), 'id')) + 1;
        return $saved;
    }

    /** Mejores puntajes de un nivel */
    public function top(string $level, int $limit = 10): array
    {
        $rows = array_filter($this->db->all(), fn($r) => $r['level'] === $level);
        usort($rows, fn($a, $b) => [$b['score'], $a['seconds']] <=> [$a['score'], $b['seconds']]);
        return array_slice($rows, 0, $limit);
    }
}
