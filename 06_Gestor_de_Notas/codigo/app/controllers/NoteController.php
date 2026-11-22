<?php
/**
 * NoteController: CRUD de notas, búsqueda y filtro por categoría.
 */
class NoteController extends Controller
{
    private Note $model;

    public function __construct()
    {
        $this->model = new Note();
    }

    public function index(): void
    {
        $q   = trim($_GET['q'] ?? '');
        $cat = $_GET['category'] ?? '';
        $this->render('notes/index', [
            'title'  => 'Mis notas',
            'notes'  => $this->model->search($q, $cat),
            'counts' => $this->model->countByCategory(),
            'q'      => $q,
            'cat'    => $cat,
        ]);
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(): void
    {
        $note = $this->model->find((int)($_GET['id'] ?? 0));
        if (!$note) $this->redirect();
        $this->form($note);
    }

    private function form(?array $note): void
    {
        $this->render('notes/form', [
            'title'      => $note ? 'Editar nota' : 'Nueva nota',
            'note'       => $_SESSION['old'] ?? $note ?? ['title' => '', 'content' => '', 'category' => 'Personal', 'color' => Note::COLORS[0]],
            'id'         => $note['id'] ?? null,
            'categories' => Note::CATEGORIES,
            'colors'     => Note::COLORS,
        ]);
        unset($_SESSION['old']);
    }

    public function save(): void
    {
        $id = (int)$this->input('id') ?: null;
        $data = [
            'title'    => $this->input('title'),
            'content'  => $this->input('content'),
            'category' => $this->input('category'),
            'color'    => $this->input('color'),
        ];
        $errors = $this->model->validate($data);
        if ($errors) {
            $_SESSION['old'] = $data;
            $this->flash(implode(' ', $errors), 'err');
            $id ? $this->redirect('edit', ['id' => $id]) : $this->redirect('create');
        }
        $this->model->save($data, $id);
        $this->flash($id ? 'Nota actualizada.' : 'Nota guardada.');
        $this->redirect();
    }

    public function pin(): void
    {
        $this->model->togglePin((int)$this->input('id'));
        $this->redirect();
    }

    public function delete(): void
    {
        $this->model->delete((int)$this->input('id'));
        $this->flash('Nota eliminada.');
        $this->redirect();
    }
}
