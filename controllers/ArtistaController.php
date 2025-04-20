<?php

/**
 * ArtistaController
 * 
 * Controlador para la gestión de artistas del sistema.
 */
class ArtistaController
{
    private $artistaModel;
    private $empresaModel;
    private $empresa_id;

    /**
     * Constructor
     * Inicializa el modelo de artista y verifica autenticación
     */
    public function __construct()
    {
        // Cargar el modelo
        require_once 'models/Artista.php';
        $this->artistaModel = new Artista();
        
        // Cargar modelo de empresa si es necesario
        require_once 'models/Empresa.php';
        $this->empresaModel = new Empresa();

        // Verificar autenticación - usuario o admin
        if (!isUserLoggedIn() && !isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere iniciar sesión.";
            header("Location:" . base_url . "user/login");
            exit();
        }

        // Obtener el ID de empresa según el tipo de usuario
        if (isAdminLoggedIn() && isset($_SESSION['admin_empresa_id'])) {
            $this->empresa_id = $_SESSION['admin_empresa_id'];
        } elseif (isUserLoggedIn()) {
            // Buscar la empresa del usuario en la BD
            $usuario = $_SESSION['user'];
            $db = Database::connect();
            $query = "SELECT id FROM empresas WHERE usuario_id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $usuario->id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $_SESSION['error_message'] = "No tienes una empresa asignada";
                header("Location:" . base_url . "user/dashboard");
                exit();
            } else {
                $empresa = $result->fetch_object();
                $this->empresa_id = $empresa->id;
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = "No se ha identificado una empresa válida";
            header("Location:" . base_url . "user/dashboard");
            exit();
        }
    }

