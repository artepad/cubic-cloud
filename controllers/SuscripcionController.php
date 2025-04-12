<?php

/**
 * SuscripcionController
 * 
 * Controlador para la gestión completa de suscripciones en el sistema.
 * Separado del AdminController para seguir el principio de responsabilidad única.
 */
class SuscripcionController
{
    private $suscripcionModel;
    private $empresaModel;
    private $planModel;

    /**
     * Constructor
     * Inicializa los modelos necesarios y verifica autenticación
     */
    public function __construct()
    {
        // Cargar los modelos
        if (class_exists('Suscripcion')) {
            $this->suscripcionModel = new Suscripcion();
        } else {
            $this->modulo_no_disponible();
        }
        
        $this->empresaModel = new Empresa();
        $this->planModel = new Plan();

        // Verificar autenticación para todas las acciones
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Acción por defecto del controlador - Lista todas las suscripciones
     */
    public function index()
    {
        // Título de la página
        $pageTitle = "Gestión de Suscripciones";

        // Cargar la vista
        require_once 'views/admin/suscripciones/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva suscripción
     */
    public function crear()
    {
        // Título de la página
        $pageTitle = "Crear Nueva Suscripción";

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
        require_once 'views/admin/suscripcioness/crear.php';
    }

    /**
     * Procesa el formulario para guardar una nueva suscripción
     */
    public function guardar()
    {
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('suscripcion/index');
            return;
        }

        // Verificar token CSRF
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo('suscripcion/crear');
                return;
            }
        }

