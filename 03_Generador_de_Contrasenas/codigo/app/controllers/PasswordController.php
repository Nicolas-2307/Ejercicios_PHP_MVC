<?php
/**
 * PasswordController: lee las opciones del formulario, solicita al modelo
 * la(s) contraseña(s) y las envía a la vista.
 */
class PasswordController extends Controller
{
    private PasswordGenerator $model;

    public function __construct()
    {
        $this->model = new PasswordGenerator();
    }

    public function index(): void
    {
        $options = ['length' => 16, 'types' => ['upper', 'lower', 'digits', 'symbols'], 'noAmbiguous' => false, 'count' => 5];
        $passwords = [];
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $options['length']      = (int)$this->input('length', 16);
            $options['types']       = (array)$this->input('types', []);
            $options['noAmbiguous'] = isset($_POST['noAmbiguous']);
            $options['count']       = max(1, min(10, (int)$this->input('count', 5)));
            try {
                for ($i = 0; $i < $options['count']; $i++) {
                    $pwd = $this->model->generate($options['length'], $options['types'], $options['noAmbiguous']);
                    $passwords[] = ['value' => $pwd, 'strength' => $this->model->strength($pwd)];
                }
            } catch (InvalidArgumentException $ex) {
                $error = $ex->getMessage();
            }
        }

        $this->render('passwords/index', [
            'title'     => 'Generar contraseñas',
            'options'   => $options,
            'passwords' => $passwords,
            'error'     => $error,
        ]);
    }
}
