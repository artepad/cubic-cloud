<?php

/**
 * UsuarioController
 * 
 * Controlador para la gestión de usuarios normales del sistema.
 * Separado del AdminController para seguir el principio de responsabilidad única.
 */
class UsuarioController
{
    private $usuarioModel;

    /**
     * Constructor
     * Inicializa el modelo de usuario y verifica autenticación
     */
    public function __construct()
    {
        // Cargar el modelo
        $this->usuarioModel = new Usuario();

        // Verificar autenticación para todas las acciones excepto las públicas
        $publicMethods = []; // Sin métodos públicos por ahora
        $currentMethod = isset($_GET['action']) ? $_GET['action'] : 'index';

        if (!in_array($currentMethod, $publicMethods) && !isAdminLoggedIn()) {
            $_SESSION['error_login'] = "Acceso denegado. Se requiere cuenta de administrador.";
            header("Location:" . base_url . "admin/login");
            exit();
        }
    }

    /**
     * Acción por defecto del controlador - Lista todos los usuarios
     */
    public function index()
    {
        return $this->listar();
    }

    /**
     * Lista todos los usuarios del sistema
     */
    public function listar()
    {
        $pageTitle = "Gestión de Usuarios";

        // Obtener usuarios de la base de datos
        $usuarios = $this->usuarioModel->getAll();

        require_once 'views/admin/usuarios/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function crear()
    {
        // Configurar el título de la página
        $pageTitle = "Crear Nuevo Usuario";

        // Incluir la vista
        require_once 'views/admin/usuarios/crear.php';
    }

    /**
     * Guarda los datos del nuevo usuario
     */
    public function guardar()
    {
        // Iniciar buffer de salida para evitar problemas de redirección
        ob_start();

        // Verificar que se han enviado los datos del formulario
        if (isset($_POST['nombre']) && isset($_POST['apellido']) && isset($_POST['email']) && isset($_POST['password'])) {

            // Verificar que el email no exista ya
            if ($this->usuarioModel->emailExists($_POST['email'])) {
                $_SESSION['error_message'] = "El correo electrónico ya está registrado";
                $this->redirectTo('usuario/crear');
                return;
            }

            // Verificar que las contraseñas coinciden
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error_message'] = "Las contraseñas no coinciden";
                $this->redirectTo('usuario/crear');
                return;
            }

            // Establecer los datos del usuario
            $usuario = new Usuario();
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
                $this->redirectTo('usuario/index');
            } else {
                $_SESSION['error_message'] = "Error al crear el usuario";
                $this->redirectTo('usuario/crear');
            }
        } else {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('usuario/crear');
        }

