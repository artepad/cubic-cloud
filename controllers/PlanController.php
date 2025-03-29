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
    public function save()
    {
        // Iniciar buffer de salida para evitar problemas de redirección
        ob_start();

        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar token CSRF
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $_SESSION['error_message'] = "Error de seguridad: token inválido";
                    $this->redirectTo("plan/crear");
                    exit();
                }
            }

            // Validar campos obligatorios
            if (
                empty($_POST['nombre']) || empty($_POST['tipo_plan']) ||
                !isset($_POST['precio_mensual']) || !isset($_POST['moneda'])
            ) {

                $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
                $this->redirectTo("plan/crear");
                exit();
            }

            try {
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
                $plan->setMaxEventos($_POST['max_eventos'] ?? 10);
                $plan->setMaxArtistas($_POST['max_artistas'] ?? 5);

                // Procesar características (actualizadas según la estructura de la base de datos)
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
                    $_SESSION['error_message'] = "Error al crear el plan";
                    $this->redirectTo("plan/crear");
                }
            } catch (Exception $e) {
                // Registrar el error
                error_log("Error guardando plan: " . $e->getMessage());
                $_SESSION['error_message'] = "Error al crear el plan: " . $e->getMessage();
                $this->redirectTo("plan/crear");
            }
        } else {
            // Si no es POST, redirigir al formulario
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
        // Limpiar el buffer de salida
        if (ob_get_length()) {
            ob_end_clean();
        }

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
    public function editar()
    {
        // Título de la página
        $pageTitle = "Editar Plan";

        // Obtener el ID del plan a editar
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de plan no especificado";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        $id = (int)$_GET['id'];

        // Obtener el plan por ID
        $plan = $this->planModel->getById($id);

        if (!$plan) {
            $_SESSION['error_message'] = "Plan no encontrado";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        // Convertir características de JSON a array
        $plan->caracteristicas_array = json_decode($plan->caracteristicas, true);

        // Incluir la vista
        require_once 'views/admin/planes/editar.php';
    }

    /**
     * Procesa el formulario de edición de plan
     */
    public function update()
    {
        // Iniciar buffer de salida
        ob_start();

        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Verificar token CSRF
            if (isset($_POST['csrf_token'])) {
                if (!validateCsrfToken($_POST['csrf_token'])) {
                    $_SESSION['error_message'] = "Error de seguridad: token inválido";
                    header("Location:" . base_url . "plan/index");
                    ob_end_flush();
                    exit();
                }
            }

            // Verificar ID del plan
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                $_SESSION['error_message'] = "ID de plan no especificado";
                header("Location:" . base_url . "plan/index");
                ob_end_flush();
                exit();
            }

            $id = (int)$_POST['id'];

            // Obtener el plan existente
            $plan = $this->planModel->getById($id);

            if (!$plan) {
                $_SESSION['error_message'] = "Plan no encontrado";
                header("Location:" . base_url . "plan/index");
                ob_end_flush();
                exit();
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
            $planObj->setMaxEventos($_POST['max_eventos'] ?? 10);
            $planObj->setMaxArtistas($_POST['max_artistas'] ?? 5);

            // Procesar características (actualizadas según la estructura de la base de datos)
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
                header("Location:" . base_url . "plan/index");
            } else {
                $_SESSION['error_message'] = "Error al actualizar el plan";
                header("Location:" . base_url . "plan/editar?id=" . $id);
            }
        } else {
            // Si no es POST, redirigir al listado
            header("Location:" . base_url . "plan/index");
        }

        ob_end_flush();
        exit();
    }

    /**
     * Elimina un plan
     */
    public function delete()
    {
        // Verificar si hay un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de plan no especificado";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        $id = (int)$_GET['id'];

        // Intentar eliminar el plan
        $resultado = $this->planModel->delete($id);

        if ($resultado) {
            $_SESSION['success_message'] = "Plan eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "No se puede eliminar el plan. Puede tener suscripciones asociadas";
        }

        header("Location:" . base_url . "plan/index");
        exit();
    }

    /**
     * Cambia el estado de un plan (Activo, Inactivo, Descontinuado)
     */
    public function cambiarEstado()
    {
        // Verificar parámetros
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        $id = (int)$_GET['id'];
        $estado = $_GET['estado'];

        // Validar estado
        $estados_validos = ['Activo', 'Inactivo', 'Descontinuado'];
        if (!in_array($estado, $estados_validos)) {
            $_SESSION['error_message'] = "Estado no válido";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        // Cambiar estado
        $resultado = $this->planModel->cambiarEstado($id, $estado);

        if ($resultado) {
            $_SESSION['success_message'] = "Estado del plan actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del plan";
        }

        header("Location:" . base_url . "plan/index");
        exit();
    }

    /**
     * Cambia la visibilidad de un plan (Si, No)
     */
    public function cambiarVisibilidad()
    {
        // Verificar parámetros
        if (!isset($_GET['id']) || !isset($_GET['visible'])) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        $id = (int)$_GET['id'];
        $visible = $_GET['visible'];

        // Validar visibilidad
        if ($visible !== 'Si' && $visible !== 'No') {
            $_SESSION['error_message'] = "Valor de visibilidad no válido";
            header("Location:" . base_url . "plan/index");
            exit();
        }

        // Cambiar visibilidad
        $resultado = $this->planModel->cambiarVisibilidad($id, $visible);

        if ($resultado) {
            $_SESSION['success_message'] = "Visibilidad del plan actualizada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar la visibilidad del plan";
        }

        header("Location:" . base_url . "plan/index");
        exit();
    }
}
