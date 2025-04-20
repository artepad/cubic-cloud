<?php

/**
 * ClienteController
 * 
 * Controlador para la gestión de clientes del sistema.
 */
class ClienteController
{
    private $clienteModel;
    private $empresaModel;
    private $empresa_id;

    /**
     * Constructor
     * Inicializa el modelo de cliente y verifica autenticación
     */
    public function __construct()
    {
        // Cargar el modelo
        $this->clienteModel = new Cliente();
        
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
     * Acción por defecto del controlador - Lista todos los clientes
     */
    public function index()
    {
        $pageTitle = "Gestión de Clientes";
        
        // Preparar filtros
        $filters = ['empresa_id' => $this->empresa_id];
        
        // Obtener clientes de la base de datos
        $clientes = $this->clienteModel->getAll($filters);

        require_once 'views/user/clientes/index.php';
    }

    /**
     * Muestra el formulario para crear un nuevo cliente
     */
    public function crear()
    {
        // Configurar el título de la página
        $pageTitle = "Crear Nuevo Cliente";

        // Incluir la vista
        require_once 'views/user/clientes/crear.php';
    }

    /**
     * Guarda los datos del nuevo cliente
     */
    public function guardar()
    {
        // Iniciar buffer de salida para evitar problemas de redirección
        ob_start();

        // Verificar que se han enviado los datos del formulario
        if (isset($_POST['nombres']) && isset($_POST['apellidos']) && isset($_POST['genero'])) {
            
            // Verificar que el correo no exista ya (si se proporcionó)
            if (!empty($_POST['correo']) && $this->clienteModel->correoExists($_POST['correo'], $this->empresa_id)) {
                $_SESSION['error_message'] = "El correo electrónico ya está registrado para otro cliente";
                $this->redirectTo('cliente/crear');
                return;
            }

            // Establecer los datos del cliente
            $cliente = new Cliente();
            $cliente->setEmpresaId($this->empresa_id);
            $cliente->setNombres($_POST['nombres']);
            $cliente->setApellidos($_POST['apellidos']);
            $cliente->setNumeroIdentificacion($_POST['numero_identificacion'] ?? '');
            $cliente->setTipoIdentificacion($_POST['tipo_identificacion'] ?? 'RUT');
            $cliente->setGenero($_POST['genero']);
            $cliente->setPais($_POST['pais'] ?? 'Chile');
            $cliente->setCodigoPais($_POST['codigo_pais'] ?? 'CL');
            $cliente->setCorreo($_POST['correo'] ?? '');
            $cliente->setCelular($_POST['celular'] ?? '');
            $cliente->setEstado($_POST['estado'] ?? 'Activo');

            // Guardar el cliente
            $save = $cliente->save();

            if ($save) {
                $_SESSION['success_message'] = "Cliente creado correctamente";
                $this->redirectTo('cliente/index');
            } else {
                $_SESSION['error_message'] = "Error al crear el cliente";
                $this->redirectTo('cliente/crear');
            }
        } else {
            $_SESSION['error_message'] = "Todos los campos obligatorios deben ser completados";
            $this->redirectTo('cliente/crear');
        }

        // Si algo falla, mostrar una página de redirección manual
        require_once 'views/admin/redirect.php';
        ob_end_flush();
    }

    /**
     * Muestra los detalles de un cliente específico
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
            $_SESSION['error_message'] = "ID de cliente no especificado";
            $this->redirectTo('cliente/index');
            return;
        }
        
        $cliente = $this->clienteModel->getById($id);

        if (!$cliente || $cliente->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Cliente no encontrado o no pertenece a su empresa";
            $this->redirectTo('cliente/index');
            return;
        }

        // Configurar el título de la página
        $pageTitle = "Detalles del Cliente";

        // Incluir la vista
        require_once 'views/admin/clientes/ver.php';
    }

    /**
     * Muestra el formulario para editar un cliente
     * @param mixed $id ID del cliente a editar (puede ser entero o array con parámetros)
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
            $_SESSION['error_message'] = "ID de cliente no especificado";
            $this->redirectTo('cliente/index');
            return;
        }
        
        $cliente = $this->clienteModel->getById($id);

        if (!$cliente || $cliente->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Cliente no encontrado o no pertenece a su empresa";
            $this->redirectTo('cliente/index');
            return;
        }

        $pageTitle = "Editar Cliente";
        require_once 'views/admin/clientes/editar.php';
    }

    /**
     * Actualiza los datos de un cliente existente
     */
    public function actualizar()
    {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['error_message'] = "Error de seguridad. Intente nuevamente.";
            $this->redirectTo('cliente/index');
            return;
        }

        if (!isset($_POST['id'])) {
            $_SESSION['error_message'] = "ID de cliente no especificado";
            $this->redirectTo('cliente/index');
            return;
        }

        $id = (int)$_POST['id'];
        
        $cliente_actual = $this->clienteModel->getById($id);

        if (!$cliente_actual || $cliente_actual->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Cliente no encontrado o no pertenece a su empresa";
            $this->redirectTo('cliente/index');
            return;
        }

        // Verificar que el correo no exista para otro cliente
        if (
            !empty($_POST['correo']) && 
            $_POST['correo'] !== $cliente_actual->correo &&
            $this->clienteModel->correoExists($_POST['correo'], $this->empresa_id, $id)
        ) {
            $_SESSION['error_message'] = "El correo electrónico ya está registrado para otro cliente";
            $this->redirectTo('cliente/editar/' . $id);
            return;
        }

        // Establecer los datos del cliente
        $cliente = new Cliente();
        $cliente->setId($id);
        $cliente->setEmpresaId($this->empresa_id);
        $cliente->setNombres(trim($_POST['nombres']));
        $cliente->setApellidos(trim($_POST['apellidos']));
        $cliente->setNumeroIdentificacion($_POST['numero_identificacion'] ?? '');
        $cliente->setTipoIdentificacion($_POST['tipo_identificacion'] ?? 'RUT');
        $cliente->setGenero($_POST['genero']);
        $cliente->setPais($_POST['pais'] ?? 'Chile');
        $cliente->setCodigoPais($_POST['codigo_pais'] ?? 'CL');
        $cliente->setCorreo($_POST['correo'] ?? '');
        $cliente->setCelular($_POST['celular'] ?? '');
        $cliente->setEstado($_POST['estado'] ?? 'Activo');

        // Actualizar el cliente
        $update = $cliente->update();

        if ($update) {
            $_SESSION['success_message'] = "Cliente actualizado correctamente";
            $this->redirectTo('cliente/index');
        } else {
            $_SESSION['error_message'] = "Error al actualizar el cliente";
            $this->redirectTo('cliente/editar/' . $id);
        }
    }

