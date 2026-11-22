<?php
/**
 * Controller
 * Clase base de la que heredan todos los controladores.
 * Se encarga de renderizar vistas dentro del layout y de redirigir.
 */
abstract class Controller
{
    /**
     * Renderiza una vista dentro del layout principal.
     * @param string $view  Ruta de la vista relativa a app/views (sin .php)
     * @param array  $data  Variables disponibles en la vista
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        ob_start();
        require dirname(__DIR__) . '/app/views/' . $view . '.php';
        $content = ob_get_clean();
        require dirname(__DIR__) . '/app/views/layout.php';
    }

    /** Responde en formato JSON (útil para peticiones AJAX) */
    protected function json($data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /** Redirige a una acción del controlador */
    protected function redirect(string $action = 'index', array $params = []): void
    {
        $query = http_build_query(array_merge(['action' => $action], $params));
        header('Location: index.php?' . $query);
        exit;
    }

    /** Guarda un mensaje "flash" que se muestra una sola vez */
    protected function flash(string $msg, string $type = 'ok'): void
    {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
    }

    /** Lee un valor de $_POST de forma segura */
    protected function input(string $key, $default = '')
    {
        return isset($_POST[$key]) ? (is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key]) : $default;
    }
}

/** Escapa texto para imprimirlo en HTML (previene XSS) */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/** Formatea un valor como moneda colombiana */
function money($value): string
{
    return '$' . number_format((float)$value, 0, ',', '.');
}
