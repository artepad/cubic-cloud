<?php
require_once 'config/db.php';

/**
 * Clase Cliente
 * 
 * Gestiona todas las operaciones relacionadas con los clientes del sistema,
 * incluyendo creación, actualización y gestión.
 */
class Cliente
{
    private $id;
    private $empresa_id;
    private $nombres;
    private $apellidos;
    private $numero_identificacion;
    private $tipo_identificacion;
    private $genero;
    private $pais;
    private $codigo_pais;
    private $correo;
    private $celular;
    private $estado;
    private $db;

    /**
     * Constructor de la clase
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Getters y setters
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEmpresaId() {
        return $this->empresa_id;
    }

    public function setEmpresaId($empresa_id) {
        $this->empresa_id = $empresa_id;
    }

    public function getNombres() {
        return $this->nombres;
    }

    public function setNombres($nombres) {
        $this->nombres = $this->db->real_escape_string($nombres);
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $this->db->real_escape_string($apellidos);
    }

    public function getNumeroIdentificacion() {
        return $this->numero_identificacion;
    }

    public function setNumeroIdentificacion($numero_identificacion) {
        $this->numero_identificacion = $this->db->real_escape_string($numero_identificacion);
    }

    public function getTipoIdentificacion() {
        return $this->tipo_identificacion;
    }

    public function setTipoIdentificacion($tipo_identificacion) {
        $this->tipo_identificacion = $this->db->real_escape_string($tipo_identificacion);
    }

    public function getGenero() {
        return $this->genero;
    }

    public function setGenero($genero) {
        $this->genero = $this->db->real_escape_string($genero);
    }

    public function getPais() {
        return $this->pais;
    }

    public function setPais($pais) {
        $this->pais = $this->db->real_escape_string($pais);
    }

    public function getCodigoPais() {
        return $this->codigo_pais;
    }

    public function setCodigoPais($codigo_pais) {
        $this->codigo_pais = $this->db->real_escape_string($codigo_pais);
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $this->db->real_escape_string($correo);
    }

    public function getCelular() {
        return $this->celular;
    }

    public function setCelular($celular) {
        $this->celular = $this->db->real_escape_string($celular);
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $this->db->real_escape_string($estado);
    }

    /**
     * Guarda un nuevo cliente en la base de datos
     * 
     * @return bool|int ID del cliente creado o false si falla
     */
    public function save()
    {
        try {
            $sql = "INSERT INTO clientes (
                    empresa_id, nombres, apellidos, numero_identificacion, 
                    tipo_identificacion, genero, pais, codigo_pais, 
                    correo, celular, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $estado = $this->estado ?: 'Activo';
            
            $stmt->bind_param(
                "issssssssss", 
                $this->empresa_id,
                $this->nombres, 
                $this->apellidos, 
                $this->numero_identificacion, 
                $this->tipo_identificacion, 
                $this->genero,
                $this->pais,
                $this->codigo_pais,
                $this->correo,
                $this->celular,
                $estado
            );
            
            if ($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                return $id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en save: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un cliente existente en la base de datos
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            $sql = "UPDATE clientes SET 
                    empresa_id = ?,
                    nombres = ?, 
                    apellidos = ?, 
                    numero_identificacion = ?, 
                    tipo_identificacion = ?, 
                    genero = ?,
                    pais = ?,
                    codigo_pais = ?,
                    correo = ?,
                    celular = ?,
                    estado = ?
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param(
                "issssssssssi", 
                $this->empresa_id,
                $this->nombres, 
                $this->apellidos, 
                $this->numero_identificacion, 
                $this->tipo_identificacion, 
                $this->genero,
                $this->pais,
                $this->codigo_pais,
                $this->correo,
                $this->celular,
                $this->estado,
                $this->id
            );
            
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un cliente por su ID
     * 
     * @param int $id ID del cliente a buscar
     * @return object|false Objeto con datos del cliente o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            $sql = "SELECT * FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows == 1) {
                $cliente = $result->fetch_object();
                $stmt->close();
                return $cliente;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una lista de clientes según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @param int $limit Límite de resultados (opcional)
     * @param int $offset Desplazamiento para paginación (opcional)
     * @return array Lista de clientes
     */
    public function getAll($filters = [], $limit = null, $offset = 0)
    {
        try {
            // Construir la consulta base
            $sql = "SELECT * FROM clientes WHERE 1=1";
            $params = [];
            $types = "";
            
            // Aplicar filtros si existen
            if (!empty($filters)) {
                if (isset($filters['empresa_id']) && $filters['empresa_id']) {
                    $sql .= " AND empresa_id = ?";
                    $params[] = $filters['empresa_id'];
                    $types .= "i";
                }
                
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR correo LIKE ? OR numero_identificacion LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "ssss";
                }
            }
            
            // Ordenar resultados
            $sql .= " ORDER BY id DESC";
            
            // Limitar resultados para paginación
            if ($limit !== null) {
                $sql .= " LIMIT ?, ?";
                $params[] = (int)$offset;
                $params[] = (int)$limit;
                $types .= "ii";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return [];
            }
            
            // Bind de parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $clientes = [];
            while ($cliente = $result->fetch_object()) {
                $clientes[] = $cliente;
            }
            
            $stmt->close();
            return $clientes;
        } catch (Exception $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Elimina un cliente por su ID
     * 
     * @param int $id ID del cliente a eliminar
     * @return bool Resultado de la operación
     */
    public function delete($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            $sql = "DELETE FROM clientes WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de un cliente
     * 
     * @param int $id ID del cliente
     * @param string $estado Nuevo estado ('Activo' o 'Inactivo')
     * @return bool Resultado de la operación
     */
    public function cambiarEstado($id, $estado)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            // Validar estado
            if (!in_array($estado, ['Activo', 'Inactivo'])) {
                return false;
            }
            
            $sql = "UPDATE clientes SET estado = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("si", $estado, $id);
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un correo ya existe en la base de datos para esta empresa
     * 
     * @param string $correo Correo a verificar
     * @param int $empresa_id ID de la empresa
     * @param int $exclude_id ID de cliente a excluir de la verificación (opcional)
     * @return bool True si el correo ya existe
     */
    public function correoExists($correo, $empresa_id, $exclude_id = null)
    {
        try {
            $sql = "SELECT id FROM clientes WHERE correo = ? AND empresa_id = ?";
            
            // Si se proporciona un ID para excluir, modificar la consulta
            if ($exclude_id) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return true; // Devolver true por precaución
            }
            
            if ($exclude_id) {
                $stmt->bind_param("sii", $correo, $empresa_id, $exclude_id);
            } else {
                $stmt->bind_param("si", $correo, $empresa_id);
            }
            
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            
            return $exists;
        } catch (Exception $e) {
            error_log("Error en correoExists: " . $e->getMessage());
            return true; // Devolver true por precaución
        }
    }

    /**
     * Cuenta el número total de clientes según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @return int Número total de clientes
     */
    public function countAll($filters = [])
    {
        try {
            // Construir la consulta base
            $sql = "SELECT COUNT(*) as total FROM clientes WHERE 1=1";
            $params = [];
            $types = "";
            
            // Aplicar filtros si existen
            if (!empty($filters)) {
                if (isset($filters['empresa_id']) && $filters['empresa_id']) {
                    $sql .= " AND empresa_id = ?";
                    $params[] = $filters['empresa_id'];
                    $types .= "i";
                }
                
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombres LIKE ? OR apellidos LIKE ? OR correo LIKE ? OR numero_identificacion LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "ssss";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return 0;
            }
            
            // Bind de parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_object();
            
            $stmt->close();
            return (int)$row->total;
        } catch (Exception $e) {
            error_log("Error en countAll: " . $e->getMessage());
            return 0;
        }
    }
}