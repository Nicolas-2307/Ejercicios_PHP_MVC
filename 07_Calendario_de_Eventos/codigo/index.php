<?php
/**
 * Front Controller (punto de entrada único de la aplicación)
 * Todas las peticiones llegan aquí: index.php?action=nombreAccion
 * El router carga el controlador y ejecuta el método solicitado.
 */
session_start();
date_default_timezone_set('America/Bogota');

// Carga automática de clases (core, modelos y controladores)
spl_autoload_register(function ($class) {
    foreach (['core', 'app/models', 'app/controllers'] as $dir) {
        $file = __DIR__ . "/$dir/$class.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
require_once __DIR__ . '/core/Controller.php';

$config     = require __DIR__ . '/config.php';
$controller = new $config['controller']();
$action     = $_GET['action'] ?? 'index';

// Solo se permiten métodos públicos definidos en el controlador
if (!is_callable([$controller, $action]) || str_starts_with($action, '_')) {
    $action = 'index';
}
$controller->$action();
