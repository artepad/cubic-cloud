<?php

/**
 * PlanController
 * 
 * Controlador para la gestión de planes de suscripción
 */
class PlanController
{
    private $planModel;

    /**
     * Constructor
     * Inicializa el modelo de Plan y verifica autenticación
     */
    public function __construct()
    {
        // Cargar el modelo
        $this->planModel = new Plan();

        // Verificar autenticación para todas las acciones
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    public function index()
    {
        // Título de la página
        $pageTitle = "Gestión de Planes";

        // Obtener todos los planes sin filtros
        $planes = $this->planModel->getAll();

        // Incluir la vista
        require_once 'views/admin/planes/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo plan
     */
    public function crear()
    {
        // Título de la página
        $pageTitle = "Crear Nuevo Plan";

        // Incluir la vista
        require_once 'views/admin/planes/crear.php';
    }

    /**
     * Procesa el formulario de creación de plan
     */
    public function guardar()
    {
        try {
            // Verificar si se ha enviado el formulario
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirectTo("plan/crear");
                return;
            }

            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo("plan/crear");
                return;
            }

            // Validar campos obligatorios
            if (
                empty($_POST['nombre']) || empty($_POST['tipo_plan']) ||
                !isset($_POST['precio_mensual']) || !isset($_POST['moneda'])
            ) {
                $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
                $this->redirectTo("plan/crear");
                return;
            }

            // Crear y configurar el objeto Plan
            $plan = new Plan();
            $plan->setNombre($_POST['nombre']);
            $plan->setDescripcion($_POST['descripcion'] ?? '');
            $plan->setTipoPlan($_POST['tipo_plan']);
            $plan->setPrecioMensual($_POST['precio_mensual']);
            $plan->setPrecioSemestral($_POST['precio_semestral'] ?? $_POST['precio_mensual']);
            $plan->setPrecioAnual($_POST['precio_anual'] ?? $_POST['precio_mensual']);
            $plan->setMoneda($_POST['moneda']);
            $plan->setMaxUsuarios($_POST['max_usuarios'] ?? 1);
            $plan->setMaxArtistas($_POST['max_artistas'] ?? 5);
            $plan->setMaxEventos($_POST['max_eventos'] ?? 10);

            // Procesar características
            $caracteristicas = [
                'soporte_prioritario' => isset($_POST['soporte_prioritario']) ? true : false,
                'soporte_telefonico' => isset($_POST['soporte_telefonico']) ? true : false,
                'copias_seguridad' => isset($_POST['copias_seguridad']) ? true : false,
                'importar_contactos' => isset($_POST['importar_contactos']) ? true : false,
                'exportar_pdf' => isset($_POST['exportar_pdf']) ? true : false,
                'reportes_avanzados' => isset($_POST['reportes_avanzados']) ? true : false
            ];

            // Si hay características personalizadas, agregarlas
            if (!empty($_POST['caracteristicas_adicionales'])) {
                $caracteristicas['adicionales'] = $_POST['caracteristicas_adicionales'];
            }

            $plan->setCaracteristicas(json_encode($caracteristicas));
            $plan->setEstado($_POST['estado'] ?? 'Activo');
            $plan->setVisible($_POST['visible'] ?? 'Si');

            // Guardar el plan
            $resultado = $plan->save();

            if ($resultado) {
                $_SESSION['success_message'] = "Plan creado correctamente";
                $this->redirectTo("plan/index");
            } else {
                $_SESSION['error_message'] = "Error al crear el plan. Verifique los registros del servidor.";
                $this->redirectTo("plan/crear");
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error guardando plan: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al crear el plan: " . $e->getMessage();
            $this->redirectTo("plan/crear");
        }
    }

    /**
     * Método auxiliar para garantizar una redirección robusta
     * 
     * @param string $path Ruta relativa a la URL base
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
     * Muestra el formulario para editar un plan existente
     */
    public function editar($id = null)
    {
        // Título de la página
        $pageTitle = "Editar Plan";

        // Si se pasó un array de parámetros en lugar de un ID directo
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else if ($id === null && isset($_GET['id'])) {
            // Para mantener compatibilidad con el formato anterior
            $id = (int)$_GET['id'];
        } else {
            $id = (int)$id;
        }

        // Verificar que el ID sea válido
        if ($id <= 0) {
            $_SESSION['error_message'] = "ID de plan no especificado o inválido";
            $this->redirectTo("plan/index");
            return;
        }

        // Obtener el plan por ID
        $plan = $this->planModel->getById($id);

        if (!$plan) {
            $_SESSION['error_message'] = "Plan no encontrado";
            $this->redirectTo("plan/index");
            return;
        }

        // Convertir características de JSON a array
        $plan->caracteristicas_array = json_decode($plan->caracteristicas, true);

        // Incluir la vista
        require_once 'views/admin/planes/editar.php';
    }

    /**
     * Procesa el formulario de edición de plan
     */
    public function actualizar()
    {
        try {
            // Verificar si se ha enviado el formulario
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->redirectTo("plan/index");
                return;
            }

            // Verificar token CSRF
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo("plan/index");
                return;
            }

            // Verificar ID del plan
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $_SESSION['error_message'] = "ID de plan no especificado";
                $this->redirectTo("plan/index");
                return;
            }

