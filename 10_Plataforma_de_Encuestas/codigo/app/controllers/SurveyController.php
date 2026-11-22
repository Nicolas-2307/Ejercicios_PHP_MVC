<?php
/**
 * SurveyController: crear encuestas, responderlas, ver resultados
 * con gráficos y exportar el informe (CSV / impresión PDF).
 */
class SurveyController extends Controller
{
    private Survey $surveys;
    private Response $responses;

    public function __construct()
    {
        $this->surveys = new Survey();
        $this->responses = new Response();
    }

    public function index(): void
    {
        $list = $this->surveys->all();
        foreach ($list as &$s) {
            $s['count'] = $this->responses->count($s['id']);
        }
        $this->render('surveys/index', ['title' => 'Encuestas', 'surveys' => $list]);
    }

    public function create(): void
    {
        $this->render('surveys/create', ['title' => 'Crear encuesta', 'old' => $_SESSION['old'] ?? null]);
        unset($_SESSION['old']);
    }

    public function store(): void
    {
        [$errors, $survey] = $this->surveys->build(
            $this->input('title'), $this->input('description'), $this->input('author'),
            (array)$this->input('questions', []), (array)$this->input('options', [])
        );
        if ($errors) {
            $_SESSION['old'] = $_POST;
            $this->flash(implode(' ', $errors), 'err');
            $this->redirect('create');
        }
        $s = $this->surveys->create($survey);
        $this->flash('Encuesta creada. ¡Comparte el enlace para recibir respuestas!');
        $this->redirect('answer', ['id' => $s['id']]);
    }

    private function getSurvey(): array
    {
        $s = $this->surveys->find((int)($_REQUEST['id'] ?? 0));
        if (!$s) $this->redirect();
        return $s;
    }

    public function answer(): void
    {
        $s = $this->getSurvey();
        $this->render('surveys/answer', ['title' => $s['title'], 's' => $s, 'count' => $this->responses->count($s['id'])]);
    }

    public function submit(): void
    {
        $s = $this->getSurvey();
        $answers = (array)$this->input('answers', []);
        $errors = $this->responses->validate($s, $answers);
        if ($errors) {
            $this->flash(implode(' ', $errors), 'err');
            $this->redirect('answer', ['id' => $s['id']]);
        }
        $this->responses->create($s['id'], $answers);
        $this->flash('¡Gracias! Tu respuesta fue registrada.');
        $this->redirect('results', ['id' => $s['id']]);
    }

    public function results(): void
    {
        $s = $this->getSurvey();
        $this->render('surveys/results', ['title' => 'Resultados', 's' => $s, 'a' => $this->responses->analyze($s)]);
    }

    /** Exporta el informe en CSV */
    public function export(): void
    {
        $s = $this->getSurvey();
        $a = $this->responses->analyze($s);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="informe_encuesta_' . $s['id'] . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM para Excel
        fputcsv($out, ['Encuesta', $s['title']], ';');
        fputcsv($out, ['Total respuestas', $a['total']], ';');
        fputcsv($out, [], ';');
        fputcsv($out, ['Pregunta', 'Opción', 'Votos', 'Porcentaje'], ';');
        foreach ($a['questions'] as $q) {
            foreach ($q['rows'] as $r) {
                fputcsv($out, [$q['text'], $r['option'], $r['votes'], $r['pct'] . '%'], ';');
            }
        }
        fclose($out);
    }

    public function delete(): void
    {
        $this->surveys->delete((int)$this->input('id'));
        $this->flash('Encuesta eliminada.');
        $this->redirect();
    }
}
