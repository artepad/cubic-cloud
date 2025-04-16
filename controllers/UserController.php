<?php

/**
 * UserController
 * 
 * Controlador para gestión de usuarios regulares del sistema,
 * incluyendo autenticación, dashboard y funcionalidades básicas.
 */
class UserController
{
    private $userModel;
    private $empresaModel;

    /**
     * Constructor
     * Inicializa los modelos necesarios y verifica autenticación
     */
    public function __construct()
    {
        // Cargar los modelos
        $this->userModel = new User();
        $this->empresaModel = class_exists('Empresa') ? new Empresa() : null;

        // Verificar autenticación para acciones protegidas
        $publicMethods = ['index', 'login', 'validate', 'recover', 'requestReset', 'reset', 'doReset'];
        $currentMethod = isset($_GET['action']) ? $_GET['action'] : 'index';

        if (!in_array($currentMethod, $publicMethods) && !isUserLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere iniciar sesión.";
            header("Location:" . base_url . "user/login");
            exit();
        }
    }

    /**
     * Acción por defecto del controlador
     * Redirige al dashboard si hay sesión, o muestra la pantalla de login
     */
    public function index()
    {
        // Verificar si hay sesión activa de usuario
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        // Si no hay sesión, mostrar login
        $this->login();
    }

    /**
     * Dashboard principal del usuario
     * Muestra estadísticas y resumen general
     */
    public function dashboard()
    {
        // Configurar el título de la página
        $pageTitle = "Dashboard de Usuario";

        // Cargar datos necesarios para el dashboard
        $user = $_SESSION['user'];
        $ultimo_login = $user->ultimo_login ? date('d/m/Y H:i', strtotime($user->ultimo_login)) : 'Este es tu primer acceso';

        // Incluir la vista
        require_once 'views/user/dashboard/index.php';
    }

    /**
     * Muestra la pantalla de login de usuario
     */
    public function login()
    {
        // Si ya está logueado como usuario, redirigir al dashboard
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        // Incluir directamente la vista de login sin layouts
        require_once 'views/user/login/login.php';
    }

    /**
     * Muestra y edita el perfil del usuario
     */
    public function profile()
    {
        // Configurar el título de la página
        $pageTitle = "Mi Perfil";

        // Obtener datos del usuario actual
        $user = $_SESSION['user'];

        // Incluir la vista
        require_once 'views/user/dashboard/profile.php';
    }

    /**
     * Establece un mensaje de error para el login
     * @param string $message Mensaje de error
     */
    private function setLoginError($message)
    {
        $_SESSION['error_login'] = $message;
    }


    /**
     * Elimina la cookie "Recuérdame"
     */
    private function deleteRememberMeCookie()
    {
        setcookie('user_remember', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}
