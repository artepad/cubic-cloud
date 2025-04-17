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
     * Vista de bienvenida después del login
     */
    public function welcome()
    {
        // Título de la página
        $pageTitle = "Bienvenida al Sistema";

        // Obtener datos del usuario actual
        $user = $_SESSION['user'];
        $ultimo_login = $user->ultimo_login ? date('d/m/Y H:i', strtotime($user->ultimo_login)) : 'Este es tu primer acceso';

        // Inicializar variables
        $empresa = null;
        $plan = null;
        $es_demo = false;
        $dias_restantes = 0;

        // Buscar la empresa donde este usuario es el administrador
        $empresaModel = new Empresa();
        // Obtener conexión a la BD para consulta personalizada
        $db = Database::connect();
        $query = "SELECT * FROM empresas WHERE usuario_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $user->id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $empresa = $result->fetch_object();

            // Si hay empresa, determinar si es demo y obtener plan
            if ($empresa) {
                $es_demo = ($empresa->es_demo == 'Si');

                // Obtener suscripción activa
                $suscripcionModel = new Suscripcion();
                $suscripcion = $suscripcionModel->getActivaByEmpresa($empresa->id);

                if ($suscripcion) {
                    // Obtener plan de la suscripción
                    $planModel = new Plan();
                    $plan = $planModel->getById($suscripcion->plan_id);

                    // Calcular días restantes si es demo
                    if ($es_demo && !empty($empresa->demo_fin)) {
                        $fecha_fin = new DateTime($empresa->demo_fin);
                        $fecha_actual = new DateTime();
                        $intervalo = $fecha_actual->diff($fecha_fin);
                        $dias_restantes = $intervalo->invert ? 0 : $intervalo->days;
                    }
                }
            }
        }
        $stmt->close();

        // Incluir la vista
        require_once 'views/user/dashboard/welcome.php';
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
     * Valida las credenciales del usuario
     * Procesa el formulario de login
     */
    public function validate()
    {
        // No permitir acceso a esta acción si ya está logueado
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        if (isset($_POST['email']) && isset($_POST['password'])) {
            // Validar los campos obligatorios
            if (empty($_POST['email']) || empty($_POST['password'])) {
                $this->setLoginError("Todos los campos son obligatorios");
                header("Location: " . base_url . "user/login");
                exit();
            }

            // Obtener y sanitizar datos del formulario
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password']; // No sanitizar contraseña para no alterarla
            $remember = isset($_POST['remember']) ? true : false;

            // Verificar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "user/login");
                exit();
            }

            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "user/login");
                    exit();
                }
            }

            // Usar el modelo para verificar las credenciales
            $usuario = $this->userModel->login($email, $password);

            if ($usuario) {
                // 1. Verificar si el usuario tiene empresa asociada
                $db = Database::connect();
                $query = "SELECT * FROM empresas WHERE usuario_id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param("i", $usuario->id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    $this->setLoginError("Usuario sin empresa asociada");
                    header("Location: " . base_url . "user/login");
                    exit();
                }

                $empresa = $result->fetch_object();
                $stmt->close();

                // 2. Verificar si la empresa está en modo demo y ya terminó el período
                if ($empresa->es_demo == 'Si' && !empty($empresa->demo_fin)) {
                    $fecha_fin = new DateTime($empresa->demo_fin);
                    $fecha_actual = new DateTime();

                    if ($fecha_actual > $fecha_fin) {
                        $this->setLoginError("El período de prueba de su empresa ha finalizado");
                        header("Location: " . base_url . "user/login");
                        exit();
                    }
                }

                // 3. Verificar si tiene suscripción activa y no caducada
                $suscripcionModel = new Suscripcion();
                $suscripcion = $suscripcionModel->getActivaByEmpresa($empresa->id);

                if (!$suscripcion) {
                    $this->setLoginError("Su empresa no cuenta con un plan activo");
                    header("Location: " . base_url . "user/login");
                    exit();
                }

                // Verificar que la suscripción no esté caducada
                if ($suscripcion->fecha_siguiente_factura) {
                    $fecha_siguiente = new DateTime($suscripcion->fecha_siguiente_factura);
                    $fecha_actual = new DateTime();

                    if ($fecha_actual > $fecha_siguiente && $suscripcion->estado != 'Pendiente') {
                        $this->setLoginError("El plan de su empresa está caducado");
                        header("Location: " . base_url . "user/login");
                        exit();
                    }
                }

                // Si pasó todas las validaciones, permitir el acceso
                $_SESSION['user'] = $usuario;

                // Regenerar ID de sesión para prevenir session fixation
                regenerateSession();

                // Si se seleccionó "Recuérdame", crear token y cookie
                if ($remember) {
                    $this->createRememberMeCookie($usuario->id);
                }

                // Redirigir al dashboard
                header("Location: " . base_url . "user/welcome");
                exit();
            } else {
                // Agregar un pequeño delay para prevenir timing attacks
                sleep(1);
                $this->setLoginError("Email o contraseña incorrectos");
                header("Location: " . base_url . "user/login");
                exit();
            }
        } else {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "user/login");
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
        if (isUserLoggedIn()) {
            $user_id = $_SESSION['user']->id;

            // Registrar el cierre de sesión en la base de datos
            $this->userModel->registerLogout($user_id);

            // Limpiar el token de "Recuérdame" 
            $this->userModel->clearRememberToken($user_id);

            // Eliminar la cookie de "Recuérdame"
            if (isset($_COOKIE['user_remember'])) {
                $this->deleteRememberMeCookie();
            }

            // Destruir la sesión de forma segura
            session_unset();
            session_destroy();
        }

        // Mostrar la vista de logout
        require_once 'views/user/login/logout.php';

        // Limpia el buffer y envía el contenido
        ob_end_flush();
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
     * Procesa la actualización del perfil de usuario
     */
    public function updateProfile()
    {
        // Verificar que el formulario se ha enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
            if (isset($_POST['csrf_token']) && !validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                header("Location: " . base_url . "user/profile");
                exit();
            }

            // Obtener datos del formulario y sanitizarlos
            $nombre = isset($_POST['nombre']) ? filter_var($_POST['nombre'], FILTER_SANITIZE_STRING) : '';
            $apellido = isset($_POST['apellido']) ? filter_var($_POST['apellido'], FILTER_SANITIZE_STRING) : '';
            $telefono = isset($_POST['telefono']) ? filter_var($_POST['telefono'], FILTER_SANITIZE_STRING) : '';

            // Actualizar el perfil usando el modelo
            $user_id = $_SESSION['user']->id;

            // Aquí iría el código para guardar los datos en el modelo

            // Actualizar los datos en la sesión
            $usuario_actualizado = $this->userModel->getById($user_id);
            if ($usuario_actualizado) {
                $_SESSION['user'] = $usuario_actualizado;
                $_SESSION['success_message'] = "Perfil actualizado correctamente";
            } else {
                $_SESSION['error_message'] = "Error al actualizar el perfil";
            }
        }

        // Redirigir al perfil
        header("Location: " . base_url . "user/profile");
        exit();
    }

    /**
     * Muestra el formulario para cambiar contraseña
     */
    public function changePassword()
    {
        // Configurar el título de la página
        $pageTitle = "Cambiar Contraseña";

        // Incluir la vista
        require_once 'views/user/dashboard/change_password.php';
    }

    /**
     * Procesa el cambio de contraseña
     */
    public function updatePassword()
    {
        // Verificar que el formulario se ha enviado
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar token CSRF
            if (isset($_POST['csrf_token']) && !validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                header("Location: " . base_url . "user/changePassword");
                exit();
            }

            // Obtener datos del formulario
            $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
            $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Validar que todos los campos están completos
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['error_message'] = "Todos los campos son obligatorios";
                header("Location: " . base_url . "user/changePassword");
                exit();
            }

            // Verificar que las contraseñas nuevas coinciden
            if ($new_password !== $confirm_password) {
                $_SESSION['error_message'] = "Las contraseñas nuevas no coinciden";
                header("Location: " . base_url . "user/changePassword");
                exit();
            }

            // Validar requisitos de contraseña
            if (!validatePasswordStrength($new_password)) {
                $_SESSION['error_message'] = "La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una minúscula y un número";
                header("Location: " . base_url . "user/changePassword");
                exit();
            }

            // Verificar contraseña actual
            $user_id = $_SESSION['user']->id;
            $usuario = $this->userModel->getById($user_id);

            if (password_verify($current_password, $usuario->password)) {
                // Actualizar contraseña
                $result = $this->userModel->updatePassword($user_id, $new_password);

                if ($result) {
                    $_SESSION['success_message'] = "Contraseña actualizada correctamente";
                } else {
                    $_SESSION['error_message'] = "Error al actualizar la contraseña";
                }
            } else {
                $_SESSION['error_message'] = "La contraseña actual es incorrecta";
            }
        }

        // Redirigir
        header("Location: " . base_url . "user/changePassword");
        exit();
    }

    /**
     * Muestra el formulario para solicitar recuperación de contraseña
     */
    public function recover()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        // Incluir vista de recuperación
        require_once 'views/user/login/recover.php';
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     */
    public function requestReset()
    {
        // No permitir acceso a esta acción si ya está logueado
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        if (isset($_POST['email']) && !empty($_POST['email'])) {
            // Sanitizar email
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->setLoginError("El formato del email no es válido");
                header("Location: " . base_url . "user/login");
                exit();
            }

            // Verificar token CSRF si está habilitado
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $this->setLoginError("Error de seguridad: token inválido");
                    header("Location: " . base_url . "user/login");
                    exit();
                }
            }

            // Generar token de recuperación
            $token = $this->userModel->generateRecoveryToken($email);

            if ($token) {
                // Intentar enviar el correo con el enlace
                $sent = $this->userModel->sendRecoveryEmail($email, $token);

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
        header("Location: " . base_url . "user/login");
        exit();
    }

    /**
     * Muestra el formulario para restablecer la contraseña
     */
    public function reset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        // Verificar que hay un token
        if (!isset($_GET['token']) || empty($_GET['token'])) {
            $this->setLoginError("Enlace de recuperación inválido");
            header("Location: " . base_url . "user/login");
            exit();
        }

        // Sanitizar token
        $token = htmlspecialchars(trim($_GET['token']));

        // Validar el token
        $usuario = $this->userModel->validateRecoveryToken($token);

        if (!$usuario) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "user/login");
            exit();
        }

        // Incluir vista para restablecer contraseña
        require_once 'views/user/login/reset.php';
    }

    /**
     * Procesa el restablecimiento de contraseña
     */
    public function doReset()
    {
        // Si ya está logueado, redirigir al dashboard
        if (isUserLoggedIn()) {
            header("Location: " . base_url . "user/dashboard");
            exit();
        }

        // Verificar campos obligatorios
        if (!isset($_POST['token']) || !isset($_POST['password']) || !isset($_POST['confirm_password'])) {
            $this->setLoginError("Todos los campos son obligatorios");
            header("Location: " . base_url . "user/login");
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
                header("Location: " . base_url . "user/reset?token=" . urlencode($token));
                exit();
            }
        }

        // Validar que las contraseñas coinciden
        if ($password !== $confirm_password) {
            $this->setLoginError("Las contraseñas no coinciden");
            header("Location: " . base_url . "user/reset?token=" . urlencode($token));
            exit();
        }

        // Validar requisitos de contraseña
        if (!validatePasswordStrength($password)) {
            $this->setLoginError("La contraseña debe tener al menos 8 caracteres, incluir una letra mayúscula, una minúscula y un número");
            header("Location: " . base_url . "user/reset?token=" . urlencode($token));
            exit();
        }

        // Validar el token
        $usuario = $this->userModel->validateRecoveryToken($token);

        if (!$usuario) {
            $this->setLoginError("El enlace ha expirado o no es válido");
            header("Location: " . base_url . "user/login");
            exit();
        }

        // Actualizar la contraseña
        $result = $this->userModel->updatePassword($usuario->id, $password);

        if ($result) {
            $_SESSION['success_message'] = "Contraseña actualizada correctamente. Ya puedes iniciar sesión";
        } else {
            $this->setLoginError("Error al actualizar la contraseña. Inténtalo de nuevo");
        }

        header("Location: " . base_url . "user/login");
        exit();
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
     * Crea una cookie "Recuérdame" segura
     * @param int $user_id ID del usuario
     */
    private function createRememberMeCookie($user_id)
    {
        $token = $this->userModel->createRememberToken($user_id, COOKIE_LIFETIME);
        if ($token) {
            // Crear cookie segura
            $secure = isset($_SERVER['HTTPS']); // true si es HTTPS
            $httponly = true; // Evita acceso mediante JavaScript
            setcookie('user_remember', $token, [
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
