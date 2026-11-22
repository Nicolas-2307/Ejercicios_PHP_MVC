<?php
/**
 * StopwatchController: muestra el cronómetro, guarda las sesiones
 * (tiempo total y vueltas) y lista el historial.
 */
class StopwatchController extends Controller
{
    private Session $model;

    public function __construct()
    {
        $this->model = new Session();
    }

    public function index(): void
    {
        $this->render('stopwatch/index', ['title' => 'Cronómetro', 'recent' => array_slice($this->model->all(), 0, 3)]);
    }

    /** POST AJAX: guarda la sesión actual */
    public function save(): void
    {
        $body  = json_decode(file_get_contents('php://input'), true) ?? [];
        $name  = trim($body['name'] ?? '');
        $total = (int)($body['total'] ?? 0);
        $laps  = (array)($body['laps'] ?? []);
        $errors = $this->model->validate($name, $total, $laps);
        if ($errors) {
            http_response_code(422);
            $this->json(['errors' => $errors]);
            return;
        }
        $s = $this->model->create($name, $total, $laps);
        $this->json(['ok' => true, 'id' => $s['id'], 'name' => $s['name']]);
    }

    public function history(): void
    {
        $this->render('stopwatch/history', ['title' => 'Historial', 'sessions' => $this->model->all()]);
    }

    public function delete(): void
    {
        $this->model->delete((int)$this->input('id'));
        $this->flash('Sesión eliminada.');
        $this->redirect('history');
    }
}