            $id = (int)$_POST['id'];

            // Obtener el plan existente
            $plan = $this->planModel->getById($id);

            if (!$plan) {
                $_SESSION['error_message'] = "Plan no encontrado";
                $this->redirectTo("plan/index");
                return;
            }

            // Crear y configurar el objeto Plan
            $planObj = new Plan();
            $planObj->setId($id);
            $planObj->setNombre($_POST['nombre']);
            $planObj->setDescripcion($_POST['descripcion'] ?? '');
            $planObj->setTipoPlan($_POST['tipo_plan']);
            $planObj->setPrecioMensual($_POST['precio_mensual']);
            $planObj->setPrecioSemestral($_POST['precio_semestral'] ?? $_POST['precio_mensual']);
            $planObj->setPrecioAnual($_POST['precio_anual'] ?? $_POST['precio_mensual']);
            $planObj->setMoneda($_POST['moneda']);
            $planObj->setMaxUsuarios($_POST['max_usuarios'] ?? 1);
            $planObj->setMaxArtistas($_POST['max_artistas'] ?? 5);
            $planObj->setMaxEventos($_POST['max_eventos'] ?? 10);

            // Procesar características
            $caracteristicas = [
                'soporte_prioritario' => isset($_POST['soporte_prioritario']) ? true : false,
                'soporte_telefonico' => isset($_POST['soporte_telefonico']) ? true : false,
                'copias_seguridad' => isset($_POST['copias_seguridad']) ? true : false,
                'importar_contactos' => isset($_POST['importar_contactos']) ? true : false,
                'exportar_pdf' => isset($_POST['exportar_pdf']) ? true : false,
                'reportes_avanzados' => isset($_POST['reportes_avanzados']) ? true : false
            ];

            // Si hay características personalizadas, agregarlas
            if (!empty($_POST['caracteristicas_adicionales'])) {
                $caracteristicas['adicionales'] = $_POST['caracteristicas_adicionales'];
            }

            $planObj->setCaracteristicas(json_encode($caracteristicas));
            $planObj->setEstado($_POST['estado'] ?? 'Activo');
            $planObj->setVisible($_POST['visible'] ?? 'Si');

            // Actualizar el plan
            $resultado = $planObj->update();

