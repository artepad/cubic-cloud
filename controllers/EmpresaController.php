<?php

/**
 * EmpresaController
 * 
 * Controlador para la gestión completa de empresas en el sistema
 */
class EmpresaController
{
    private $empresaModel;
    private $usuarioModel;
    private $planModel;
    private $suscripcionModel;

    /**
     * Constructor
     * Inicializa los modelos necesarios y verifica autenticación
     */
    public function __construct()
    {
        // Cargar los modelos
        $this->empresaModel = new Empresa();
        $this->usuarioModel = new Usuario();
        $this->planModel = new Plan();

        // Verificar si existe el modelo de Suscripcion en el sistema
        if (class_exists('Suscripcion')) {
            $this->suscripcionModel = new Suscripcion();
        }

        // Verificar autenticación para todas las acciones
        if (!isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Acción para listar todas las empresas
     */
    public function index()
    {
        // Título de la página
        $pageTitle = "Gestión de Empresas";

        // Obtener todas las empresas sin filtros ni paginación
        $empresas = $this->empresaModel->getAll();

        // Cargar la vista
        require_once 'views/admin/empresas/index.php';
    }

    /**
     * Muestra el formulario para crear una nueva empresa
     */
    public function crear()
    {
        // Título de la página
        $pageTitle = "Crear Nueva Empresa";

        // Cargar la vista
        require_once 'views/admin/empresas/crear.php';
    }

    /**
     * Guarda una nueva empresa en la base de datos
     */
    public function guardar()
    {
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo('empresa/index');
            return;
        }

        // Verificar token CSRF
        if (isset($_POST['csrf_token'])) {
            if (!validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = "Error de seguridad: token inválido";
                $this->redirectTo('empresa/crear');
                return;
            }
        }

        // Validar campos obligatorios
        if (empty($_POST['nombre']) || empty($_POST['usuario_id']) || empty($_POST['direccion']) || empty($_POST['pais'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('empresa/crear');
            return;
        }

        try {
            // Verificar que el usuario exista y sea tipo ADMIN
            $usuario = $this->usuarioModel->getById($_POST['usuario_id']);
            if (!$usuario || $usuario->tipo_usuario != 'ADMIN') {
                $_SESSION['error_message'] = "El usuario seleccionado no existe o no es un administrador";
                $this->redirectTo('empresa/crear');
                return;
            }

            // Verificar que la identificación fiscal no esté duplicada (si se proporciona)
            if (
                !empty($_POST['identificacion_fiscal']) &&
                $this->empresaModel->identificacionExists($_POST['identificacion_fiscal'])
            ) {
                $_SESSION['error_message'] = "La identificación fiscal ya está registrada para otra empresa";
                $this->redirectTo('empresa/crear');
                return;
            }

            // Crear y configurar el objeto Empresa
            $empresa = new Empresa();
            $empresa->setUsuarioId($_POST['usuario_id']);
            $empresa->setNombre($_POST['nombre']);
            $empresa->setIdentificacionFiscal($_POST['identificacion_fiscal'] ?? '');
            $empresa->setDireccion($_POST['direccion']);
            $empresa->setTelefono($_POST['telefono'] ?? '');
            $empresa->setEmailContacto($_POST['email_contacto'] ?? '');

            // Configurar datos de facturación como campos individuales
            $empresa->setRazonSocialFacturacion($_POST['razon_social_facturacion'] ?? '');
            $empresa->setDireccionFacturacion($_POST['direccion_facturacion'] ?? '');
            $empresa->setCiudadFacturacion($_POST['ciudad_facturacion'] ?? '');
            $empresa->setCodigoPostal($_POST['codigo_postal'] ?? '');
            $empresa->setContactoFacturacion($_POST['contacto_facturacion'] ?? '');
            $empresa->setEmailFacturacion($_POST['email_facturacion'] ?? '');

            $empresa->setPais($_POST['pais']);
            $empresa->setCodigoPais($_POST['codigo_pais'] ?? '');
            $empresa->setTipoMoneda($_POST['tipo_moneda'] ?? 'CLP');
            $empresa->setEstado($_POST['estado'] ?? 'activa');

            // Configuración de demo
            $es_demo = isset($_POST['es_demo']) ? 'Si' : 'No';
            $empresa->setEsDemo($es_demo);

            if ($es_demo == 'Si') {
                $empresa->setDemoInicio($_POST['demo_inicio'] ?? null);
                $empresa->setDemoFin($_POST['demo_fin'] ?? null);
            }

            // Procesar imágenes si se han subido
            $upload_dir = 'uploads/empresas/';

            // Crear el directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Procesar imagen de empresa
            if (isset($_FILES['imagen_empresa']) && $_FILES['imagen_empresa']['error'] == 0) {
                $imagen_empresa = $this->processUploadedFile($_FILES['imagen_empresa'], $upload_dir);
                if ($imagen_empresa) {
                    $empresa->setImagenEmpresa($imagen_empresa);
                }
            }

            // Procesar imagen de documento
            if (isset($_FILES['imagen_documento']) && $_FILES['imagen_documento']['error'] == 0) {
                $imagen_documento = $this->processUploadedFile($_FILES['imagen_documento'], $upload_dir);
                if ($imagen_documento) {
                    $empresa->setImagenDocumento($imagen_documento);
                }
            }

            // Procesar imagen de firma
            if (isset($_FILES['imagen_firma']) && $_FILES['imagen_firma']['error'] == 0) {
                $imagen_firma = $this->processUploadedFile($_FILES['imagen_firma'], $upload_dir);
                if ($imagen_firma) {
                    $empresa->setImagenFirma($imagen_firma);
                }
            }

            // Guardar la empresa
            $empresa_id = $empresa->save();

            if ($empresa_id) {
                // Si se ha seleccionado un plan, crear la suscripción
                if (isset($_POST['plan_id']) && !empty($_POST['plan_id']) && isset($this->suscripcionModel)) {
                    $this->crearSuscripcion($empresa_id, $_POST['plan_id'], $_POST['periodo'] ?? 'Mensual');
                }

                $_SESSION['success_message'] = "Empresa creada correctamente";
                $this->redirectTo('empresa/index');
            } else {
                $_SESSION['error_message'] = "Error al crear la empresa";
                $this->redirectTo('empresa/crear');
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error guardando empresa: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al crear la empresa: " . $e->getMessage();
            $this->redirectTo('empresa/crear');
        }
    }

    /**
     * Procesa un archivo subido y lo mueve al directorio especificado
     * 
     * @param array $file Archivo subido ($_FILES['nombre'])
     * @param string $upload_dir Directorio donde guardar el archivo
     * @return string|false Ruta del archivo guardado o false si falla
     */
    private function processUploadedFile($file, $upload_dir)
    {
        // Verificar que el archivo sea válido
        if ($file['error'] != 0) {
            return false;
        }

        // Verificar el tipo de archivo (solo imágenes)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }

        // Verificar tamaño máximo (2MB)
        if ($file['size'] > 2097152) { // 2MB en bytes
            return false;
        }

        // Generar nombre único para el archivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('empresa_') . '.' . $extension;
        $filepath = $upload_dir . $filename;

        // Mover archivo al directorio de destino
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filepath;
        }

        return false;
    }

    /**
     * Elimina una imagen si existe
     * 
     * @param string $image_path Ruta de la imagen a eliminar
     */
    private function deleteImage($image_path)
    {
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }
    }




    /**
     * Muestra el formulario para editar una empresa existente
     * 
     * @param mixed $id ID de la empresa a editar (puede ser entero o array con parámetros)
     */
    public function editar($id = null)
    {
        // Si se pasó un array de parámetros en lugar de un ID directo
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else {
            $id = (int)$id;
        }

        if (!$id) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        // Obtener la empresa por ID
        $empresa = $this->empresaModel->getById($id);

        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Título de la página
        $pageTitle = "Editar Empresa";

        // Cargar la vista
        require_once 'views/admin/empresas/editar.php';
    }

    /**
     * Actualiza los datos de una empresa existente
     */
    public function actualizar()
    {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['error_message'] = "Error de seguridad. Intente nuevamente.";
            $this->redirectTo('empresa/index');
            return;
        }

        if (!isset($_POST['id'])) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_POST['id'];
        $empresa_actual = $this->empresaModel->getById($id);

        if (!$empresa_actual) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Verificar campos obligatorios
        if (empty($_POST['nombre']) || empty($_POST['usuario_id']) || empty($_POST['direccion']) || empty($_POST['pais'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('empresa/editar/' . $id);
            return;
        }

        try {
            // Verificar que el usuario exista y sea tipo ADMIN
            $usuario = $this->usuarioModel->getById($_POST['usuario_id']);
            if (!$usuario || $usuario->tipo_usuario != 'ADMIN') {
                $_SESSION['error_message'] = "El usuario seleccionado no existe o no es un administrador";
                $this->redirectTo('empresa/editar/' . $id);
                return;
            }

            // Verificar que la identificación fiscal no esté duplicada (si se proporciona)
            if (
                !empty($_POST['identificacion_fiscal']) &&
                $_POST['identificacion_fiscal'] !== $empresa_actual->identificacion_fiscal &&
                $this->empresaModel->identificacionExists($_POST['identificacion_fiscal'], $id)
            ) {
                $_SESSION['error_message'] = "La identificación fiscal ya está registrada para otra empresa";
                $this->redirectTo('empresa/editar/' . $id);
                return;
            }

            // Crear y configurar el objeto Empresa
            $empresa = new Empresa();
            $empresa->setId($id);
            $empresa->setUsuarioId($_POST['usuario_id']);
            $empresa->setNombre($_POST['nombre']);
            $empresa->setIdentificacionFiscal($_POST['identificacion_fiscal'] ?? '');
            $empresa->setDireccion($_POST['direccion']);
            $empresa->setTelefono($_POST['telefono'] ?? '');
            $empresa->setEmailContacto($_POST['email_contacto'] ?? '');

            // Configurar datos de facturación
            $empresa->setRazonSocialFacturacion($_POST['razon_social_facturacion'] ?? '');
            $empresa->setDireccionFacturacion($_POST['direccion_facturacion'] ?? '');
            $empresa->setCiudadFacturacion($_POST['ciudad_facturacion'] ?? '');
            $empresa->setCodigoPostal($_POST['codigo_postal'] ?? '');
            $empresa->setContactoFacturacion($_POST['contacto_facturacion'] ?? '');
            $empresa->setEmailFacturacion($_POST['email_facturacion'] ?? '');

            $empresa->setPais($_POST['pais']);
            $empresa->setCodigoPais($_POST['codigo_pais'] ?? '');
            $empresa->setTipoMoneda($_POST['tipo_moneda'] ?? 'CLP');
            $empresa->setEstado($_POST['estado'] ?? 'activa');

            // Configuración de demo
            $es_demo = isset($_POST['es_demo']) ? 'Si' : 'No';
            $empresa->setEsDemo($es_demo);

            if ($es_demo == 'Si') {
                $empresa->setDemoInicio($_POST['demo_inicio'] ?? null);
                $empresa->setDemoFin($_POST['demo_fin'] ?? null);
            } else {
                $empresa->setDemoInicio(null);
                $empresa->setDemoFin(null);
            }

            // Procesar imágenes
            $upload_dir = 'uploads/empresas/';

            // Crear el directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Procesar imagen de empresa
            if (isset($_FILES['imagen_empresa']) && $_FILES['imagen_empresa']['error'] == 0) {
                // Eliminar imagen anterior si existe
                if (!empty($empresa_actual->imagen_empresa)) {
                    $this->deleteImage($empresa_actual->imagen_empresa);
                }

                $imagen_empresa = $this->processUploadedFile($_FILES['imagen_empresa'], $upload_dir);
                if ($imagen_empresa) {
                    $empresa->setImagenEmpresa($imagen_empresa);
                } else {
                    $empresa->setImagenEmpresa($empresa_actual->imagen_empresa);
                }
            } else {
                // Mantener la imagen existente a menos que se haya marcado para eliminar
                if (isset($_POST['eliminar_imagen_empresa']) && $_POST['eliminar_imagen_empresa'] == 1) {
                    $this->deleteImage($empresa_actual->imagen_empresa);
                    $empresa->setImagenEmpresa('');
                } else {
                    $empresa->setImagenEmpresa($empresa_actual->imagen_empresa);
                }
            }

            // Procesar imagen de documento
            if (isset($_FILES['imagen_documento']) && $_FILES['imagen_documento']['error'] == 0) {
                // Eliminar imagen anterior si existe
                if (!empty($empresa_actual->imagen_documento)) {
                    $this->deleteImage($empresa_actual->imagen_documento);
                }

                $imagen_documento = $this->processUploadedFile($_FILES['imagen_documento'], $upload_dir);
                if ($imagen_documento) {
                    $empresa->setImagenDocumento($imagen_documento);
                } else {
                    $empresa->setImagenDocumento($empresa_actual->imagen_documento);
                }
            } else {
                // Mantener la imagen existente a menos que se haya marcado para eliminar
                if (isset($_POST['eliminar_imagen_documento']) && $_POST['eliminar_imagen_documento'] == 1) {
                    $this->deleteImage($empresa_actual->imagen_documento);
                    $empresa->setImagenDocumento('');
                } else {
                    $empresa->setImagenDocumento($empresa_actual->imagen_documento);
                }
            }

            // Procesar imagen de firma
            if (isset($_FILES['imagen_firma']) && $_FILES['imagen_firma']['error'] == 0) {
                // Eliminar imagen anterior si existe
                if (!empty($empresa_actual->imagen_firma)) {
                    $this->deleteImage($empresa_actual->imagen_firma);
                }

                $imagen_firma = $this->processUploadedFile($_FILES['imagen_firma'], $upload_dir);
                if ($imagen_firma) {
                    $empresa->setImagenFirma($imagen_firma);
                } else {
                    $empresa->setImagenFirma($empresa_actual->imagen_firma);
                }
            } else {
                // Mantener la imagen existente a menos que se haya marcado para eliminar
                if (isset($_POST['eliminar_imagen_firma']) && $_POST['eliminar_imagen_firma'] == 1) {
                    $this->deleteImage($empresa_actual->imagen_firma);
                    $empresa->setImagenFirma('');
                } else {
                    $empresa->setImagenFirma($empresa_actual->imagen_firma);
                }
            }

            // Actualizar la empresa
            $result = $empresa->update();

            if ($result) {
                $_SESSION['success_message'] = "Empresa actualizada correctamente";
                $this->redirectTo('empresa/index');
            } else {
                $_SESSION['error_message'] = "Error al actualizar la empresa";
                $this->redirectTo('empresa/editar/' . $id);
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error actualizando empresa: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al actualizar la empresa: " . $e->getMessage();
            $this->redirectTo('empresa/editar/' . $id);
        }
    }

    /**
     * Cambia el estado de una empresa (activa/suspendida)
     * 
     * @param mixed $id ID de la empresa o array con parámetros
     * @param string $estado Nuevo estado ('activa' o 'suspendida')
     */
    public function cambiarEstado($id = null, $estado = null)
    {
        // Si se pasó un array de parámetros
        if (is_array($id) && isset($id['id']) && isset($id['estado'])) {
            $estado = $id['estado'];
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
            $estado = null;
        } else {
            $id = (int)$id;
        }

        if (!$id || !$estado) {
            $_SESSION['error_message'] = "Parámetros incorrectos";
            $this->redirectTo('empresa/index');
            return;
        }

        // Validar estado
        if (!in_array($estado, ['activa', 'suspendida'])) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('empresa/index');
            return;
        }

        // Verificar que la empresa exista
        $empresa = $this->empresaModel->getById($id);
        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Cambiar estado
        $result = $this->empresaModel->cambiarEstado($id, $estado);

        if ($result) {
            $_SESSION['success_message'] = "Estado de la empresa actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado de la empresa";
        }

        $this->redirectTo('empresa/index');
    }

    /**
     * Elimina una empresa
     * 
     * @param mixed $id ID de la empresa a eliminar
     */
    public function eliminar($id = null)
    {
        // Si se pasó un array de parámetros
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else {
            $id = (int)$id;
        }

        if (!$id) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        // Verificar que la empresa exista
        $empresa = $this->empresaModel->getById($id);
        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Eliminar imágenes de la empresa
        if (!empty($empresa->imagen_empresa)) {
            $this->deleteImage($empresa->imagen_empresa);
        }
        if (!empty($empresa->imagen_documento)) {
            $this->deleteImage($empresa->imagen_documento);
        }
        if (!empty($empresa->imagen_firma)) {
            $this->deleteImage($empresa->imagen_firma);
        }

        // Eliminar la empresa
        $result = $this->empresaModel->delete($id);

        if ($result) {
            $_SESSION['success_message'] = "Empresa eliminada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar la empresa";
        }

        $this->redirectTo('empresa/index');
    }

    /**
     * Crea una suscripción para una empresa recién creada
     * 
     * @param int $empresa_id ID de la empresa
     * @param int $plan_id ID del plan
     * @param string $periodo Período de facturación (Mensual, Semestral, Anual)
     * @return int|bool ID de la suscripción o false si falla
     */
    private function crearSuscripcion($empresa_id, $plan_id, $periodo = 'Mensual')
    {
        // Verificar que existe el modelo de Suscripcion
        if (!isset($this->suscripcionModel)) {
            error_log("Error: No se pudo crear la suscripción. Modelo no disponible.");
            return false;
        }

        try {
            // Obtener información del plan
            $plan = $this->planModel->getById($plan_id);
            if (!$plan) {
                return false;
            }

            // Obtener precio total según el período
            $precio_total = 0;
            switch ($periodo) {
                case 'Mensual':
                    $precio_total = $plan->precio_mensual;
                    break;
                case 'Semestral':
                    $precio_total = $plan->precio_semestral;
                    break;
                case 'Anual':
                    $precio_total = $plan->precio_anual;
                    break;
            }

            // Generar número único de suscripción
            $numero_suscripcion = 'SUB-' . date('Ymd') . '-' . $empresa_id;

            // Calcular fechas
            $fecha_inicio = date('Y-m-d');
            $fecha_siguiente_factura = null;
            $fecha_fin = null;

            switch ($periodo) {
                case 'Mensual':
                    $fecha_siguiente_factura = date('Y-m-d', strtotime('+1 month'));
                    break;
                case 'Semestral':
                    $fecha_siguiente_factura = date('Y-m-d', strtotime('+6 months'));
                    break;
                case 'Anual':
                    $fecha_siguiente_factura = date('Y-m-d', strtotime('+1 year'));
                    break;
            }

            // Configurar la suscripción
            $suscripcion = $this->suscripcionModel;
            $suscripcion->setEmpresaId($empresa_id);
            $suscripcion->setPlanId($plan_id);
            $suscripcion->setNumeroSuscripcion($numero_suscripcion);
            $suscripcion->setPeriodoFacturacion($periodo);
            $suscripcion->setFechaInicio($fecha_inicio);
            $suscripcion->setFechaSiguienteFactura($fecha_siguiente_factura);
            $suscripcion->setFechaFin($fecha_fin);
            $suscripcion->setPrecioTotal($precio_total);
            $suscripcion->setMoneda($plan->moneda);
            $suscripcion->setEstado('Activa');

            // Guardar la suscripción
            return $suscripcion->save();
        } catch (Exception $e) {
            error_log("Error creando suscripción: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Muestra los detalles de una empresa
     */
    public function ver($id = null)
    {
        // Si se pasó un array de parámetros en lugar de un ID directo
        if (is_array($id) && isset($id['id'])) {
            $id = (int)$id['id'];
        } elseif (is_array($id)) {
            $id = null;
        } else {
            $id = (int)$id;
        }

        if (!$id) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        // Obtener la empresa por ID
        $empresa = $this->empresaModel->getById($id);

        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Obtener información del administrador
        $admin = $this->usuarioModel->getById($empresa->usuario_id);

        // Título de la página
        $pageTitle = "Detalles de Empresa";

        // Cargar la vista
        require_once 'views/admin/empresas/ver.php';
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
}