    /**
     * Acción por defecto del controlador - Lista todos los artistas
     */
    public function index()
    {
        $pageTitle = "Gestión de Artistas";
        
        // Preparar filtros
        $filters = ['empresa_id' => $this->empresa_id];
        
        // Obtener artistas de la base de datos
        $artistas = $this->artistaModel->getAll($filters);

        require_once 'views/user/artistas/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo artista
     */
    public function crear()
    {
        // Configurar el título de la página
        $pageTitle = "Crear Nuevo Artista";

        // Incluir la vista
        require_once 'views/user/artistas/crear.php';
    }

    /**
     * Guarda los datos del nuevo artista
     */
    public function guardar()
    {
        // Iniciar buffer de salida para evitar problemas de redirección
        ob_start();

        // Verificar que se han enviado los datos del formulario
        if (isset($_POST['nombre']) && isset($_POST['genero_musical'])) {
            
            // Verificar que el nombre no exista ya
            if ($this->artistaModel->nombreExists($_POST['nombre'], $this->empresa_id)) {
                $_SESSION['error_message'] = "El nombre artístico ya está registrado para otro artista";
                $this->redirectTo('artistas/crear');
                return;
            }

            // Establecer los datos del artista
            $artista = new Artista();
            $artista->setEmpresaId($this->empresa_id);
            $artista->setNombre($_POST['nombre']);
            $artista->setGeneroMusical($_POST['genero_musical']);
            $artista->setDescripcion($_POST['descripcion'] ?? '');
            $artista->setPresentacion($_POST['presentacion'] ?? '');
            $artista->setEstado($_POST['estado'] ?? 'Activo');

            // Procesar imagen de presentación
            $imagen_presentacion = '';
            if (isset($_FILES['imagen_presentacion']) && $_FILES['imagen_presentacion']['tmp_name'] != '') {
                $imagen_presentacion = $this->subirImagen('imagen_presentacion', 'artistas/presentacion');
                if ($imagen_presentacion) {
                    $artista->setImagenPresentacion($imagen_presentacion);
                }
            }

            // Procesar logo del artista
            $logo_artista = '';
            if (isset($_FILES['logo_artista']) && $_FILES['logo_artista']['tmp_name'] != '') {
                $logo_artista = $this->subirImagen('logo_artista', 'artistas/logos');
                if ($logo_artista) {
                    $artista->setLogoArtista($logo_artista);
                }
            }

            // Guardar el artista
            $save = $artista->save();

            if ($save) {
                $_SESSION['success_message'] = "Artista creado correctamente";
                $this->redirectTo('artistas/index');
            } else {
                $_SESSION['error_message'] = "Error al crear el artista";
                $this->redirectTo('artistas/crear');
            }
        } else {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('artistas/crear');
        }

        // Si algo falla, mostrar una página de redirección manual
        require_once 'views/user/redirect.php';
        ob_end_flush();
    }

    /**
     * Muestra los detalles de un artista específico
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
            $_SESSION['error_message'] = "ID de artista no especificado";
            $this->redirectTo('artista/index');
            return;
        }
        
        $artista = $this->artistaModel->getById($id);

        if (!$artista || $artista->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Artista no encontrado o no pertenece a su empresa";
            $this->redirectTo('artista/index');
            return;
        }

        // Configurar el título de la página
        $pageTitle = "Detalles del Artista";

        // Incluir la vista
        require_once 'views/user/artistas/ver.php';
    }

    /**
     * Muestra el formulario para editar un artista
     * @param mixed $id ID del artista a editar (puede ser entero o array con parámetros)
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
            $_SESSION['error_message'] = "ID de artista no especificado";
            $this->redirectTo('artista/index');
            return;
        }
        
        $artista = $this->artistaModel->getById($id);

        if (!$artista || $artista->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Artista no encontrado o no pertenece a su empresa";
            $this->redirectTo('artista/index');
            return;
        }

        $pageTitle = "Editar Artista";
        require_once 'views/user/artistas/editar.php';
    }

    /**
     * Actualiza los datos de un artista existente
     */
    public function actualizar()
    {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['error_message'] = "Error de seguridad. Intente nuevamente.";
            $this->redirectTo('artista/index');
            return;
        }

        if (!isset($_POST['id'])) {
            $_SESSION['error_message'] = "ID de artista no especificado";
            $this->redirectTo('artista/index');
            return;
        }

        $id = (int)$_POST['id'];
        
        $artista_actual = $this->artistaModel->getById($id);

        if (!$artista_actual || $artista_actual->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Artista no encontrado o no pertenece a su empresa";
            $this->redirectTo('artista/index');
            return;
        }

        // Verificar que el nombre no exista para otro artista
        if (
            $_POST['nombre'] !== $artista_actual->nombre &&
            $this->artistaModel->nombreExists($_POST['nombre'], $this->empresa_id, $id)
        ) {
            $_SESSION['error_message'] = "El nombre artístico ya está registrado para otro artista";
            $this->redirectTo('artista/editar/' . $id);
            return;
        }

        // Establecer los datos del artista
        $artista = new Artista();
        $artista->setId($id);
        $artista->setEmpresaId($this->empresa_id);
        $artista->setNombre(trim($_POST['nombre']));
        $artista->setGeneroMusical($_POST['genero_musical']);
        $artista->setDescripcion($_POST['descripcion'] ?? '');
        $artista->setPresentacion($_POST['presentacion'] ?? '');
        $artista->setEstado($_POST['estado'] ?? 'Activo');
        
        // Mantener imágenes existentes si no se suben nuevas
        $artista->setImagenPresentacion($artista_actual->imagen_presentacion);
        $artista->setLogoArtista($artista_actual->logo_artista);

        // Procesar imagen de presentación si se sube una nueva
        if (isset($_FILES['imagen_presentacion']) && $_FILES['imagen_presentacion']['tmp_name'] != '') {
            $imagen_presentacion = $this->subirImagen('imagen_presentacion', 'artistas/presentacion');
            if ($imagen_presentacion) {
                // Eliminar imagen anterior si existe
                if (!empty($artista_actual->imagen_presentacion)) {
                    $this->eliminarImagen($artista_actual->imagen_presentacion);
                }
                $artista->setImagenPresentacion($imagen_presentacion);
            }
        }

        // Procesar logo del artista si se sube uno nuevo
        if (isset($_FILES['logo_artista']) && $_FILES['logo_artista']['tmp_name'] != '') {
            $logo_artista = $this->subirImagen('logo_artista', 'artistas/logos');
            if ($logo_artista) {
                // Eliminar logo anterior si existe
                if (!empty($artista_actual->logo_artista)) {
                    $this->eliminarImagen($artista_actual->logo_artista);
                }
                $artista->setLogoArtista($logo_artista);
            }
        }

        // Actualizar el artista
        $update = $artista->update();

        if ($update) {
            $_SESSION['success_message'] = "Artista actualizado correctamente";
            $this->redirectTo('artista/index');
        } else {
            $_SESSION['error_message'] = "Error al actualizar el artista";
            $this->redirectTo('artista/editar/' . $id);
        }
    }

