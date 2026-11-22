<?php
/**
 * ReservationController: flujo de reserva
 * 1) index: elegir servicio y fecha, ver disponibilidad
 * 2) store: crear reserva (pendiente)
 * 3) confirm: el usuario revisa y confirma la reserva
 * 4) list: ver y cancelar reservas
 */
class ReservationController extends Controller
{
    private Reservation $model;
    private Service $services;

    public function __construct()
    {
        $this->model = new Reservation();
        $this->services = new Service();
    }

    public function index(): void
    {
        $serviceId = (int)($_GET['service'] ?? 1);
        if (!$this->services->find($serviceId)) $serviceId = 1;
        $date = $_GET['date'] ?? date('Y-m-d', strtotime('+1 day'));
        if (!DateTime::createFromFormat('Y-m-d', $date)) $date = date('Y-m-d');

        // Próximos 7 días con la cantidad de cupos libres
        $week = [];
        for ($i = 0; $i < 7; $i++) {
            $d = date('Y-m-d', strtotime("+$i day"));
            $isSunday = date('N', strtotime($d)) == 7;
            $week[$d] = $isSunday ? 0 : count(array_filter($this->model->availability($serviceId, $d)));
        }

        $this->render('reservations/index', [
            'title'        => 'Reservar cita',
            'services'     => $this->services->all(),
            'serviceId'    => $serviceId,
            'date'         => $date,
            'isSunday'     => date('N', strtotime($date)) == 7,
            'availability' => $this->model->availability($serviceId, $date),
            'week'         => $week,
            'old'          => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['old']);
    }

    public function store(): void
    {
        $data = [
            'service_id' => (int)$this->input('service_id'),
            'date'       => $this->input('date'),
            'time'       => $this->input('time'),
            'name'       => $this->input('name'),
            'email'      => $this->input('email'),
            'phone'      => $this->input('phone'),
        ];
        $errors = $this->model->validate($data);
        if ($errors) {
            $_SESSION['old'] = $data;
            $this->flash(implode(' ', $errors), 'err');
            $this->redirect('index', ['service' => $data['service_id'], 'date' => $data['date']]);
        }
        $res = $this->model->create($data);
        $this->redirect('review', ['id' => $res['id']]);
    }

    /** Pantalla de revisión antes de confirmar */
    public function review(): void
    {
        $res = $this->model->find((int)($_GET['id'] ?? 0));
        if (!$res) $this->redirect();
        $this->render('reservations/review', [
            'title' => 'Confirmar reserva', 'r' => $res, 'service' => $this->services->find($res['service_id']),
        ]);
    }

    public function confirm(): void
    {
        $id = (int)$this->input('id');
        $this->model->setStatus($id, 'confirmada');
        $this->flash('¡Reserva confirmada! Te esperamos.');
        $this->redirect('review', ['id' => $id]);
    }

    public function cancel(): void
    {
        $this->model->setStatus((int)$this->input('id'), 'cancelada');
        $this->flash('Reserva cancelada. El horario quedó disponible nuevamente.', 'warn');
        $this->redirect('list');
    }

    public function list(): void
    {
        $this->render('reservations/list', [
            'title' => 'Mis reservas', 'rows' => $this->model->all(), 'services' => $this->services->all(),
        ]);
    }
}
