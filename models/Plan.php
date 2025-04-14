<?php
require_once 'config/db.php';

/**
 * Clase Plan
 * 
 * Gestiona todas las operaciones relacionadas con los planes de suscripción,
 * incluyendo creación, actualización, eliminación y consulta.
 */
class Plan
{
    // Propiedades de la clase
    private $id;
    private $nombre;
    private $descripcion;
    private $tipo_plan;
    private $precio_mensual;
    private $precio_semestral;
    private $precio_anual;
    private $moneda;
    private $max_usuarios;
    private $max_eventos;
    private $max_artistas;
    private $caracteristicas;
    private $estado;
    private $visible;
    private $db;
    private $lastError;

    /**
     * Constructor de la clase
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::connect();
        $this->lastError = null;
    }
    
    /**
     * Obtiene el último error registrado
     * 
     * @return string|null Mensaje de error o null si no hay error
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Establece un mensaje de error
     * 
     * @param string $error Mensaje de error
     */
    private function setError($error)
    {
        $this->lastError = $error;
        error_log("Error en Plan: " . $error);
    }

    /**
     * Getters y setters
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $this->db->real_escape_string($nombre);
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $this->db->real_escape_string($descripcion);
    }

    public function getTipoPlan()
    {
        return $this->tipo_plan;
    }

    public function setTipoPlan($tipo_plan)
    {
        // Validar que el tipo sea uno de los permitidos
        $tipos_validos = ['Básico', 'Profesional', 'Premium', 'Personalizado'];
        if (in_array($tipo_plan, $tipos_validos)) {
            $this->tipo_plan = $tipo_plan;
        } else {
            $this->tipo_plan = 'Básico'; // Valor por defecto
        }
    }

    public function getPrecioMensual()
    {
        return $this->precio_mensual;
    }

    public function setPrecioMensual($precio_mensual)
    {
        // Validar que sea un número válido
        $precio = filter_var($precio_mensual, FILTER_VALIDATE_FLOAT);
        if ($precio !== false && $precio >= 0) {
            $this->precio_mensual = $precio;
        } else {
            $this->precio_mensual = 0.00;
        }
    }

    public function getPrecioSemestral()
    {
        return $this->precio_semestral;
    }

    public function setPrecioSemestral($precio_semestral)
    {
        // Validar que sea un número válido
        $precio = filter_var($precio_semestral, FILTER_VALIDATE_FLOAT);
        if ($precio !== false && $precio >= 0) {
            $this->precio_semestral = $precio;
        } else {
            $this->precio_semestral = 0.00;
        }
    }

    public function getPrecioAnual()
    {
        return $this->precio_anual;
    }

    public function setPrecioAnual($precio_anual)
    {
        // Validar que sea un número válido
        $precio = filter_var($precio_anual, FILTER_VALIDATE_FLOAT);
        if ($precio !== false && $precio >= 0) {
            $this->precio_anual = $precio;
        } else {
            $this->precio_anual = 0.00;
        }
    }

    public function getMoneda()
    {
        return $this->moneda;
    }

    public function setMoneda($moneda)
    {
        // Validar que la moneda sea uno de los valores esperados
        $monedas_validas = ['CLP', 'USD', 'EUR'];
        if (in_array($moneda, $monedas_validas)) {
            $this->moneda = $moneda;
        } else {
            $this->moneda = 'CLP'; // Valor por defecto
        }
    }

    public function getMaxUsuarios()
    {
        return $this->max_usuarios;
    }

    public function setMaxUsuarios($max_usuarios)
    {
        // Validar que sea un número entero positivo
        $max = filter_var($max_usuarios, FILTER_VALIDATE_INT);
        if ($max !== false && $max >= 0) {
            $this->max_usuarios = $max;
        } else {
            $this->max_usuarios = 1; // Valor por defecto
        }
    }

    public function getMaxArtistas()
    {
        return $this->max_artistas;
    }

    public function setMaxArtistas($max_artistas)
    {
        // Validar que sea un número entero positivo
        $max = filter_var($max_artistas, FILTER_VALIDATE_INT);
        if ($max !== false && $max >= 0) {
            $this->max_artistas = $max;
        } else {
            $this->max_artistas = 5; // Valor por defecto
        }
    }

    public function getMaxEventos()
    {
        return $this->max_eventos;
    }

    public function setMaxEventos($max_eventos)
    {
        // Validar que sea un número entero positivo
        $max = filter_var($max_eventos, FILTER_VALIDATE_INT);
        if ($max !== false && $max >= 0) {
            $this->max_eventos = $max;
        } else {
            $this->max_eventos = 10; // Valor por defecto
        }
    }

    public function getCaracteristicas()
    {
        return $this->caracteristicas;
    }

    public function setCaracteristicas($caracteristicas)
    {
        // Si es un array, convertirlo a JSON
        if (is_array($caracteristicas)) {
            $this->caracteristicas = json_encode($caracteristicas);
        } else {
            // Validar que sea un JSON válido
            json_decode($caracteristicas);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->caracteristicas = $caracteristicas;
            } else {
                $this->setError("Error en formato JSON: " . json_last_error_msg());
                $this->caracteristicas = '{}'; // JSON vacío por defecto
            }
        }
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        // Validar que el estado sea uno de los permitidos
        $estados_validos = ['Activo', 'Inactivo', 'Descontinuado'];
        if (in_array($estado, $estados_validos)) {
            $this->estado = $estado;
        } else {
            $this->estado = 'Activo'; // Valor por defecto
        }
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        // Validar que sea uno de los valores permitidos
        if ($visible === 'Si' || $visible === 'No') {
            $this->visible = $visible;
        } else {
            $this->visible = 'Si'; // Valor por defecto
        }
    }

    /**
     * Guarda un nuevo plan en la base de datos
     * 
     * @return bool|int ID del plan creado o false si falla
     */
    public function save()
    {
        try {
            // Verificar que tenemos los campos mínimos requeridos
            if (empty($this->nombre) || empty($this->tipo_plan) || !isset($this->precio_mensual)) {
                $this->setError("Faltan campos obligatorios");
                return false;
            }

            // Asegurarse de que descripción nunca sea null para evitar problemas con NOT NULL
            if (empty($this->descripcion)) {
                $this->descripcion = '';
            }

            $sql = "INSERT INTO planes (
                nombre, descripcion, tipo_plan, 
                precio_mensual, precio_semestral, precio_anual, moneda,
                max_usuarios, max_artistas, max_eventos,
                caracteristicas, estado, visible
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            // Bindear los parámetros
            if (!$stmt->bind_param(
                "sssdddsiiisss",
                $this->nombre,
                $this->descripcion,
                $this->tipo_plan,
                $this->precio_mensual,
                $this->precio_semestral,
                $this->precio_anual,
                $this->moneda,
                $this->max_usuarios,
                $this->max_artistas,
                $this->max_eventos,
                $this->caracteristicas,
                $this->estado,
                $this->visible
            )) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            // Ejecutar la consulta
            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $id = $this->db->insert_id;
            $stmt->close();
            return $id;
        } catch (Exception $e) {
            $this->setError("Error en save: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un plan existente en la base de datos
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            // Verificar que tenemos un ID
            if (empty($this->id) || $this->id <= 0) {
                $this->setError("ID de plan no válido");
                return false;
            }

            // Asegurarse de que descripción nunca sea null para evitar problemas con NOT NULL
            if (empty($this->descripcion)) {
                $this->descripcion = '';
            }

            $sql = "UPDATE planes SET 
                nombre = ?, 
                descripcion = ?, 
                tipo_plan = ?, 
                precio_mensual = ?, 
                precio_semestral = ?, 
                precio_anual = ?, 
                moneda = ?, 
                max_usuarios = ?, 
                max_artistas = ?,
                max_eventos = ?, 
                caracteristicas = ?,
                estado = ?,
                visible = ?
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            // Bindear los parámetros
            if (!$stmt->bind_param(
                "sssdddsiiisssi",
                $this->nombre,
                $this->descripcion,
                $this->tipo_plan,
                $this->precio_mensual,
                $this->precio_semestral,
                $this->precio_anual,
                $this->moneda,
                $this->max_usuarios,
                $this->max_artistas,
                $this->max_eventos,
                $this->caracteristicas,
                $this->estado,
                $this->visible,
                $this->id
            )) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            // Ejecutar la consulta
            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            $this->setError("Error en update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un plan por su ID
     * 
     * @param int $id ID del plan a buscar
     * @return object|false Objeto con datos del plan o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            $sql = "SELECT * FROM planes WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            if (!$stmt->bind_param("i", $id)) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $plan = $result->fetch_object();
                $stmt->close();
                return $plan;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            $this->setError("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una lista de todos los planes
     * 
     * @return array Lista de planes
     */
    public function getAll()
    {
        try {
            // Consulta simple para obtener todos los planes
            $sql = "SELECT * FROM planes ORDER BY id ASC";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return [];
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return [];
            }

            $result = $stmt->get_result();

            $planes = [];
            while ($plan = $result->fetch_object()) {
                $planes[] = $plan;
            }

            $stmt->close();
            return $planes;
        } catch (Exception $e) {
            $this->setError("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Elimina un plan por su ID
     * 
     * @param int $id ID del plan a eliminar
     * @return bool Resultado de la operación
     */
    public function delete($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            // Comprobar primero si hay suscripciones usando este plan
            $check_sql = "SELECT COUNT(*) as count FROM suscripciones WHERE plan_id = ?";
            $check_stmt = $this->db->prepare($check_sql);

            if (!$check_stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            if (!$check_stmt->bind_param("i", $id)) {
                $this->setError("Error en bind_param: " . $check_stmt->error);
                $check_stmt->close();
                return false;
            }

            if (!$check_stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $check_stmt->error);
                $check_stmt->close();
                return false;
            }

            $check_result = $check_stmt->get_result();
            $row = $check_result->fetch_object();
            $check_stmt->close();

            // Si hay suscripciones asociadas, no permitir eliminar el plan
            if ($row->count > 0) {
                $this->setError("No se puede eliminar el plan porque tiene suscripciones asociadas");
                return false;
            }

            // Si no hay suscripciones asociadas, proceder con la eliminación
            $sql = "DELETE FROM planes WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            if (!$stmt->bind_param("i", $id)) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            $this->setError("Error en delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un plan
     * 
     * @param int $id ID del plan
     * @param string $estado Nuevo estado ('Activo', 'Inactivo', 'Descontinuado')
     * @return bool Resultado de la operación
     */
    public function cambiarEstado($id, $estado)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            // Validar estado
            $estados_validos = ['Activo', 'Inactivo', 'Descontinuado'];
            if (!in_array($estado, $estados_validos)) {
                $this->setError("Estado no válido: " . $estado);
                return false;
            }

            $sql = "UPDATE planes SET estado = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            if (!$stmt->bind_param("si", $estado, $id)) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            $this->setError("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia la visibilidad de un plan
     * 
     * @param int $id ID del plan
     * @param string $visible Nuevo valor ('Si', 'No')
     * @return bool Resultado de la operación
     */
    public function cambiarVisibilidad($id, $visible)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            // Validar visibilidad
            if ($visible !== 'Si' && $visible !== 'No') {
                $this->setError("Valor de visibilidad no válido: " . $visible);
                return false;
            }

            $sql = "UPDATE planes SET visible = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return false;
            }

            if (!$stmt->bind_param("si", $visible, $id)) {
                $this->setError("Error en bind_param: " . $stmt->error);
                $stmt->close();
                return false;
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return false;
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            $this->setError("Error en cambiarVisibilidad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cuenta el número total de planes según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @return int Número total de planes
     */
    public function countAll($filters = [])
    {
        try {
            // Construir la consulta base
            $sql = "SELECT COUNT(*) as total FROM planes WHERE 1=1";
            $params = [];
            $types = "";

            // Aplicar filtros si existen
            if (!empty($filters)) {
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }

                if (isset($filters['tipo_plan']) && $filters['tipo_plan']) {
                    $sql .= " AND tipo_plan = ?";
                    $params[] = $filters['tipo_plan'];
                    $types .= "s";
                }

                if (isset($filters['visible']) && $filters['visible']) {
                    $sql .= " AND visible = ?";
                    $params[] = $filters['visible'];
                    $types .= "s";
                }

                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "ss";
                }
            }

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return 0;
            }

            // Bind de parámetros si existen
            if (!empty($params)) {
                if (!$stmt->bind_param($types, ...$params)) {
                    $this->setError("Error en bind_param: " . $stmt->error);
                    $stmt->close();
                    return 0;
                }
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return 0;
            }

            $result = $stmt->get_result();
            $row = $result->fetch_object();

            $stmt->close();
            return (int)$row->total;
        } catch (Exception $e) {
            $this->setError("Error en countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtiene planes activos y visibles para mostrar a los clientes
     * 
     * @return array Lista de planes publicados
     */
    public function getPlanesPublicados()
    {
        try {
            $sql = "SELECT * FROM planes WHERE estado = 'Activo' AND visible = 'Si' ORDER BY precio_mensual ASC";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                $this->setError("Error preparando consulta: " . $this->db->error);
                return [];
            }

            if (!$stmt->execute()) {
                $this->setError("Error ejecutando consulta: " . $stmt->error);
                $stmt->close();
                return [];
            }

            $result = $stmt->get_result();

            $planes = [];
            while ($plan = $result->fetch_object()) {
                // Convertir el JSON de características a un array asociativo
                $plan->caracteristicas_array = json_decode($plan->caracteristicas, true);
                $planes[] = $plan;
            }

            $stmt->close();
            return $planes;
        } catch (Exception $e) {
            $this->setError("Error en getPlanesPublicados: " . $e->getMessage());
            return [];
        }
    }
}