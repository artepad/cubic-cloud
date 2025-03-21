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
}