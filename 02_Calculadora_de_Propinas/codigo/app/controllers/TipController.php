<?php
/**
 * TipController: recibe el monto, el porcentaje y las personas,
 * le pide al modelo el cálculo y muestra el resultado.
 */
class TipController extends Controller
{
    private TipCalculator $model;

    public function __construct()
    {
        $this->model = new TipCalculator();
    }

    public function index(): void
    {
        $input  = ['amount' => '', 'percent' => 15, 'custom' => '', 'people' => 1];
        $result = null;
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['amount']  = $this->input('amount');
            $input['percent'] = $this->input('percent', '15');
            $input['custom']  = $this->input('custom');
            $input['people']  = $this->input('people', '1');

            $percent = $input['percent'] === 'custom' ? (float)$input['custom'] : (float)$input['percent'];
            $amount  = (float)$input['amount'];
            $people  = (int)$input['people'];

            $errors = $this->model->validate($amount, $percent, $people);
            if (!$errors) {
                $result = $this->model->calculate($amount, $percent, $people);
                $this->model->saveHistory($result);
            }
        }

        $this->render('tips/index', [
            'title'       => 'Calcular propina',
            'input'       => $input,
            'result'      => $result,
            'errors'      => $errors,
            'percentages' => TipCalculator::PERCENTAGES,
        ]);
    }

    /** Endpoint AJAX para el cálculo automático mientras el usuario escribe */
    public function api(): void
    {
        $amount  = (float)($_GET['amount'] ?? 0);
        $percent = (float)($_GET['percent'] ?? 0);
        $people  = (int)($_GET['people'] ?? 1);
        $errors  = $this->model->validate($amount, $percent, $people);
        $this->json($errors ? ['errors' => $errors] : $this->model->calculate($amount, $percent, $people));
    }

    public function history(): void
    {
        $this->render('tips/history', ['title' => 'Historial', 'rows' => $this->model->history()]);
    }

    public function clear(): void
    {
        $this->model->clearHistory();
        $this->flash('Historial borrado.');
        $this->redirect('history');
    }
}
