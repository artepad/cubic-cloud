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

        // Procesar filtros si existen
        $filters = [];
        if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
            $filters['busqueda'] = trim($_GET['busqueda']);
        }
        if (isset($_GET['estado']) && !empty($_GET['estado'])) {
            $filters['estado'] = $_GET['estado'];
        }
        if (isset($_GET['es_demo']) && !empty($_GET['es_demo'])) {
            $filters['es_demo'] = $_GET['es_demo'];
        }

        // Configuración de paginación
        $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
        $elementosPorPagina = 10;
        $offset = ($pagina - 1) * $elementosPorPagina;

        // Obtener empresas según filtros y paginación
        $empresas = $this->empresaModel->getAll($filters, $elementosPorPagina, $offset);
        $total_empresas = $this->empresaModel->countAll($filters);

        // Cálculos para la paginación
        $total_paginas = ceil($total_empresas / $elementosPorPagina);
        $paginacion = [
            'pagina_actual' => $pagina,
            'total_paginas' => $total_paginas,
            'total_elementos' => $total_empresas
        ];

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
    public function save()
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
            if (!empty($_POST['identificacion_fiscal']) && 
                $this->empresaModel->identificacionExists($_POST['identificacion_fiscal'])) {
                $_SESSION['error_message'] = "La identificación fiscal ya está registrada para otra empresa";
                $this->redirectTo('empresa/crear');
                return;
            }

            // Procesar datos de facturación (si existen)
            $datos_facturacion = isset($_POST['datos_facturacion']) ? $_POST['datos_facturacion'] : [];

            // Crear y configurar el objeto Empresa
            $empresa = new Empresa();
            $empresa->setUsuarioId($_POST['usuario_id']);
            $empresa->setNombre($_POST['nombre']);
            $empresa->setIdentificacionFiscal($_POST['identificacion_fiscal'] ?? '');
            $empresa->setDireccion($_POST['direccion']);
            $empresa->setTelefono($_POST['telefono'] ?? '');
            $empresa->setEmailContacto($_POST['email_contacto'] ?? '');
            $empresa->setDatosFacturacion($datos_facturacion);
            $empresa->setPais($_POST['pais']);
            $empresa->setCodigoPais($_POST['codigo_pais'] ?? '');
            $empresa->setLimiteUsuarios($_POST['limite_usuarios'] ?? 1);
            $empresa->setLimiteEventos($_POST['limite_eventos'] ?? 10);
            $empresa->setLimiteArtistas($_POST['limite_artistas'] ?? 5);
            $empresa->setLimiteAlmacenamiento(100); // Valor por defecto
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
     * Muestra el formulario para editar una empresa
     */
    public function editar()
    {
        // Título de la página
        $pageTitle = "Editar Empresa";

        // Obtener el ID de la empresa a editar
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener la empresa por ID
        $empresa = $this->empresaModel->getById($id);

        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Cargar la vista
        require_once 'views/admin/empresas/editar.php';
    }

    /**
     * Actualiza una empresa existente
     */
    public function update()
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
                $this->redirectTo('empresa/index');
                return;
            }
        }

        // Verificar ID de la empresa
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_POST['id'];

        // Obtener la empresa existente
        $empresa_actual = $this->empresaModel->getById($id);

        if (!$empresa_actual) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Validar campos obligatorios
        if (empty($_POST['nombre']) || empty($_POST['usuario_id']) || empty($_POST['direccion']) || empty($_POST['pais'])) {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('empresa/editar?id=' . $id);
            return;
        }

        try {
            // Verificar que el usuario exista y sea tipo ADMIN
            $usuario = $this->usuarioModel->getById($_POST['usuario_id']);
            if (!$usuario || $usuario->tipo_usuario != 'ADMIN') {
                $_SESSION['error_message'] = "El usuario seleccionado no existe o no es un administrador";
                $this->redirectTo('empresa/editar?id=' . $id);
                return;
            }

            // Verificar que la identificación fiscal no esté duplicada (si se proporciona)
            if (!empty($_POST['identificacion_fiscal']) && 
                $this->empresaModel->identificacionExists($_POST['identificacion_fiscal'], $id)) {
                $_SESSION['error_message'] = "La identificación fiscal ya está registrada para otra empresa";
                $this->redirectTo('empresa/editar?id=' . $id);
                return;
            }

            // Procesar datos de facturación (si existen)
            $datos_facturacion = isset($_POST['datos_facturacion']) ? $_POST['datos_facturacion'] : [];

            // Crear y configurar el objeto Empresa
            $empresa = new Empresa();
            $empresa->setId($id);
            $empresa->setUsuarioId($_POST['usuario_id']);
            $empresa->setNombre($_POST['nombre']);
            $empresa->setIdentificacionFiscal($_POST['identificacion_fiscal'] ?? '');
            $empresa->setDireccion($_POST['direccion']);
            $empresa->setTelefono($_POST['telefono'] ?? '');
            $empresa->setEmailContacto($_POST['email_contacto'] ?? '');
            $empresa->setDatosFacturacion($datos_facturacion);
            $empresa->setPais($_POST['pais']);
            $empresa->setCodigoPais($_POST['codigo_pais'] ?? '');
            $empresa->setLimiteUsuarios($_POST['limite_usuarios'] ?? 1);
            $empresa->setLimiteEventos($_POST['limite_eventos'] ?? 10);
            $empresa->setLimiteArtistas($_POST['limite_artistas'] ?? 5);
            $empresa->setLimiteAlmacenamiento($empresa_actual->limite_almacenamiento); // Mantener el valor actual
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

            // Mantener imágenes actuales si no se han cambiado
            $empresa->setImagenEmpresa($empresa_actual->imagen_empresa);
            $empresa->setImagenDocumento($empresa_actual->imagen_documento);
            $empresa->setImagenFirma($empresa_actual->imagen_firma);

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
                    // Eliminar imagen anterior si existe
                    if (!empty($empresa_actual->imagen_empresa) && file_exists($empresa_actual->imagen_empresa)) {
                        unlink($empresa_actual->imagen_empresa);
                    }
                    $empresa->setImagenEmpresa($imagen_empresa);
                }
            } elseif (isset($_POST['eliminar_imagen_empresa'])) {
                // Eliminar imagen si se ha marcado la casilla
                if (!empty($empresa_actual->imagen_empresa) && file_exists($empresa_actual->imagen_empresa)) {
                    unlink($empresa_actual->imagen_empresa);
                }
                $empresa->setImagenEmpresa('');
            }

            // Procesar imagen de documento
            if (isset($_FILES['imagen_documento']) && $_FILES['imagen_documento']['error'] == 0) {
                $imagen_documento = $this->processUploadedFile($_FILES['imagen_documento'], $upload_dir);
                if ($imagen_documento) {
                    // Eliminar imagen anterior si existe
                    if (!empty($empresa_actual->imagen_documento) && file_exists($empresa_actual->imagen_documento)) {
                        unlink($empresa_actual->imagen_documento);
                    }
                    $empresa->setImagenDocumento($imagen_documento);
                }
            } elseif (isset($_POST['eliminar_imagen_documento'])) {
                // Eliminar imagen si se ha marcado la casilla
                if (!empty($empresa_actual->imagen_documento) && file_exists($empresa_actual->imagen_documento)) {
                    unlink($empresa_actual->imagen_documento);
                }
                $empresa->setImagenDocumento('');
            }

            // Procesar imagen de firma
            if (isset($_FILES['imagen_firma']) && $_FILES['imagen_firma']['error'] == 0) {
                $imagen_firma = $this->processUploadedFile($_FILES['imagen_firma'], $upload_dir);
                if ($imagen_firma) {
                    // Eliminar imagen anterior si existe
                    if (!empty($empresa_actual->imagen_firma) && file_exists($empresa_actual->imagen_firma)) {
                        unlink($empresa_actual->imagen_firma);
                    }
                    $empresa->setImagenFirma($imagen_firma);
                }
            } elseif (isset($_POST['eliminar_imagen_firma'])) {
                // Eliminar imagen si se ha marcado la casilla
                if (!empty($empresa_actual->imagen_firma) && file_exists($empresa_actual->imagen_firma)) {
                    unlink($empresa_actual->imagen_firma);
                }
                $empresa->setImagenFirma('');
            }

            // Actualizar la empresa
            $resultado = $empresa->update();

            if ($resultado) {
                $_SESSION['success_message'] = "Empresa actualizada correctamente";
                $this->redirectTo('empresa/index');
            } else {
                $_SESSION['error_message'] = "Error al actualizar la empresa";
                $this->redirectTo('empresa/editar?id=' . $id);
            }
        } catch (Exception $e) {
            // Registrar el error
            error_log("Error actualizando empresa: " . $e->getMessage());
            $_SESSION['error_message'] = "Error al actualizar la empresa: " . $e->getMessage();
            $this->redirectTo('empresa/editar?id=' . $id);
        }
    }

    /**
     * Elimina una empresa del sistema
     */
    public function delete()
    {
        // Verificar si hay un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener la empresa para poder eliminar sus imágenes
        $empresa = $this->empresaModel->getById($id);

        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Eliminar las imágenes si existen
        $this->deleteImage($empresa->imagen_empresa);
        $this->deleteImage($empresa->imagen_documento);
        $this->deleteImage($empresa->imagen_firma);

        // Intentar eliminar la empresa
        $resultado = $this->empresaModel->delete($id);

        if ($resultado) {
            $_SESSION['success_message'] = "Empresa eliminada correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar la empresa";
        }

        $this->redirectTo('empresa/index');
    }

    /**
     * Cambia el estado de una empresa (activa/suspendida)
     */
    public function cambiarEstado()
    {
        // Verificar parámetros
        if (!isset($_GET['id']) || !isset($_GET['estado'])) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_GET['id'];
        $estado = $_GET['estado'];

        // Validar estado
        if (!in_array($estado, ['activa', 'suspendida'])) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('empresa/index');
            return;
        }

        // Verificar que la empresa existe
        $empresa = $this->empresaModel->getById($id);
        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Cambiar estado
        $resultado = $this->empresaModel->cambiarEstado($id, $estado);

        if ($resultado) {
            $_SESSION['success_message'] = "Estado de la empresa actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado de la empresa";
        }

        $this->redirectTo('empresa/index');
    }

    /**
     * Muestra los detalles de una empresa
     */
    public function ver()
    {
        // Título de la página
        $pageTitle = "Detalles de Empresa";

        // Obtener el ID de la empresa a ver
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de empresa no especificado";
            $this->redirectTo('empresa/index');
            return;
        }

        $id = (int)$_GET['id'];

        // Obtener la empresa por ID
        $empresa = $this->empresaModel->getById($id);

        if (!$empresa) {
            $_SESSION['error_message'] = "Empresa no encontrada";
            $this->redirectTo('empresa/index');
            return;
        }

        // Obtener información del administrador
        $admin = $this->usuarioModel->getById($empresa->usuario_id);

        // Convertir datos de facturación JSON a array
        $datos_facturacion = json_decode($empresa->datos_facturacion, true) ?: [];

        // Cargar la vista
        require_once 'views/admin/empresas/ver.php';
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

            // Obtener precio base según el período
            $precio_base = 0;
            switch ($periodo) {
                case 'Mensual':
                    $precio_base = $plan->precio_mensual;
                    break;
                case 'Semestral':
                    $precio_base = $plan->precio_semestral;
                    break;
                case 'Anual':
                    $precio_base = $plan->precio_anual;
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
            $suscripcion->setPrecioBase($precio_base);
            $suscripcion->setDescuentoPorcentaje(0);
            $suscripcion->setPrecioFinal($precio_base);
            $suscripcion->setMoneda($plan->moneda);
            $suscripcion->setEstado('Activa');
            $suscripcion->setRenovacionAutomatica('Si');
            $suscripcion->setMetodoPago('Transferencia');
            
            // Guardar la suscripción
            return $suscripcion->save();
        } catch (Exception $e) {
            error_log("Error creando suscripción: " . $e->getMessage());
            return false;
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