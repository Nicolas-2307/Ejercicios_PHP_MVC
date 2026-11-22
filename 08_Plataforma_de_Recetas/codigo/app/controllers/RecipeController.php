<?php
/**
 * RecipeController: explorar/buscar/filtrar recetas, ver detalle,
 * guardar en favoritos (por sesión de usuario) y compartir recetas nuevas.
 */
class RecipeController extends Controller
{
    private Recipe $model;

    public function __construct()
    {
        $this->model = new Recipe();
        $_SESSION['favorites'] = $_SESSION['favorites'] ?? [];
    }

    public function index(): void
    {
        $f = [
            'q'          => trim($_GET['q'] ?? ''),
            'type'       => $_GET['type'] ?? '',
            'ingredient' => trim($_GET['ingredient'] ?? ''),
            'maxTime'    => (int)($_GET['maxTime'] ?? 0),
            'difficulty' => $_GET['difficulty'] ?? '',
        ];
        $this->render('recipes/index', [
            'title'     => 'Explorar recetas',
            'recipes'   => $this->model->search($f['q'], $f['type'], $f['ingredient'], $f['maxTime'], $f['difficulty']),
            'f'         => $f,
            'favorites' => $_SESSION['favorites'],
            'heading'   => 'Explorar recetas',
        ]);
    }

    public function show(): void
    {
        $r = $this->model->find((int)($_GET['id'] ?? 0));
        if (!$r) $this->redirect();
        $this->render('recipes/show', [
            'title'    => $r['name'],
            'r'        => $r,
            'isFav'    => in_array($r['id'], $_SESSION['favorites'], true),
            'shareUrl' => 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . strtok($_SERVER['REQUEST_URI'], '?') . '?action=show&id=' . $r['id'],
        ]);
    }

    /** Guardar / quitar de favoritos */
    public function toggleFav(): void
    {
        $id = (int)$this->input('id');
        $favs = &$_SESSION['favorites'];
        if (in_array($id, $favs, true)) {
            $favs = array_values(array_diff($favs, [$id]));
            $this->model->changeSaves($id, -1);
            $this->flash('Receta quitada de tus guardadas.', 'warn');
        } else {
            $favs[] = $id;
            $this->model->changeSaves($id, 1);
            $this->flash('Receta guardada en “Mis guardadas”.');
        }
        $back = $this->input('back', 'index');
        $back === 'show' ? $this->redirect('show', ['id' => $id]) : $this->redirect($back);
    }

    public function favorites(): void
    {
        $all = $this->model->search();
        $favs = array_filter($all, fn($r) => in_array($r['id'], $_SESSION['favorites'], true));
        $this->render('recipes/index', [
            'title'     => 'Mis guardadas',
            'recipes'   => $favs,
            'f'         => null,
            'favorites' => $_SESSION['favorites'],
            'heading'   => '❤ Mis recetas guardadas',
        ]);
    }

    public function create(): void
    {
        $this->render('recipes/form', [
            'title' => 'Compartir receta',
            'old'   => $_SESSION['old'] ?? ['name' => '', 'type' => 'Almuerzo', 'difficulty' => 'Fácil', 'time' => 30, 'servings' => 2, 'emoji' => '🍽️', 'ingredients' => '', 'steps' => '', 'author' => ''],
        ]);
        unset($_SESSION['old']);
    }

    public function store(): void
    {
        $raw = [
            'name' => $this->input('name'), 'type' => $this->input('type'), 'difficulty' => $this->input('difficulty'),
            'time' => (int)$this->input('time'), 'servings' => max(1, (int)$this->input('servings')),
            'emoji' => $this->input('emoji') ?: '🍽️', 'ingredients' => $this->input('ingredients'),
            'steps' => $this->input('steps'), 'author' => $this->input('author'),
        ];
        $data = $raw;
        $data['ingredients'] = Recipe::lines($raw['ingredients']);
        $data['steps'] = Recipe::lines($raw['steps']);
        $errors = $this->model->validate($data);
        if ($errors) {
            $_SESSION['old'] = $raw;
            $this->flash(implode(' ', $errors), 'err');
            $this->redirect('create');
        }
        $r = $this->model->create($data);
        $this->flash('¡Gracias por compartir tu receta! Ya está publicada.');
        $this->redirect('show', ['id' => $r['id']]);
    }
}
