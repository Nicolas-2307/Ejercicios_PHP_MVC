<?php
/**
 * Modelo Response: respuestas de los usuarios a una encuesta.
 * Estructura: id, survey_id, answers: [indiceOpcion por pregunta], date
 */
class Response
{
    private JsonStore $db;

    public function __construct()
    {
        $this->db = new JsonStore('responses');
    }

    public function validate(array $survey, array $answers): array
    {
        $errors = [];
        foreach ($survey['questions'] as $i => $q) {
            if (!isset($answers[$i]) || !isset($q['options'][(int)$answers[$i]])) {
                $errors[] = 'Responde la pregunta: “' . $q['text'] . '”.';
            }
        }
        return $errors;
    }

    public function create(int $surveyId, array $answers): void
    {
        $this->db->insert(['survey_id' => $surveyId, 'answers' => array_map('intval', $answers), 'date' => date('Y-m-d H:i')]);
    }

    public function bySurvey(int $surveyId): array
    {
        return array_values(array_filter($this->db->all(), fn($r) => $r['survey_id'] === $surveyId));
    }

    public function count(int $surveyId): int
    {
        return count($this->bySurvey($surveyId));
    }

    /**
     * Analiza los resultados: para cada pregunta devuelve votos y porcentaje
     * por opción, la opción ganadora y el total de respuestas.
     */
    public function analyze(array $survey): array
    {
        $responses = $this->bySurvey($survey['id']);
        $total = count($responses);
        $result = [];
        foreach ($survey['questions'] as $qi => $q) {
            $votes = array_fill(0, count($q['options']), 0);
            foreach ($responses as $r) {
                if (isset($r['answers'][$qi], $votes[$r['answers'][$qi]])) $votes[$r['answers'][$qi]]++;
            }
            $rows = [];
            foreach ($q['options'] as $oi => $opt) {
                $rows[] = ['option' => $opt, 'votes' => $votes[$oi], 'pct' => $total ? round($votes[$oi] * 100 / $total, 1) : 0];
            }
            $max = max($votes);
            $result[] = ['text' => $q['text'], 'rows' => $rows,
                'winner' => $total && $max ? $q['options'][array_search($max, $votes)] : null];
        }
        return ['total' => $total, 'questions' => $result,
            'last' => $responses ? end($responses)['date'] : null];
    }

    public function deleteBySurvey(int $surveyId): void
    {
        $this->db->save(array_filter($this->db->all(), fn($r) => $r['survey_id'] !== $surveyId));
    }
}
