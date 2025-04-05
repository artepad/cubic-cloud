<?php

/**
 * AdminController
 * 
 * Controlador para gestión de superadministradores, incluyendo login, logout,
 * recuperación de contraseñas y gestión del dashboard.
 * Refactorizado para seguir el principio de responsabilidad única.
 */
class AdminController
{
    private $adminModel;
    private $empresaModel;
    private $planModel;
    private $suscripcionModel;

    /**
     * Constructor
     * Inicializa el modelo de administrador
     */
    public function __construct()
    {
        // Cargar los modelos
        $this->adminModel = new SystemAdmin();
        $this->empresaModel = class_exists('Empresa') ? new Empresa() : null;
        $this->planModel = class_exists('Plan') ? new Plan() : null;
        
        // Verificar si existe el modelo de Suscripcion en el sistema
        if (class_exists('Suscripcion')) {
            $this->suscripcionModel = new Suscripcion();
        }

        // Verificar autenticación para las acciones del dashboard
        // No aplicar esta verificación para acciones de login/autenticación
        $publicMethods = ['index', 'login', 'validate', 'recover', 'requestReset', 'reset', 'doReset'];
        $currentMethod = isset($_GET['action']) ? $_GET['action'] : 'index';

        if (!in_array($currentMethod, $publicMethods) && !isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Acción por defecto del controlador
     * Redirige al dashboard si hay sesión, o muestra la pantalla de login
     */
    public function index()
    {
        // Verificar si hay sesión activa de administrador
        if (isAdminLoggedIn()) {
            header("Location: " . base_url . "admin/dashboard");
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
            header("Location: " . base_url . "admin/dashboard");
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
            header("Location: " . base_url . "admin/dashboard");
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

                // Redirigir al dashboard
                header("Location: " . base_url . "admin/welcome");
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
            header("Location: " . base_url . "admin/dashboard");
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
            header("Location: " . base_url . "admin/dashboard");
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
            header("Location: " . base_url . "admin/dashboard");
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
            header("Location: " . base_url . "admin/dashboard");
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
     * Vista principal del dashboard
     */
    public function dashboard()
    {
        // Configurar el título de la página (si se usa en el layout)
        $pageTitle = "Dashboard del Sistema";

        // Cargar datos necesarios para el dashboard
        $admin = $_SESSION['admin'];
        $ultimo_login = $admin->ultimo_login ? date('d/m/Y H:i', strtotime($admin->ultimo_login)) : 'Este es tu primer acceso';

        // Datos de estadísticas
        $empresas_count = $this->getEmpresasCount();
        $usuarios_count = $this->getUsuariosCount();
        $eventos_count = $this->getEventosCount();
        $ingresos = $this->getIngresosEstimados();

        // Incluir la vista
        require_once 'views/admin/dashboard/index.php';
    }

    /**
     * Vista de bienvenida después del login
     */
    public function welcome()
    {
        // Configurar el título de la página (si se usa en el layout)
        $pageTitle = "Bienvenida al Sistema";

        // Obtener datos del admin actual
        $admin = $_SESSION['admin'];
        $ultimo_login = $admin->ultimo_login ? date('d/m/Y H:i', strtotime($admin->ultimo_login)) : 'Este es tu primer acceso';

        // Cargar datos de resumen del sistema
        $empresas_count = $this->getEmpresasCount();
        $usuarios_count = $this->getUsuariosCount();
        $eventos_count = $this->getEventosCount();
        $ingresos = $this->getIngresosEstimados();

        // Incluir la vista
        require_once 'views/admin/dashboard/welcome.php';
    }

    /**
     * Configuración del sistema
     */
    public function configuracion()
    {
        $pageTitle = "Configuración del Sistema";
        require_once 'views/admin/dashboard/configuracion.php';
    }

    /**
     * Muestra la lista de suscripciones
     */
    public function suscripciones()
    {
        // Título de la página
        $pageTitle = "Gestión de Suscripciones";

        // Redirigir si no existe el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            header("Location: " . base_url . "admin/dashboard");
            exit();
        }

        // Obtener parámetros de filtrado y paginación
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $elementosPorPagina = 10;
        $offset = ($pagina - 1) * $elementosPorPagina;

        // Aplicar filtros si existen
        $filters = [];
        if (isset($_GET['estado']) && !empty($_GET['estado'])) {
            $filters['estado'] = $_GET['estado'];
        }
        if (isset($_GET['empresa_id']) && !empty($_GET['empresa_id'])) {
            $filters['empresa_id'] = $_GET['empresa_id'];
        }
        if (isset($_GET['plan_id']) && !empty($_GET['plan_id'])) {
            $filters['plan_id'] = $_GET['plan_id'];
        }
        if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
            $filters['busqueda'] = $_GET['busqueda'];
        }
        if (isset($_GET['periodo']) && !empty($_GET['periodo'])) {
            $filters['periodo'] = $_GET['periodo'];
        }
        if (isset($_GET['vencidas']) && $_GET['vencidas'] == '1') {
            $filters['vencidas'] = true;
        }

        // Obtener suscripciones con paginación
        $suscripciones = $this->suscripcionModel->getAll($filters, $elementosPorPagina, $offset);
        $total_suscripciones = $this->suscripcionModel->countAll($filters);

        // Obtener listas para los filtros
        $empresas = $this->empresaModel->getAll();
        $planes = $this->planModel->getAll();

        // Cálculos para paginación
        $total_paginas = ceil($total_suscripciones / $elementosPorPagina);

        // Cargar la vista
        require_once 'views/admin/suscripciones/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva suscripción
     */
    public function crearSuscripcion()
    {
        // Título de la página
        $pageTitle = "Crear Nueva Suscripción";

        // Verificar que exista el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            header("Location: " . base_url . "admin/dashboard");
            exit();
        }

        // Obtener datos para selectores
        $empresas = $this->empresaModel->getAll(['estado' => 'activa']);
        $planes = $this->planModel->getAll(['estado' => 'Activo']);

        // Verificar si hay empresa preseleccionada (desde vista empresa)
        $empresa_id_preseleccionado = isset($_GET['empresa_id']) ? intval($_GET['empresa_id']) : null;
        $empresa_preseleccionada = null;

        if ($empresa_id_preseleccionado) {
            $empresa_preseleccionada = $this->empresaModel->getById($empresa_id_preseleccionado);
        }

        // Cargar la vista
        require_once 'views/admin/suscripciones/crear.php';
    }

    /**
     * Procesa el formulario para guardar una nueva suscripción
     */
    public function saveSuscripcion()
    {
        // Verificar si existe el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            $this->redirectTo('admin/dashboard');
            return;
        }

        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('admin/suscripciones');
            return;
        }

        // Verificar token CSRF
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo('admin/crearSuscripcion');
                return;
            }
        }

        // Validar campos obligatorios
        if (empty($_POST['empresa_id']) || empty($_POST['plan_id']) || empty($_POST['precio_total']) || empty($_POST['fecha_inicio'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('admin/crearSuscripcion');
            return;
        }

        try {
            // Crear y configurar el objeto Suscripcion
            $suscripcion = new Suscripcion();
            $suscripcion->setEmpresaId($_POST['empresa_id']);
            $suscripcion->setPlanId($_POST['plan_id']);
            $suscripcion->setNumeroSuscripcion($_POST['numero_suscripcion']);
            $suscripcion->setPeriodoFacturacion($_POST['periodo_facturacion']);
            $suscripcion->setFechaInicio($_POST['fecha_inicio']);

            // Calcular fecha siguiente factura según período
            $fecha_inicio = new DateTime($_POST['fecha_inicio']);
            $fecha_siguiente = clone $fecha_inicio;

            switch ($_POST['periodo_facturacion']) {
                case 'Mensual':
                    $fecha_siguiente->add(new DateInterval('P1M'));
                    break;
                case 'Semestral':
                    $fecha_siguiente->add(new DateInterval('P6M'));
                    break;
                case 'Anual':
                    $fecha_siguiente->add(new DateInterval('P1Y'));
                    break;
            }

            $suscripcion->setFechaSiguienteFactura($fecha_siguiente->format('Y-m-d'));
            $suscripcion->setPrecioTotal($_POST['precio_total']);
            $suscripcion->setMoneda($_POST['moneda']);
            $suscripcion->setEstado($_POST['estado']);

            // Guardar la suscripción
            $id = $suscripcion->save();

            if ($id) {
                $_SESSION['success_message'] = "Suscripción creada correctamente";
                $this->redirectTo('admin/suscripciones');
            } else {
                $_SESSION['error_message'] = "Error al crear la suscripción";
                $this->redirectTo('admin/crearSuscripcion');
            }
        } catch (Exception $e) {
            error_log("Error en saveSuscripcion: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al procesar la solicitud: " . $e->getMessage();
            $this->redirectTo('admin/crearSuscripcion');
        }
    }

    /**
     * Cambia el estado de una suscripción
     */
    public function cambiarEstadoSuscripcion()
    {
        // Verificar que exista el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            $this->redirectTo('admin/dashboard');
            return;
        }

        // Verificar parámetros necesarios
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            $this->redirectTo('admin/suscripciones');
            return;
        }

        $id = (int)$_GET['id'];
        $estado = $_GET['estado'];

        // Validar estado
        $estados_validos = ['Activa', 'Suspendida', 'Cancelada', 'Finalizada', 'Pendiente'];
        if (!in_array($estado, $estados_validos)) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('admin/suscripciones');
            return;
        }

        // Cargar modelo y actualizar estado
        $motivo = "Cambio de estado manual desde el panel de administración";
        $resultado = $this->suscripcionModel->cambiarEstado($id, $estado, $motivo);

        if ($resultado) {
            $_SESSION['success_message'] = "Estado de suscripción actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado de la suscripción";
        }

        $this->redirectTo('admin/suscripciones');
    }

    /**
     * Renueva una suscripción
     */
    public function renovarSuscripcion()
    {
        // Verificar que exista el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            $this->redirectTo('admin/dashboard');
            return;
        }

        // Verificar parámetro necesario
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('admin/suscripciones');
            return;
        }

        $id = (int)$_GET['id'];

        // Cargar modelo y renovar
        $resultado = $this->suscripcionModel->renovar($id);

        if ($resultado) {
            $_SESSION['success_message'] = "Suscripción renovada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al renovar la suscripción";
        }

        $this->redirectTo('admin/suscripciones');
    }

    /**
     * Muestra el historial de cambios de una suscripción
     */
    public function historialSuscripcion()
    {
        // Verificar que exista el modelo de suscripción
        if (!isset($this->suscripcionModel)) {
            $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
            $this->redirectTo('admin/dashboard');
            return;
        }

        // Título de la página
        $pageTitle = "Historial de Suscripción";

        // Verificar parámetro necesario
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('admin/suscripciones');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener suscripción y su historial
        $suscripcion = $this->suscripcionModel->getById($id);

        if (!$suscripcion) {
            $_SESSION['error_message'] = "Suscripción no encontrada";
            $this->redirectTo('admin/suscripciones');
            return;
        }

        // Obtener empresa y plan asociados
        $empresa = $this->empresaModel->getById($suscripcion->empresa_id);
        $plan = $this->planModel->getById($suscripcion->plan_id);

        // Incluir la vista
        require_once 'views/admin/suscripciones/historial.php';
    }

    /**
     * Método auxiliar para redireccionar
     */
    private function redirectTo($path)
    {
        // Verificar que no se hayan enviado headers aún
        if (!headers_sent()) {
            header("Location: " . base_url . $path);
        } else {
            // Usar JavaScript como respaldo si los headers ya se enviaron
            echo "<script>window.location.href = '" . base_url . $path . "';</script>";
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . base_url . $path . '">';
            echo '</noscript>';
        }
        exit();
    }

    /**
     * Obtiene la cantidad total de empresas
     * @return int Número de empresas
     */
    private function getEmpresasCount()
    {
        if ($this->empresaModel) {
            return $this->empresaModel->countAll();
        }
        return 0;
    }

    /**
     * Obtiene la cantidad total de usuarios
     * @return int Número de usuarios
     */
    private function getUsuariosCount()
    {
        $usuarioModel = new Usuario();
        return $usuarioModel->countAll();
    }

    /**
     * Obtiene la cantidad total de eventos activos
     * @return int Número de eventos
     */
    private function getEventosCount()
    {
        // En un caso real, esto consultaría la base de datos
        return 32;
    }

    /**
     * Obtiene los ingresos estimados
     * @return string Ingresos formateados
     */
    private function getIngresosEstimados()
    {
        // En un caso real, esto consultaría la base de datos
        return '$4,500';
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