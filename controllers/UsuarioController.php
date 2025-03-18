<?php

/**
 * UsuarioController
 * 
 * Controlador para la gestión de sesiones de usuarios del sistema,
 * incluyendo login, logout, registro y recuperación de contraseñas.
 */
class UsuarioController
{
    private $usuarioModel;

    /**
     * Constructor
     * Inicializa el modelo de usuario
     */
    public function __construct()
    {
        // Cargar el modelo
        $this->usuarioModel = new Usuario();
    }

    /**
     * Acción por defecto del controlador
     * Redirige al dashboard si hay sesión, o muestra la pantalla de login
     */
    public function index()
    {
        // Verificar si hay sesión activa de usuario
        if (isLoggedIn()) {
            // Redirigir según el tipo de usuario
            $this->redirectBasedOnUserType();
            exit();
        }

        // Si no hay sesión, mostrar login
        $this->login();
    }

    /**
     * Muestra la pantalla de login de usuario
     */
    public function login()
    {
        // Si ya está logueado, redirigir al dashboard apropiado
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        // Incluir directamente la vista de login sin layouts
        require_once 'views/usuarios/login.php';
    }

    /**
 * Valida las credenciales del usuario
 * Procesa el formulario de login
 */
public function validate()
{
    // No permitir acceso a esta acción si ya está logueado
    if (isLoggedIn()) {
        $this->redirectBasedOnUserType();
        exit();
    }

    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Validar los campos obligatorios
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        // Obtener y sanitizar datos del formulario
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password']; // No sanitizar contraseña para no alterarla
        $remember = isset($_POST['remember']) ? true : false;

        // Verificar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setLoginError("El formato del email no es válido");
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        // Verificar token CSRF si está habilitado
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $this->setLoginError("Error de seguridad: token inválido");
                header("Location: " . base_url . "usuario/login");
                exit();
            }
        }

        // Usar el método alternativo para verificar las credenciales
        $usuario = $this->usuarioModel->loginAlternative($email, $password);

        if ($usuario) {
            // Crear sesión con los datos del usuario
            $_SESSION['usuario'] = $usuario;

            // Regenerar ID de sesión para prevenir session fixation
            regenerateSession();

            // Si se seleccionó "Recuérdame", crear token y cookie
            if ($remember) {
                $this->createRememberMeCookie($usuario->id);
            }

            // Redirigir según el tipo de usuario
            $this->redirectBasedOnUserType();
            exit();
        } else {
            // Agregar un pequeño delay para prevenir timing attacks
            sleep(1);
            $this->setLoginError("Email o contraseña incorrectos");
            header("Location: " . base_url . "usuario/login");
            exit();
        }
    } else {
        $this->setLoginError("Todos los campos son obligatorios");
        header("Location: " . base_url . "usuario/login");
        exit();
    }
}

    /**
     * Cierra la sesión del usuario
     */
    public function logout()
    {
        // Iniciar buffer de salida para evitar el envío prematuro de headers
        ob_start();

        // Verificar que el usuario está logueado
        if (isLoggedIn()) {
            $usuario_id = $_SESSION['usuario']->id;

            // Registrar el cierre de sesión en la base de datos
            $this->usuarioModel->registerLogout($usuario_id);

            // Limpiar el token de "Recuérdame" 
            $this->usuarioModel->clearRememberToken($usuario_id);

            // Eliminar la cookie de "Recuérdame"
            if (isset($_COOKIE['usuario_remember'])) {
                $this->deleteRememberMeCookie();
            }

            // Destruir la sesión de forma segura
            session_unset();
            session_destroy();
        }

        // Mostrar la vista de logout
        require_once 'views/usuarios/logout.php';

        // Limpia el buffer y envía el contenido
        ob_end_flush();
    }

    /**
     * Muestra el formulario para solicitar recuperación de contraseña
     */
    public function recover()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        // Incluir vista de recuperación
        require_once 'views/usuarios/recover.php';
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function requestReset()
    {
        // No permitir acceso a esta acción si ya está logueado
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // Sanitizar email
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "usuario/recover");
                exit();
            }

            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "usuario/recover");
                    exit();
                }
            }

            // Generar token de recuperación
            $token = $this->usuarioModel->generateRecoveryToken($email);

            if ($token) {
                // Intentar enviar el correo con el enlace
                $sent = $this->usuarioModel->sendRecoveryEmail($email, $token);

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
        header("Location: " . base_url . "usuario/login");
        exit();
    }

    /**
     * Muestra el formulario para restablecer la contraseña
     */
    public function reset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        // Verificar que hay un token
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            $this->setLoginError("Enlace de recuperación inválido");
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        // Sanitizar token
        $token = htmlspecialchars(trim($_GET['token']));

        // Validar el token
        $usuario = $this->usuarioModel->validateRecoveryToken($token);

        if (!$usuario) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        // Incluir vista para restablecer contraseña
        require_once 'views/usuarios/reset.php';
    }

    /**
     * Procesa el restablecimiento de contraseña
     */
    public function doReset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        // Verificar campos obligatorios
        if (!isset($_POST['token']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "usuario/login");
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
                header("Location: " . base_url . "usuario/reset?token=" . urlencode($token));
                exit();
            }
        }

        // Validar que las contraseñas coinciden
        if ($password !== $confirm_password) {
            $this->setLoginError("Las contraseñas no coinciden");
            header("Location: " . base_url . "usuario/reset?token=" . urlencode($token));
            exit();
        }

        // Validar requisitos de contraseña
        if (!validatePasswordStrength($password)) {
            $this->setLoginError("La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una minúscula y un número");
            header("Location: " . base_url . "usuario/reset?token=" . urlencode($token));
            exit();
        }

        // Validar el token
        $usuario = $this->usuarioModel->validateRecoveryToken($token);

        if (!$usuario) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        // Actualizar la contraseña
        $result = $this->usuarioModel->updatePassword($usuario->id, $password);

        if ($result) {
            $_SESSION['success_message'] = "Contraseña actualizada correctamente. Ya puedes iniciar sesión";
        } else {
            $this->setLoginError("Error al actualizar la contraseña. Inténtalo de nuevo");
        }

        header("Location: " . base_url . "usuario/login");
        exit();
    }

    /**
     * Muestra el formulario de registro
     */
    public function registro()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        // Incluir vista de registro
        require_once 'views/usuarios/registro.php';
    }

    /**
     * Procesa el formulario de registro
     */
    public function save()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isLoggedIn()) {
            $this->redirectBasedOnUserType();
            exit();
        }

        if (isset($_POST) && !empty($_POST)) {
            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "usuario/registro");
                    exit();
                }
            }

            // Sanitizar y validar datos
            $nombre = isset($_POST['nombre']) ? filter_var($_POST['nombre'], FILTER_SANITIZE_STRING) : false;
            $apellido = isset($_POST['apellido']) ? filter_var($_POST['apellido'], FILTER_SANITIZE_STRING) : false;
            $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : false;
            $password = isset($_POST['password']) ? $_POST['password'] : false;
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : false;

            // Validar campos obligatorios
            if (!$nombre || !$apellido || !$email || !$password || !$confirm_password) {
                $this->setLoginError("Todos los campos son obligatorios");
                header("Location: " . base_url . "usuario/registro");
                exit();
            }

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "usuario/registro");
                exit();
            }

            // Validar que las contraseñas coinciden
            if ($password !== $confirm_password) {
                $this->setLoginError("Las contraseñas no coinciden");
                header("Location: " . base_url . "usuario/registro");
                exit();
            }

            // Validar requisitos de contraseña
            if (!validatePasswordStrength($password)) {
                $this->setLoginError("La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una minúscula y un número");
                header("Location: " . base_url . "usuario/registro");
                exit();
            }

            // Configurar el modelo con los datos del formulario
            $this->usuarioModel->setNombre($nombre);
            $this->usuarioModel->setApellido($apellido);
            $this->usuarioModel->setEmail($email);
            $this->usuarioModel->setPassword($password);
            $this->usuarioModel->setTipoUsuario('VENDEDOR'); // Tipo por defecto

            // Intentar registrar el usuario
            $save = $this->usuarioModel->registro();

            if ($save) {
                $_SESSION['success_message'] = "Registro completado con éxito. Ya puedes iniciar sesión";
            } else {
                $this->setLoginError("Error al registrar el usuario. Es posible que el email ya esté en uso");
            }
        } else {
            $this->setLoginError("Error al procesar el formulario");
        }

        header("Location: " . base_url . "usuario/login");
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
     * @param int $usuario_id ID del usuario
     */
    private function createRememberMeCookie($usuario_id)
    {
        $token = $this->usuarioModel->createRememberToken($usuario_id, COOKIE_LIFETIME);
        if ($token) {
            // Crear cookie segura
            $secure = isset($_SERVER['HTTPS']); // true si es HTTPS
            $httponly = true; // Evita acceso mediante JavaScript
            setcookie('usuario_remember', $token, [
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
        setcookie('usuario_remember', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    /**
     * Redirige al usuario según su tipo/rol
     */
    private function redirectBasedOnUserType()
    {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']->tipo_usuario)) {
            header("Location: " . base_url . "usuario/login");
            exit();
        }

        switch ($_SESSION['usuario']->tipo_usuario) {
            case 'ADMIN':
                header("Location: " . base_url . "empresas/dashboard");
                break;
            case 'VENDEDOR':
                header("Location: " . base_url . "ventas/dashboard");
                break;
            case 'TOUR_MANAGER':
                header("Location: " . base_url . "eventos/dashboard");
                break;
            default:
                header("Location: " . base_url);
        }
        exit();
    }
}