        // Si algo falla, mostrar una página de redirección manual
        require_once 'views/admin/redirect.php';
        ob_end_flush();
    }


    /**
     * Muestra los detalles de un usuario específico
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
            $_SESSION['error_message'] = "ID de usuario no especificado";
            $this->redirectTo('usuario/index');
            return;
        }

        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo('usuario/index');
            return;
        }

        // Configurar el título de la página
        $pageTitle = "Detalles del Usuario";

        // Incluir la vista
        require_once 'views/admin/usuarios/ver.php';
    }

    /**
     * Muestra el formulario para editar un usuario
     * @param mixed $id ID del usuario a editar (puede ser entero o array con parámetros)
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
            $_SESSION['error_message'] = "ID de usuario no especificado";
            $this->redirectTo('usuario/index');
            return;
        }

        $usuario = $this->usuarioModel->getById($id);

        if (!$usuario) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo('usuario/index');
            return;
        }

        $pageTitle = "Editar Usuario";
        require_once 'views/admin/usuarios/editar.php';
    }

    /**
     * Actualiza los datos de un usuario existente
     */
    public function actualizar()
    {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['error_message'] = "Error de seguridad. Intente nuevamente.";
            $this->redirectTo('usuario/index');
            return;
        }

        if (!isset($_POST['id'])) {
            $_SESSION['error_message'] = "ID de usuario no especificado";
            $this->redirectTo('usuario/index');
            return;
        }

        $id = (int)$_POST['id'];
        $usuario_actual = $this->usuarioModel->getById($id);

        if (!$usuario_actual) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo('usuario/index');
            return;
        }

        // Verificar que el email no exista para otro usuario
        if (
            $_POST['email'] !== $usuario_actual->email &&
            $this->usuarioModel->emailExists($_POST['email'], $id)
        ) {
            $_SESSION['error_message'] = "El correo electrónico ya está registrado para otro usuario";
            $this->redirectTo('usuario/editar/' . $id);
            return;
        }

        // Establecer los datos del usuario
        $usuario = new Usuario();
        $usuario->setId($id);
        $usuario->setNombre(trim($_POST['nombre']));
        $usuario->setApellido(trim($_POST['apellido']));
        $usuario->setEmail(trim($_POST['email']));
        $usuario->setTelefono($_POST['telefono'] ?? '');
        $usuario->setPais($_POST['pais'] ?? 'Chile');
        $usuario->setCodigoPais($_POST['codigo_pais'] ?? 'CL');
        $usuario->setNumeroIdentificacion($_POST['numero_identificacion'] ?? '');
        $usuario->setTipoIdentificacion($_POST['tipo_identificacion'] ?? 'RUT');
        $usuario->setTipoUsuario($_POST['tipo_usuario'] ?? 'ADMIN');
        $usuario->setEstado($_POST['estado'] ?? 'Activo');

        // Actualizar contraseña solo si se ha proporcionado una nueva
        if (!empty($_POST['password'])) {
            // Verificar que las contraseñas coinciden
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $_SESSION['error_message'] = "Las contraseñas no coinciden";
                $this->redirectTo('usuario/editar/' . $id);
                return;
            }

            // Verificar longitud mínima
            if (strlen($_POST['password']) < 8) {
                $_SESSION['error_message'] = "La contraseña debe tener al menos 8 caracteres";
                $this->redirectTo('usuario/editar/' . $id);
                return;
            }

            $usuario->setPassword($_POST['password']);
        }

        // Actualizar el usuario
        $update = $usuario->update();

        if ($update) {
            $_SESSION['success_message'] = "Usuario actualizado correctamente";
            $this->redirectTo('usuario/index');
        } else {
            $_SESSION['error_message'] = "Error al actualizar el usuario";
            $this->redirectTo('usuario/editar/' . $id);
        }
    }

    /**
     * Elimina un usuario
     */
    public function eliminar()
    {
        if (!isset($_GET['id'])) {
            $_SESSION['error_message'] = "ID de usuario no especificado";
            $this->redirectTo('usuario/listar');
            return;
        }

        $id = (int)$_GET['id'];

        // No permitir eliminar al usuario actual
        if (isset($_SESSION['admin']) && $_SESSION['admin']->id == $id) {
            $_SESSION['error_message'] = "No puedes eliminar tu propio usuario";
            $this->redirectTo('usuario/listar');
            return;
        }

        // Verificar si el usuario existe
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo('usuario/listar');
            return;
        }

        // Eliminar el usuario
        $delete = $this->usuarioModel->delete($id);

        if ($delete) {
            $_SESSION['success_message'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el usuario";
        }

        $this->redirectTo('usuario/listar');
    }

    /**
     * Cambia el estado de un usuario (Activo/Inactivo)
     * 
     * @param mixed $params Parámetros de la ruta (puede ser array o valores individuales)
     */
    public function cambiarEstado($params = null)
    {
        // Obtener parámetros ya sea desde la ruta o desde GET
        $id = null;
        $estado = null;

        // Si vienen como parámetros de ruta
        if (is_array($params) && isset($params['id']) && isset($params['estado'])) {
            $id = (int)$params['id'];
            $estado = $params['estado'];
        }
        // Si vienen como parámetros individuales de ruta
        elseif (!is_array($params) && $params !== null) {
            $id = (int)$params;
            $estado = func_get_arg(1); // Obtener el segundo parámetro
        }
        // Si vienen como parámetros GET
        elseif (isset($_GET['id']) && isset($_GET['estado'])) {
            $id = (int)$_GET['id'];
            $estado = $_GET['estado'];
        }

        if (!$id || !$estado) {
            $_SESSION['error_message'] = "Parámetros insuficientes";
            $this->redirectTo('usuario/index');
            return;
        }

        // Validar estado
        if (!in_array($estado, ['Activo', 'Inactivo'])) {
            $_SESSION['error_message'] = "Estado no válido";
            $this->redirectTo('usuario/index');
            return;
        }

        // Verificar si el usuario existe
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            $_SESSION['error_message'] = "Usuario no encontrado";
            $this->redirectTo('usuario/index');
            return;
        }

        // Cambiar estado
        $cambio = $this->usuarioModel->cambiarEstado($id, $estado);

        if ($cambio) {
            $_SESSION['success_message'] = "Estado del usuario actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del usuario";
        }

        $this->redirectTo('usuario/index');
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