        // Validar campos obligatorios
        if (empty($_POST['empresa_id']) || empty($_POST['plan_id']) || empty($_POST['precio_total']) || empty($_POST['fecha_inicio'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('suscripcion/crear');
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
                $this->redirectTo('suscripcion/index');
            } else {
                $_SESSION['error_message'] = "Error al crear la suscripción";
                $this->redirectTo('suscripcion/crear');
            }
        } catch (Exception $e) {
            error_log("Error en guardarSuscripcion: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al procesar la solicitud: " . $e->getMessage();
            $this->redirectTo('suscripcion/crear');
        }
    }

    /**
     * Muestra el formulario para editar una suscripción
     */
    public function editar()
    {
        // Título de la página
        $pageTitle = "Editar Suscripción";

        // Obtener el ID de la suscripción a editar
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener la suscripción por ID
        $suscripcion = $this->suscripcionModel->getById($id);

        if (!$suscripcion) {
            $_SESSION['error_message'] = "Suscripción no encontrada";
            $this->redirectTo('suscripcion/index');
            return;
        }

        // Obtener listas para los selectores
        $empresas = $this->empresaModel->getAll(['estado' => 'activa']);
        $planes = $this->planModel->getAll();

        // Cargar la vista
        require_once 'views/admin/suscripciones/editar.php';
    }

    /**
     * Procesa el formulario para actualizar una suscripción
     */
    public function actualizar()
    {
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('suscripcion/index');
            return;
        }

        // Verificar token CSRF
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo('suscripcion/index');
                return;
            }
        }

        // Verificar ID de la suscripción
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_POST['id'];

        // Validar campos obligatorios
        if (empty($_POST['empresa_id']) || empty($_POST['plan_id']) || empty($_POST['precio_total'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('suscripcion/editar?id=' . $id);
            return;
        }

        try {
            // Obtener la suscripción actual
            $suscripcion_actual = $this->suscripcionModel->getById($id);
            
            if (!$suscripcion_actual) {
                $_SESSION['error_message'] = "Suscripción no encontrada";
                $this->redirectTo('suscripcion/index');
                return;
            }

            // Crear y configurar el objeto Suscripcion
            $suscripcion = new Suscripcion();
            $suscripcion->setId($id);
            $suscripcion->setEmpresaId($_POST['empresa_id']);
            $suscripcion->setPlanId($_POST['plan_id']);
            $suscripcion->setNumeroSuscripcion($_POST['numero_suscripcion']);
            $suscripcion->setPeriodoFacturacion($_POST['periodo_facturacion']);
            $suscripcion->setFechaInicio($_POST['fecha_inicio']);
            $suscripcion->setFechaSiguienteFactura($_POST['fecha_siguiente_factura']);
            $suscripcion->setPrecioTotal($_POST['precio_total']);
            $suscripcion->setMoneda($_POST['moneda']);
            $suscripcion->setEstado($_POST['estado']);

            // Si se está cambiando el estado a "Cancelada", establecer fecha de cancelación
            if ($_POST['estado'] == 'Cancelada' && $suscripcion_actual->estado != 'Cancelada') {
                $suscripcion->setFechaCancelacion(date('Y-m-d'));
            } else {
                $suscripcion->setFechaCancelacion($suscripcion_actual->fecha_cancelacion);
            }

            // Actualizar la suscripción
            $result = $suscripcion->update();

            if ($result) {
                $_SESSION['success_message'] = "Suscripción actualizada correctamente";
                $this->redirectTo('suscripcion/index');
            } else {
                $_SESSION['error_message'] = "Error al actualizar la suscripción";
                $this->redirectTo('suscripcion/editar?id=' . $id);
            }
        } catch (Exception $e) {
            error_log("Error en actualizarSuscripcion: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al procesar la solicitud: " . $e->getMessage();
            $this->redirectTo('suscripcion/editar?id=' . $id);
        }
    }

    /**
     * Cambia el estado de una suscripción
     */
    public function cambiarEstado()
    {
        // Verificar parámetros necesarios
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_GET['id'];
        $estado = $_GET['estado'];

        // Validar estado
        $estados_validos = ['Activa', 'Suspendida', 'Cancelada', 'Finalizada', 'Pendiente'];
        if (!in_array($estado, $estados_validos)) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('suscripcion/index');
            return;
        }

        // Actualizar estado
        $motivo = "Cambio de estado manual desde el panel de administración";
        $resultado = $this->suscripcionModel->cambiarEstado($id, $estado, $motivo);

        if ($resultado) {
            $_SESSION['success_message'] = "Estado de suscripción actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado de la suscripción";
        }

        $this->redirectTo('suscripcion/index');
    }

    /**
     * Renueva una suscripción
     */
    public function renovar()
    {
        // Verificar parámetro necesario
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Renovar suscripción
        $resultado = $this->suscripcionModel->renovar($id);

        if ($resultado) {
            $_SESSION['success_message'] = "Suscripción renovada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al renovar la suscripción";
        }

        $this->redirectTo('suscripcion/index');
    }

    /**
     * Muestra el historial de cambios de una suscripción
     */
    public function historial()
    {
        // Título de la página
        $pageTitle = "Historial de Suscripción";

        // Verificar parámetro necesario
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener suscripción
        $suscripcion = $this->suscripcionModel->getById($id);

        if (!$suscripcion) {
            $_SESSION['error_message'] = "Suscripción no encontrada";
            $this->redirectTo('suscripcion/index');
            return;
        }

        // Obtener empresa y plan asociados
        $empresa = $this->empresaModel->getById($suscripcion->empresa_id);
        $plan = $this->planModel->getById($suscripcion->plan_id);

        // Incluir la vista
        require_once 'views/admin/suscripciones/historial.php';
    }

    /**
     * Genera una factura para una suscripción
     */
    public function generarFactura()
    {
        // Verificar parámetro necesario
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de suscripción no especificado";
            $this->redirectTo('suscripcion/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Crear factura
        $factura_id = $this->suscripcionModel->crearFactura($id);

        if ($factura_id) {
            $_SESSION['success_message'] = "Factura generada correctamente";
            $this->redirectTo('suscripcion/verFactura?id=' . $factura_id);
        } else {
            $_SESSION['error_message'] = "Error al generar la factura";
            $this->redirectTo('suscripcion/index');
        }
    }

    /**
     * Muestra una factura
     */
    public function verFactura()
    {
        // Implementar según necesidades
        $this->redirectTo('suscripcion/index');
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
     * Muestra un mensaje de error si el módulo de suscripciones no está disponible
     */
    private function modulo_no_disponible()
    {
        $_SESSION['error_message'] = "El módulo de suscripciones no está disponible";
        header("Location:" . base_url . "admin/dashboard");
        exit();
    }
}