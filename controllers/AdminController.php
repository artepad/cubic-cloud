<?php

/**
 * AdminController
 * 
 * Controlador para la gestión de sesiones de administradores del sistema,
 * incluyendo login, logout y recuperación de contraseñas.
 */
class AdminController
{
    private $adminModel;

    /**
     * Constructor
     * Inicializa el modelo de administrador
     */
    public function __construct()
    {
        // Cargar el modelo
        $this->adminModel = new SystemAdmin();
    }

    /**
     * Acción por defecto del controlador
     * Redirige al dashboard si hay sesión, o muestra la pantalla de login
     */
    public function index()
    {
        // Verificar si hay sesión activa de administrador
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        // Si no hay sesión, mostrar login
        $this->login();
    }

    /**
     * Muestra la pantalla de login de administrador
     */
    public function login()
    {
        // Si ya está logueado como admin, redirigir al dashboard
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        // Incluir directamente la vista de login sin layouts
        require_once 'views/admin/login/login.php';
    }

    /**
     * Valida las credenciales del administrador
     * Procesa el formulario de login
     */
    public function validate()
    {
        // No permitir acceso a esta acción si ya está logueado
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        if (isset($_POST['email']) && isset($_POST['password'])) {
            // Validar los campos obligatorios
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $this->setLoginError("Todos los campos son obligatorios");
                header("Location: " . base_url . "admin/login");
                exit();
            }

            // Obtener y sanitizar datos del formulario
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // No sanitizar contraseña para no alterarla
            $remember = isset($_POST['remember']) ? true : false;

            // Verificar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "admin/login");
                exit();
            }

            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "admin/login");
                    exit();
                }
            }

            // Usar el modelo para verificar las credenciales
            $admin = $this->adminModel->login($email, $password);

            if ($admin) {
                // Crear sesión con los datos del admin
                $_SESSION['admin'] = $admin;

                // Regenerar ID de sesión para prevenir session fixation
                regenerateSession();

                // Si se seleccionó "Recuérdame", crear token y cookie
                if ($remember) {
                    $this->createRememberMeCookie($admin->id);
                }

                // Redirigir al dashboard - MODIFICADO PARA USAR systemDashboard
                header("Location: " . base_url . "systemDashboard/welcome");
                exit();
            } else {
                // Agregar un pequeño delay para prevenir timing attacks
                sleep(1);
                $this->setLoginError("Email o contraseña incorrectos");
                header("Location: " . base_url . "admin/login");
                exit();
            }
        } else {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Cierra la sesión del administrador
     */
    public function logout()
    {
        // Iniciar buffer de salida para evitar el envío prematuro de headers
        ob_start();

        // Verificar que el administrador está logueado
        if (isAdminLoggedIn()) {
            $admin_id = $_SESSION['admin']->id;

            // Registrar el cierre de sesión en la base de datos
            $this->adminModel->registerLogout($admin_id);

            // Limpiar el token de "Recuérdame" 
            $this->adminModel->clearRememberToken($admin_id);

            // Eliminar la cookie de "Recuérdame"
            if (isset($_COOKIE['admin_remember'])) {
                $this->deleteRememberMeCookie();
            }

            // Destruir la sesión de forma segura
            session_unset();
            session_destroy();
        }

        // Mostrar la vista de logout
        require_once 'views/admin/login/logout.php';
        
        // Limpia el buffer y envía el contenido
        ob_end_flush();
    }

    /**
     * Muestra el formulario para solicitar recuperación de contraseña
     */
    public function recover()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        // Incluir vista de recuperación
        require_once 'views/admin/login/recover.php';
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function requestReset()
    {
        // No permitir acceso a esta acción si ya está logueado
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // Sanitizar email
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "admin/login");
                exit();
            }

            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "admin/login");
                    exit();
                }
            }

            // Generar token de recuperación
            $token = $this->adminModel->generateRecoveryToken($email);

            if ($token) {
                // Intentar enviar el correo con el enlace
                $sent = $this->adminModel->sendRecoveryEmail($email, $token);

                if ($sent) {
                    $_SESSION['success_message'] = "Se ha enviado un correo con instrucciones para recuperar tu contraseña";
                } else {
                    $this->setLoginError("No se pudo enviar el correo. Por favor, contacta al administrador");
                }
            } else {
                // No revelar si el email existe o no (seguridad)
                $_SESSION['success_message'] = "Si el email existe en nuestro sistema, recibirás instrucciones para recuperar tu contraseña";
            }
        } else {
            $this->setLoginError("Por favor, introduce tu correo electrónico");
        }

        // Redireccionar al login
        header("Location: " . base_url . "admin/login");
        exit();
    }

    /**
     * Muestra el formulario para restablecer la contraseña
     */
    public function reset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        // Verificar que hay un token
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            $this->setLoginError("Enlace de recuperación inválido");
            header("Location: " . base_url . "admin/login");
            exit();
        }

        // Sanitizar token
        $token = htmlspecialchars(trim($_GET['token']));

        // Validar el token
        $admin = $this->adminModel->validateRecoveryToken($token);

        if (!$admin) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "admin/login");
            exit();
        }

        // Incluir vista para restablecer contraseña
        require_once 'views/admin/login/reset.php';
    }

    /**
     * Procesa el restablecimiento de contraseña
     */
    public function doReset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "systemDashboard/index");
            exit();
        }

        // Verificar campos obligatorios
        if (!isset($_POST['token']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "admin/login");
            exit();
        }

        // Sanitizar token
        $token = htmlspecialchars(trim($_POST['token']));
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Verificar token CSRF si está habilitado
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $this->setLoginError("Error de seguridad: token inválido");
                header("Location: " . base_url . "admin/reset?token=" . urlencode($token));
                exit();
            }
        }

        // Validar que las contraseñas coinciden
        if ($password !== $confirm_password) {
            $this->setLoginError("Las contraseñas no coinciden");
            header("Location: " . base_url . "admin/reset?token=" . urlencode($token));
            exit();
        }

        // Validar requisitos de contraseña
        if (!validatePasswordStrength($password)) {
            $this->setLoginError("La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una minúscula y un número");
            header("Location: " . base_url . "admin/reset?token=" . urlencode($token));
            exit();
        }

        // Validar el token
        $admin = $this->adminModel->validateRecoveryToken($token);

        if (!$admin) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "admin/login");
            exit();
        }

        // Actualizar la contraseña
        $result = $this->adminModel->updatePassword($admin->id, $password);

        if ($result) {
            $_SESSION['success_message'] = "Contraseña actualizada correctamente. Ya puedes iniciar sesión";
        } else {
            $this->setLoginError("Error al actualizar la contraseña. Inténtalo de nuevo");
        }

        header("Location: " . base_url . "admin/login");
        exit();
    }

    /**
     * Métodos auxiliares
     */

    /**
     * Establece un mensaje de error para el login
     * @param string $message Mensaje de error
     */
    private function setLoginError($message)
    {
        $_SESSION['error_login'] = $message;
    }

    /**
     * Crea una cookie "Recuérdame" segura
     * @param int $admin_id ID del administrador
     */
    private function createRememberMeCookie($admin_id)
    {
        $token = $this->adminModel->createRememberToken($admin_id, COOKIE_LIFETIME);
        if ($token) {
            // Crear cookie segura
            $secure = isset($_SERVER['HTTPS']); // true si es HTTPS
            $httponly = true; // Evita acceso mediante JavaScript
            setcookie('admin_remember', $token, [
                'expires' => time() + (86400 * COOKIE_LIFETIME),
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => 'Lax' // Previene CSRF
            ]);
        }
    }

    /**
     * Elimina la cookie "Recuérdame"
     */
    private function deleteRememberMeCookie()
    {
        setcookie('admin_remember', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}