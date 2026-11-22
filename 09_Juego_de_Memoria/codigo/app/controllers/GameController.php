<?php
/**
 * GameController: selección de nivel, partida y registro de puntajes.
 */
class GameController extends Controller
{
    private MemoryGame $model;

    public function __construct()
    {
        $this->model = new MemoryGame();
    }

    public function index(): void
    {
        $this->render('game/index', [
            'title'  => 'Elige un nivel',
            'levels' => MemoryGame::LEVELS,
            'player' => $_SESSION['player'] ?? '',
        ]);
    }

    public function play(): void
    {
        $level = $_REQUEST['level'] ?? 'facil';
        if (!isset(MemoryGame::LEVELS[$level])) $level = 'facil';
        if (isset($_POST['player'])) {
            $_SESSION['player'] = mb_substr(trim($_POST['player']), 0, 20);
        }
        $this->render('game/play', [
            'title'  => 'Jugando',
            'level'  => $level,
            'cfg'    => MemoryGame::LEVELS[$level],
            'cards'  => $this->model->board($level),
            'player' => $_SESSION['player'] ?? 'Anónimo',
            'top'    => $this->model->top($level, 5),
        ]);
    }

    /** POST AJAX al terminar la partida */
    public function saveScore(): void
    {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $level = $body['level'] ?? '';
        if (!isset(MemoryGame::LEVELS[$level])) {
            http_response_code(422);
            $this->json(['error' => 'Nivel no válido']);
            return;
        }
        $this->json($this->model->saveScore($_SESSION['player'] ?? 'Anónimo', $level, (int)($body['moves'] ?? 0), (int)($body['seconds'] ?? 0)));
    }

    public function scores(): void
    {
        $tops = [];
        foreach (MemoryGame::LEVELS as $k => $l) {
            $tops[$k] = $this->model->top($k);
        }
        $this->render('game/scores', ['title' => 'Mejores puntajes', 'tops' => $tops, 'levels' => MemoryGame::LEVELS]);
    }
}