            if ($resultado) {
                $_SESSION['success_message'] = "Plan actualizado correctamente";
                $this->redirectTo("plan/index");
            } else {
                $_SESSION['error_message'] = "Error al actualizar el plan";
                $this->redirectTo("plan/editar/" . $id);
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error actualizando plan: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al actualizar el plan: " . $e->getMessage();
            $this->redirectTo("plan/index");
        }
    }

    /**
     * Elimina un plan
     */
    public function eliminar($id = null)
    {
        // Si se pasó un array de parámetros en lugar de un ID directo
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else if ($id === null && isset($_GET['id'])) {
            // Para mantener compatibilidad con el formato anterior
            $id = (int)$_GET['id'];
        } else {
            $id = (int)$id;
        }

        // Verificar que el ID sea válido
        if ($id <= 0) {
            $_SESSION['error_message'] = "ID de plan no especificado o inválido";
            $this->redirectTo("plan/index");
            return;
        }

        // Intentar eliminar el plan
        $resultado = $this->planModel->delete($id);

        if ($resultado) {
            $_SESSION['success_message'] = "Plan eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "No se puede eliminar el plan. Puede tener suscripciones asociadas";
        }

        $this->redirectTo("plan/index");
    }

    /**
     * Cambia el estado de un plan (Activo, Inactivo, Descontinuado)
     */
    public function cambiarEstado($id = null, $estado = null)
    {
        // Si se pasó un array de parámetros en lugar de ID y estado directos
        if (is_array($id) && isset($id['id']) && isset($id['estado'])) {
            $estado = $id['estado'];
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
            $estado = null;
        } else if ($id === null || $estado === null) {
            // Para mantener compatibilidad con el formato anterior (query string)
            if (isset($_GET['id']) && isset($_GET['estado'])) {
                $id = (int)$_GET['id'];
                $estado = $_GET['estado'];
            } else {
                $_SESSION['error_message'] = "Parámetros insuficientes";
                $this->redirectTo("plan/index");
                return;
            }
        }

        // Validar estado
        $estados_validos = ['Activo', 'Inactivo', 'Descontinuado'];
        if (!in_array($estado, $estados_validos)) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo("plan/index");
            return;
        }

        // Cambiar estado
        $resultado = $this->planModel->cambiarEstado($id, $estado);

        if ($resultado) {
            $_SESSION['success_message'] = "Estado del plan actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del plan";
        }

        $this->redirectTo("plan/index");
    }

    /**
     * Muestra los detalles de un plan específico
     * 
     * @param mixed $id ID del plan a ver (puede ser entero o array con parámetros)
     */
    public function ver($id = null)
    {
        // Título de la página
        $pageTitle = "Detalles del Plan";

        // Si se pasó un array de parámetros en lugar de un ID directo
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else if ($id === null && isset($_GET['id'])) {
            // Para mantener compatibilidad con el formato anterior
            $id = (int)$_GET['id'];
        } else {
            $id = (int)$id;
        }

        if ($id <= 0) {
            $_SESSION['error_message'] = "ID de plan no especificado o inválido";
            $this->redirectTo("plan/index");
            return;
        }

        // Obtener el plan por ID
        $plan = $this->planModel->getById($id);

        if (!$plan) {
            $_SESSION['error_message'] = "Plan no encontrado";
            $this->redirectTo("plan/index");
            return;
        }

        // Convertir características de JSON a array
        $plan->caracteristicas_array = json_decode($plan->caracteristicas, true);

        // Incluir la vista
        require_once 'views/admin/planes/ver.php';
    }

    /**
     * Cambia la visibilidad de un plan (Si, No)
     */
    public function cambiarVisibilidad($id = null, $visible = null)
    {
        // Si se pasó un array de parámetros en lugar de ID y visible directos
        if (is_array($id) && isset($id['id']) && isset($id['visibilidad'])) {
            $visible = $id['visibilidad'];
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
            $visible = null;
        } else if ($id === null || $visible === null) {
            // Para mantener compatibilidad con el formato anterior
            if (isset($_GET['id']) && isset($_GET['visible'])) {
                $id = (int)$_GET['id'];
                $visible = $_GET['visible'];
            } else {
                $_SESSION['error_message'] = "Parámetros insuficientes";
                $this->redirectTo("plan/index");
                return;
            }
        }

        // Validar visibilidad
        if ($visible !== 'Si' && $visible !== 'No') {
            $_SESSION['error_message'] = "Valor de visibilidad no válido";
            $this->redirectTo("plan/index");
            return;
        }

        // Cambiar visibilidad
        $resultado = $this->planModel->cambiarVisibilidad($id, $visible);

        if ($resultado) {
            $_SESSION['success_message'] = "Visibilidad del plan actualizada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar la visibilidad del plan";
        }

        $this->redirectTo("plan/index");
    }
}