    /**
     * Elimina un cliente
     * @param mixed $id ID del cliente a eliminar (puede ser entero o array con parámetros)
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
            $_SESSION['error_message'] = "ID de cliente no especificado";
            $this->redirectTo('cliente/index');
            return;
        }
        
        // Verificar si el cliente existe y pertenece a la empresa
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente || $cliente->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Cliente no encontrado o no pertenece a su empresa";
            $this->redirectTo('cliente/index');
            return;
        }

        // Eliminar el cliente
        $delete = $this->clienteModel->delete($id);

        if ($delete) {
            $_SESSION['success_message'] = "Cliente eliminado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al eliminar el cliente";
        }

        $this->redirectTo('cliente/index');
    }

    /**
     * Cambia el estado de un cliente (Activo/Inactivo)
     * 
     * @param mixed $id ID del cliente a cambiar de estado
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
                $_SESSION['error_message'] = "Parámetros insuficientes para cambiar el estado del cliente";
                $this->redirectTo('cliente/index');
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
            $this->redirectTo('cliente/index');
            return;
        }
        
        // Verificar si el cliente existe y pertenece a la empresa
        $cliente = $this->clienteModel->getById($id);
        if (!$cliente || $cliente->empresa_id != $this->empresa_id) {
            $_SESSION['error_message'] = "Cliente no encontrado o no pertenece a su empresa";
            $this->redirectTo('cliente/index');
            return;
        }

        // Cambiar estado
        $cambio = $this->clienteModel->cambiarEstado($id, $estado);

        if ($cambio) {
            $_SESSION['success_message'] = "Estado del cliente actualizado correctamente";
        } else {
            $_SESSION['error_message'] = "Error al actualizar el estado del cliente";
        }

        $this->redirectTo('cliente/index');
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