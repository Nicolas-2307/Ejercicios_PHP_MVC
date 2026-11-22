<?php
/**
 * EventController: vista mensual del calendario, CRUD de eventos,
 * lista de próximos eventos y API de recordatorios (para notificaciones).
 */
class EventController extends Controller
{
    private Event $model;

    public function __construct()
    {
        $this->model = new Event();
    }

    public function index(): void
    {
        $year  = (int)($_GET['y'] ?? date('Y'));
        $month = (int)($_GET['m'] ?? date('n'));
        if ($month < 1)  { $month = 12; $year--; }
        if ($month > 12) { $month = 1;  $year++; }

        $this->render('events/calendar', [
            'title'      => 'Calendario',
            'year'       => $year,
            'month'      => $month,
            'events'     => $this->model->byMonth($year, $month),
            'reminders'  => $this->model->activeReminders(),
            'categories' => Event::CATEGORIES,
        ]);
    }

    public function create(): void
    {
        $this->form(null, ['date' => $_GET['date'] ?? date('Y-m-d')]);
    }

    public function edit(): void
    {
        $ev = $this->model->find((int)($_GET['id'] ?? 0));
        if (!$ev) $this->redirect();
        $this->form($ev);
    }

    private function form(?array $ev, array $defaults = []): void
    {
        $base = ['title' => '', 'description' => '', 'date' => date('Y-m-d'), 'time' => '08:00', 'category' => 'academico', 'reminder' => 30];
        $this->render('events/form', [
            'title'      => $ev ? 'Editar evento' : 'Nuevo evento',
            'ev'         => $_SESSION['old'] ?? $ev ?? array_merge($base, $defaults),
            'id'         => $ev['id'] ?? null,
            'categories' => Event::CATEGORIES,
            'reminders'  => Event::REMINDERS,
        ]);
        unset($_SESSION['old']);
    }

    public function save(): void
    {
        $id = (int)$this->input('id') ?: null;
        $data = [
            'title'       => $this->input('title'),
            'description' => $this->input('description'),
            'date'        => $this->input('date'),
            'time'        => $this->input('time'),
            'category'    => $this->input('category'),
            'reminder'    => (int)$this->input('reminder'),
        ];
        $errors = $this->model->validate($data);
        if ($errors) {
            $_SESSION['old'] = $data;
            $this->flash(implode(' ', $errors), 'err');
            $id ? $this->redirect('edit', ['id' => $id]) : $this->redirect('create');
        }
        $this->model->save($data, $id);
        $this->flash($id ? 'Evento actualizado.' : 'Evento creado.');
        [$y, $m] = explode('-', $data['date']);
        $this->redirect('index', ['y' => (int)$y, 'm' => (int)$m]);
    }

    public function delete(): void
    {
        $this->model->delete((int)$this->input('id'));
        $this->flash('Evento eliminado.');
        $this->redirect();
    }

    public function upcoming(): void
    {
        $this->render('events/upcoming', [
            'title' => 'Próximos eventos', 'rows' => $this->model->upcoming(), 'categories' => Event::CATEGORIES,
        ]);
    }

    /** API JSON consultada por JavaScript para mostrar notificaciones */
    public function reminders(): void
    {
        $this->json($this->model->activeReminders());
    }
}
