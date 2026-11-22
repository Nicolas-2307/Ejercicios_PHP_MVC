<?php
/**
 * ExpenseController: registro, listado, eliminación y resumen mensual de gastos.
 */
class ExpenseController extends Controller
{
    private Expense $model;

    public function __construct()
    {
        $this->model = new Expense();
    }

    private function month(): string
    {
        $m = $_GET['month'] ?? date('Y-m');
        return preg_match('/^\d{4}-\d{2}$/', $m) ? $m : date('Y-m');
    }

    public function index(): void
    {
        $month = $this->month();
        $cat = $_GET['category'] ?? '';
        $rows = $this->model->byMonth($month, $cat);
        $this->render('expenses/index', [
            'title'      => 'Registro de gastos',
            'rows'       => $rows,
            'month'      => $month,
            'cat'        => $cat,
            'total'      => array_sum(array_column($rows, 'amount')),
            'categories' => Expense::CATEGORIES,
            'old'        => $_SESSION['old'] ?? [],
        ]);
        unset($_SESSION['old']);
    }

    public function store(): void
    {
        $data = [
            'date'        => $this->input('date'),
            'description' => $this->input('description'),
            'category'    => $this->input('category'),
            'amount'      => $this->input('amount'),
        ];
        $errors = $this->model->validate($data);
        if ($errors) {
            $_SESSION['old'] = $data;
            $this->flash(implode(' ', $errors), 'err');
        } else {
            $this->model->create($data);
            $this->flash('Gasto registrado: ' . $data['description'] . ' por ' . money($data['amount']));
        }
        $this->redirect('index', ['month' => substr($data['date'], 0, 7) ?: date('Y-m')]);
    }

    public function delete(): void
    {
        $this->model->delete((int)$this->input('id'));
        $this->flash('Gasto eliminado.');
        $this->redirect('index', ['month' => $this->input('month', date('Y-m'))]);
    }

    public function summary(): void
    {
        $month = $this->month();
        $this->render('expenses/summary', [
            'title'      => 'Resumen mensual',
            'month'      => $month,
            'summary'    => $this->model->summary($month),
            'monthly'    => $this->model->monthlyTotals(),
            'categories' => Expense::CATEGORIES,
        ]);
    }
}