    /**
     * Elimina un artista
     * @param mixed $id ID del artista a eliminar (puede ser entero o array con parámetros)
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

        if (!$id) {
            $_SESSION['error_message'] = "ID de artista no especificado";
            $this->redirectTo('artista/index');
            return;
        }
        
        // Verificar si el artista existe y pertenece a la empresa
        $artista = $this->artistaModel->getById($id);
        if (!$artista || $artista->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Artista no encontrado o no pertenece a su empresa";
            $this->redirectTo('artista/index');
            return;
        }

        // Eliminar imágenes asociadas
        if (!empty($artista->imagen_presentacion)) {
            $this->eliminarImagen($artista->imagen_presentacion);
        }
        if (!empty($artista->logo_artista)) {
            $this->eliminarImagen($artista->logo_artista);
        }

        // Eliminar el artista
        $delete = $this->artistaModel->delete($id);

        if ($delete) {
            $_SESSION['success_message'] = "Artista eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el artista";
        }

        $this->redirectTo('artista/index');
    }

    /**
     * Cambia el estado de un artista (Activo/Inactivo)
     * 
     * @param mixed $id ID del artista a cambiar de estado
     * @param string $estado Nuevo estado (Activo/Inactivo)
     */
    public function cambiarEstado($id = null, $estado = null)
    {
        // Si no se especificaron parámetros, intentar obtenerlos de $_GET
        if ($id === null || $estado === null) {
            if (isset($_GET['id']) && isset($_GET['estado'])) {
                $id = (int)$_GET['id'];
                $estado = $_GET['estado'];
            } else {
                $_SESSION['error_message'] = "Parámetros insuficientes para cambiar el estado del artista";
                $this->redirectTo('artista/index');
                return;
            }
        }

        // Si los parámetros vienen en un array (desde el Router)
        if (is_array($id) && isset($id['id']) && isset($id['estado'])) {
            $estado = $id['estado'];
            $id = (int)$id['id'];
        }

        // Validar estado
        if (!in_array($estado, ['Activo', 'Inactivo'])) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('artista/index');
            return;
        }
        
        // Verificar si el artista existe y pertenece a la empresa
        $artista = $this->artistaModel->getById($id);
        if (!$artista || $artista->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Artista no encontrado o no pertenece a su empresa";
            $this->redirectTo('artista/index');
            return;
        }

        // Cambiar estado
        $cambio = $this->artistaModel->cambiarEstado($id, $estado);

        if ($cambio) {
            $_SESSION['success_message'] = "Estado del artista actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del artista";
        }

        $this->redirectTo('artista/index');
    }

    /**
     * Sube una imagen al servidor
     * 
     * @param string $file_input Nombre del campo de archivo en el formulario
     * @param string $destino_dir Directorio de destino relativo a 'uploads/'
     * @return string|false Ruta relativa de la imagen o false si falla
     */
    private function subirImagen($file_input, $destino_dir)
    {
        // Verificar si se subió un archivo
        if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] != UPLOAD_ERR_OK) {
            return false;
        }

        // Información del archivo
        $file_tmp = $_FILES[$file_input]['tmp_name'];
        $file_name = $_FILES[$file_input]['name'];
        $file_size = $_FILES[$file_input]['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validar extensión
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $extensiones_permitidas)) {
            $_SESSION['error_message'] = "Formato de imagen no permitido. Use: JPG, JPEG, PNG o GIF";
            return false;
        }

        // Validar tamaño (máximo 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            $_SESSION['error_message'] = "La imagen es demasiado grande. Máximo 5MB";
            return false;
        }

        // Crear directorio si no existe
        $upload_dir = 'uploads/' . $destino_dir;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generar nombre único para evitar sobrescrituras
        $new_file_name = md5(uniqid(rand(), true)) . '.' . $file_ext;
        $destino = $upload_dir . '/' . $new_file_name;

        // Mover el archivo
        if (move_uploaded_file($file_tmp, $destino)) {
            return $destino;
        } else {
            error_log("Error al mover archivo: " . $file_tmp . " a " . $destino);
            return false;
        }
    }

    /**
     * Elimina una imagen del servidor
     * 
     * @param string $ruta_imagen Ruta relativa de la imagen a eliminar
     * @return bool Resultado de la operación
     */
    private function eliminarImagen($ruta_imagen)
    {
        if (file_exists($ruta_imagen)) {
            return unlink($ruta_imagen);
        }
        return false;
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
}