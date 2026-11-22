<?php
/**
 * TaskController: recibe las acciones del usuario sobre la lista de tareas,
 * llama al modelo Task y decide qué vista mostrar.
 */
class TaskController extends Controller
{
    private Task $model;

    public function __construct()
    {
        $this->model = new Task();
    }

    /** GET index.php?action=index&filter=all|pending|done */
    public function index(): void
    {
        $filter = $_GET['filter'] ?? 'all';
        if (!in_array($filter, ['all', 'pending', 'done'], true)) {
            $filter = 'all';
        }
        $this->render('tasks/index', [
            'title'  => 'Mis tareas',
            'tasks'  => $this->model->all($filter),
            'stats'  => $this->model->stats(),
            'filter' => $filter,
        ]);
    }

    /** POST: agregar tarea */
    public function store(): void
    {
        $error = $this->model->create($this->input('title'), $this->input('priority', 'media'));
        $error ? $this->flash($error, 'err') : $this->flash('Tarea agregada correctamente.');
        $this->redirect();
    }

    /** POST: marcar como completada / pendiente */
    public function toggle(): void
    {
        $this->model->toggle((int)$this->input('id'));
        $this->redirect('index', ['filter' => $this->input('filter', 'all')]);
    }

    /** POST: eliminar tarea */
    public function delete(): void
    {
        $this->model->delete((int)$this->input('id'));
        $this->flash('Tarea eliminada.');
        $this->redirect('index', ['filter' => $this->input('filter', 'all')]);
    }

    /** POST: eliminar todas las completadas */
    public function clear(): void
    {
        $this->model->clearCompleted();
        $this->flash('Se eliminaron las tareas completadas.');
        $this->redirect();
    }
}
