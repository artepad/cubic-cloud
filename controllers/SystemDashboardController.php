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
 * Procesa la creación de un nuevo usuario
 */
public function guardarUsuario()
{
    // Verificar si es una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location:" . base_url . "systemDashboard/usuarios");
        exit();
    }
    
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $_SESSION['error_message'] = "Error de seguridad: token inválido";
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }
    
    // Validar campos obligatorios
    $campos_requeridos = ['nombre', 'apellido', 'email', 'estado', 'password', 'confirm_password'];
    foreach ($campos_requeridos as $campo) {
        if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
            $_SESSION['error_message'] = "Todos los campos marcados con * son obligatorios";
            $_SESSION['form_data'] = $_POST; // Guardar datos para repoblar el formulario
            header("Location:" . base_url . "systemDashboard/crearUsuario");
            exit();
        }
    }
    
    // Validar coincidencia de contraseñas
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['error_message'] = "Las contraseñas no coinciden";
        $_SESSION['form_data'] = $_POST;
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }
    
    // Validar fortaleza de contraseña
    if (!validatePasswordStrength($_POST['password'])) {
        $_SESSION['error_message'] = "La contraseña no cumple con los requisitos mínimos de seguridad";
        $_SESSION['form_data'] = $_POST;
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }
    
    // Validar formato de email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "El formato del email no es válido";
        $_SESSION['form_data'] = $_POST;
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }
    
    // Verificar si el email ya existe
    if ($this->usuarioExiste($_POST['email'])) {
        $_SESSION['error_message'] = "El email ya está registrado en el sistema";
        $_SESSION['form_data'] = $_POST;
        header("Location:" . base_url . "systemDashboard/crearUsuario");
        exit();
    }
    
    // Preparar datos del usuario con sanitización
    $usuario = [
        'nombre' => htmlspecialchars(trim($_POST['nombre'])),
        'apellido' => htmlspecialchars(trim($_POST['apellido'])),
        'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
        'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono'])) : null,
        'pais' => htmlspecialchars(trim($_POST['pais'])),
        'codigo_pais' => $this->getCodigoPais($_POST['pais']),
        'tipo_identificacion' => isset($_POST['tipo_identificacion']) ? htmlspecialchars(trim($_POST['tipo_identificacion'])) : null,
        'numero_identificacion' => isset($_POST['numero_identificacion']) ? htmlspecialchars(trim($_POST['numero_identificacion'])) : null,
        'tipo_usuario' => 'ADMIN', // Forzar el tipo de usuario a ADMIN
        'estado' => htmlspecialchars(trim($_POST['estado'])),
        'password' => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
        // Datos adicionales
        'notificaciones' => json_encode([
            'email' => isset($_POST['notif_email']) ? true : false,
            'sistema' => isset($_POST['notif_sistema']) ? true : false
        ])
    ];
    
    // Guardar el usuario en la base de datos
    $resultado = $this->guardarUsuarioDB($usuario);
    
    if ($resultado) {
        // Éxito
        $_SESSION['success_message'] = "Usuario administrador creado correctamente";
        header("Location:" . base_url . "systemDashboard/usuarios");
    } else {
        // Error
        $_SESSION['error_message'] = "Error al crear el usuario. Inténtalo de nuevo";
        $_SESSION['form_data'] = $_POST;
        header("Location:" . base_url . "systemDashboard/crearUsuario");
    }
    exit();
}

/**
 * Guarda un nuevo usuario en la base de datos
 * 
 * @param array $usuario Datos del usuario a guardar
 * @return bool True si se guardó correctamente
 */
private function guardarUsuarioDB($usuario)
{
    // Conexión a la base de datos
    $db = Database::connect();
    
    // Consulta preparada
    $query = "INSERT INTO usuarios (
                nombre, apellido, email, telefono, pais, codigo_pais,
                numero_identificacion, tipo_identificacion, tipo_usuario,
                password, estado, notificaciones
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param(
        'ssssssssssss',
        $usuario['nombre'],
        $usuario['apellido'],
        $usuario['email'],
        $usuario['telefono'],
        $usuario['pais'],
        $usuario['codigo_pais'],
        $usuario['numero_identificacion'],
        $usuario['tipo_identificacion'],
        $usuario['tipo_usuario'],
        $usuario['password'],
        $usuario['estado'],
        $usuario['notificaciones']
    );
    
    $resultado = $stmt->execute();
    
    $stmt->close();
    $db->close();
    
    return $resultado;
}

/**
 * Verifica si ya existe un usuario con el email proporcionado
 * 
 * @param string $email Email a verificar
 * @return bool True si el usuario ya existe
 */
private function usuarioExiste($email)
{
    // Conexión a la base de datos
    $db = Database::connect();
    
    // Consulta preparada
    $query = "SELECT COUNT(*) AS total FROM usuarios WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    
    $stmt->close();
    $db->close();
    
    return ($row->total > 0);
}

/**
 * Obtiene el código ISO de un país
 * 
 * @param string $nombrePais Nombre del país
 * @return string Código ISO del país
 */
private function getCodigoPais($nombrePais)
{
    // Mapeo de países a códigos ISO
    $codigosPais = [
        'Chile' => 'CL',
        'Argentina' => 'AR',
        'México' => 'MX',
        'Colombia' => 'CO',
        'Perú' => 'PE',
        'España' => 'ES',
        'Estados Unidos' => 'US'
    ];
    
    return isset($codigosPais[$nombrePais]) ? $codigosPais[$nombrePais] : 'XX';
}


}


