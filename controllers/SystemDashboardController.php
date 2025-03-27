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
     * Muestra el formulario para crear un nuevo usuario
     */
    public function crearUsuario()
    {
        // Validar que el usuario sea superadmin
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }

        // Configurar el título de la página
        $pageTitle = "Crear Nuevo Usuario";

        // Incluir la vista
        require_once 'views/admin_dashboard/crear_usuario.php';
    }

    /**
     * Guarda los datos del nuevo usuario
     */
    /**
     * Guarda los datos del nuevo usuario
     */
    public function saveUsuario()
    {
        // Iniciar buffer de salida para evitar problemas de redirección
        ob_start();

        // Validar que el usuario sea superadmin
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            ob_end_flush();
            exit();
        }

        // Verificar que se han enviado los datos del formulario
        if (isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['email']) && isset($_POST['password'])) {

            // Crear una instancia del modelo de usuario
            $usuario = new Usuario();

            // Verificar que el email no exista ya
            if ($usuario->emailExists($_POST['email'])) {
                $_SESSION['error_message'] = "El correo electrónico ya está registrado";
                header("Location:" . base_url . "systemDashboard/crearUsuario");
                ob_end_flush();
                exit();
            }

            // Verificar que las contraseñas coinciden
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error_message'] = "Las contraseñas no coinciden";
                header("Location:" . base_url . "systemDashboard/crearUsuario");
                ob_end_flush();
                exit();
            }

            // Establecer los datos del usuario
            $usuario->setNombre($_POST['nombre']);
            $usuario->setApellido($_POST['apellido']);
            $usuario->setEmail($_POST['email']);
            $usuario->setPassword($_POST['password']);
            $usuario->setTelefono($_POST['telefono'] ?? '');
            $usuario->setPais($_POST['pais'] ?? 'Chile');
            $usuario->setCodigoPais($_POST['codigo_pais'] ?? 'CL');
            $usuario->setNumeroIdentificacion($_POST['numero_identificacion'] ?? '');
            $usuario->setTipoIdentificacion($_POST['tipo_identificacion'] ?? 'RUT');
            $usuario->setTipoUsuario($_POST['tipo_usuario'] ?? 'ADMIN');
            $usuario->setEstado($_POST['estado'] ?? 'Activo');

            // Guardar el usuario
            $save = $usuario->save();

            if ($save) {
                $_SESSION['success_message'] = "Usuario creado correctamente";

                // Usar una redirección JavaScript como respaldo en caso de que header() falle
                echo "<script>window.location.href = '" . base_url . "systemDashboard/usuarios';</script>";

                // Intentar redirección normal
                header("Location: " . base_url . "systemDashboard/usuarios");
                ob_end_flush();
                exit();
            } else {
                $_SESSION['error_message'] = "Error al crear el usuario";
                header("Location: " . base_url . "systemDashboard/crearUsuario");
                ob_end_flush();
                exit();
            }
        } else {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            header("Location: " . base_url . "systemDashboard/crearUsuario");
            ob_end_flush();
            exit();
        }

        // Si algo falla, mostrar una página de redirección manual
        require_once 'views/admin_dashboard/redirect.php';
        ob_end_flush();
        exit();
    }
}
