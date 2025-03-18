<?php

/**
 * SystemDashboardController
 * 
 * Controlador para gestionar el dashboard y funcionalidades específicas
 * del superadministrador del sistema
 */
class SystemDashboardController
{
    /**
     * Constructor de la clase
     */
    public function __construct()
    {
        // Validar que el usuario sea superadmin
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Vista principal del dashboard
     */
    public function index()
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
        require_once 'views/admin_dashboard/index.php';
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
        require_once 'views/admin_dashboard/welcome.php';
    }

    /**
     * Gestión de empresas
     */
    public function empresas()
    {
        $pageTitle = "Gestión de Empresas";
        require_once 'views/admin_dashboard/empresas.php';
    }

    /**
     * Gestión de usuarios
     */
    public function usuarios()
    {
        $pageTitle = "Gestión de Usuarios";
        require_once 'views/admin_dashboard/usuarios.php';
    }

    /**
     * Gestión de planes
     */
    public function planes()
    {
        $pageTitle = "Gestión de Planes";
        require_once 'views/admin_dashboard/planes.php';
    }
    /**
     * Gestión de suscripciones
     */
    public function suscripciones()
    {
        $pageTitle = "Gestión de Suscripciones";
        require_once 'views/admin_dashboard/suscripciones.php';
    }
    /**
     * Configuración del sistema
     */
    public function configuracion()
    {
        $pageTitle = "Configuración del Sistema";
        require_once 'views/admin_dashboard/configuracion.php';
    }

    /**
     * Obtiene la cantidad total de empresas
     * @return int Número de empresas
     */
    private function getEmpresasCount()
    {
        // En un caso real, esto consultaría la base de datos
        // Por ahora retornamos un valor de ejemplo
        return 15;
    }

    /**
     * Obtiene la cantidad total de usuarios
     * @return int Número de usuarios
     */
    private function getUsuariosCount()
    {
        // En un caso real, esto consultaría la base de datos
        return 54;
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
     * Muestra formulario para crear un nuevo usuario
     */
    public function crearUsuario()
    {
        $pageTitle = "Crear Nuevo Usuario";

        // Usar un array vacío temporalmente en lugar de llamar a getEmpresas()
        $empresas = [];

        // Incluir la vista del formulario
        require_once 'views/admin_dashboard/crear_usuario.php';
    }

    /**
 * Guarda un nuevo usuario en el sistema
 */
public function guardarUsuario()
{
    // Verificar que los datos vengan por POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['error_message'] = "Método de envío no válido";
        header("Location:" . base_url . "systemDashboard/usuarios");
        exit();
    }

    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Error de seguridad: token inválido";
        header("Location:" . base_url . "systemDashboard/usuarios");
        exit();
    }

    // Validar campos obligatorios
    $campos_requeridos = ['nombre', 'apellido', 'email', 'pais', 'tipo_usuario', 'estado', 'password', 'confirm_password'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            $_SESSION['error_message'] = "El campo {$campo} es obligatorio";
            header("Location:" . base_url . "systemDashboard/crearUsuario");
            exit();
        }
    }

    // Validar formato de email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "El formato del email no es válido";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }

    // Validar que las contraseñas coincidan
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['error_message'] = "Las contraseñas no coinciden";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }

    // Validar la fortaleza de la contraseña
    if (!validatePasswordStrength($_POST['password'])) {
        $_SESSION['error_message'] = "La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }

    // Crear instancia del modelo de Usuario
    $usuarioModel = new Usuario();

    // Verificar si el email ya existe
    if ($usuarioModel->emailExists($email)) {
        $_SESSION['error_message'] = "El email ya está registrado en el sistema";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }

    // Preparar datos del usuario (con sanitización)
    $usuario = [
        'nombre' => htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8'),
        'apellido' => htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8'),
        'email' => $email,
        'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8') : null,
        'pais' => htmlspecialchars(trim($_POST['pais']), ENT_QUOTES, 'UTF-8'),
        'codigo_pais' => $this->getCodigoPais($_POST['pais']),
        'tipo_identificacion' => isset($_POST['tipo_identificacion']) ? htmlspecialchars(trim($_POST['tipo_identificacion']), ENT_QUOTES, 'UTF-8') : null,
        'numero_identificacion' => isset($_POST['numero_identificacion']) ? htmlspecialchars(trim($_POST['numero_identificacion']), ENT_QUOTES, 'UTF-8') : null,
        'tipo_usuario' => htmlspecialchars(trim($_POST['tipo_usuario']), ENT_QUOTES, 'UTF-8'),
        'password' => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
        'estado' => htmlspecialchars(trim($_POST['estado']), ENT_QUOTES, 'UTF-8'),
        'notificaciones' => json_encode([
            'email' => isset($_POST['notif_email']) ? true : false,
            'sistema' => isset($_POST['notif_sistema']) ? true : false
        ])
    ];

    // Guardar el usuario
    $result = $usuarioModel->save($usuario);

    if ($result) {
        $_SESSION['success_message'] = "Usuario creado correctamente";
        header("Location:" . base_url . "systemDashboard/usuarios");
    } else {
        $_SESSION['error_message'] = "Error al crear el usuario. Por favor, inténtalo de nuevo";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
    }
    exit();
}

/**
 * Obtiene el código ISO del país a partir del nombre
 * 
 * @param string $nombre_pais Nombre del país
 * @return string Código ISO de 2 letras
 */
private function getCodigoPais($nombre_pais)
{
    $codigos = [
        'Chile' => 'CL',
        'Argentina' => 'AR',
        'México' => 'MX',
        'Colombia' => 'CO',
        'Perú' => 'PE',
        'España' => 'ES',
        'Estados Unidos' => 'US'
    ];

    return isset($codigos[$nombre_pais]) ? $codigos[$nombre_pais] : 'XX';
}
}
