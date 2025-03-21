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

    public function redirectAfterSave()
    {
        // Llamar al método que guarda el usuario
        $result = $this->guardarUsuario();

        if ($result) {
            // Mostrar la página de redirección
            require_once 'views/admin_dashboard/redirect.php';
        } else {
            // Incluir directamente la vista de creación de usuario con mensaje de error
            $this->crearUsuario();
        }

        exit();
    }

    /**
     * Guarda un nuevo usuario en el sistema
     */
    public function guardarUsuario()
    {
        try {
            // Validar campos obligatorios
            $campos_requeridos = ['nombre', 'apellido', 'email', 'pais', 'tipo_usuario', 'estado', 'password', 'confirm_password'];
            foreach ($campos_requeridos as $campo) {
                if (!isset($_POST[$campo]) || empty($_POST[$campo])) {
                    $_SESSION['error_message'] = "El campo {$campo} es obligatorio";
                    return false;
                }
            }

            // Validar formato de email
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error_message'] = "El formato del email no es válido";
                return false;
            }

            // Validar que las contraseñas coincidan
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error_message'] = "Las contraseñas no coinciden";
                return false;
            }

            // Crear instancia del modelo de Usuario
            $usuarioModel = new Usuario();

            // Verificar si el email ya existe
            if ($usuarioModel->emailExists($email)) {
                $_SESSION['error_message'] = "El email ya está registrado en el sistema";
                return false;
            }

            // Datos básicos del usuario
            $usuario = [
                'nombre' => htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8'),
                'apellido' => htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8'),
                'email' => $email,
                'telefono' => isset($_POST['telefono']) ? htmlspecialchars(trim($_POST['telefono']), ENT_QUOTES, 'UTF-8') : '',
                'pais' => htmlspecialchars(trim($_POST['pais']), ENT_QUOTES, 'UTF-8'),
                'codigo_pais' => $this->getCodigoPais($_POST['pais']),
                'tipo_identificacion' => isset($_POST['tipo_identificacion']) ? htmlspecialchars(trim($_POST['tipo_identificacion']), ENT_QUOTES, 'UTF-8') : '',
                'numero_identificacion' => isset($_POST['numero_identificacion']) ? htmlspecialchars(trim($_POST['numero_identificacion']), ENT_QUOTES, 'UTF-8') : '',
                'tipo_usuario' => htmlspecialchars(trim($_POST['tipo_usuario']), ENT_QUOTES, 'UTF-8'),
                'password' => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
                'estado' => htmlspecialchars(trim($_POST['estado']), ENT_QUOTES, 'UTF-8')
            ];

            $result = $usuarioModel->save($usuario);

            if ($result) {
                $_SESSION['success_message'] = "Usuario creado correctamente";
                return true;
            } else {
                $_SESSION['error_message'] = "Error al crear el usuario";
                return false;
            }
        } catch (Exception $e) {
            error_log("Error en guardarUsuario: " . $e->getMessage());
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            return false;
        }
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

    /**
     * Muestra formulario para editar un usuario existente
     * 
     * @param int $id ID del usuario a editar
     */
    public function editarUsuario($id = null)
    {
        if (!$id) {
            $_SESSION['error_message'] = "ID de usuario no especificado";
            header("Location:" . base_url . "systemDashboard/usuarios");
            exit();
        }

        // Obtener datos del usuario
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->getById($id);

        if (!$usuario) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            header("Location:" . base_url . "systemDashboard/usuarios");
            exit();
        }

        $pageTitle = "Editar Usuario";

        // Cargar la vista
        require_once 'views/admin_dashboard/editar_usuario.php';
    }

    /**
     * Actualiza los datos de un usuario existente
     */
    public function actualizarUsuario()
    {
        // Esta función se implementará más adelante
        $_SESSION['error_message'] = "Función en desarrollo";
        header("Location:" . base_url . "systemDashboard/usuarios");
        exit();
    }

    /**
     * Elimina un usuario del sistema
     * 
     * @param int $id ID del usuario a eliminar
     */
    public function eliminarUsuario($id = null)
    {
        if (!$id) {
            $_SESSION['error_message'] = "ID de usuario no especificado";
            header("Location:" . base_url . "systemDashboard/usuarios");
            exit();
        }

        // Eliminar usuario
        $usuarioModel = new Usuario();
        $result = $usuarioModel->delete($id);

        if ($result) {
            $_SESSION['success_message'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el usuario";
        }

        header("Location:" . base_url . "systemDashboard/usuarios");
        exit();
    }
}
