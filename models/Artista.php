<?php
require_once 'config/db.php';

/**
 * Clase Artista
 * 
 * Gestiona todas las operaciones relacionadas con los artistas del sistema,
 * incluyendo creación, actualización, eliminación y consulta.
 */
class Artista
{
    private $id;
    private $empresa_id;
    private $nombre;
    private $genero_musical;
    private $descripcion;
    private $presentacion;
    private $imagen_presentacion;
    private $logo_artista;
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

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $this->db->real_escape_string($nombre);
    }

    public function getGeneroMusical() {
        return $this->genero_musical;
    }

    public function setGeneroMusical($genero_musical) {
        $this->genero_musical = $this->db->real_escape_string($genero_musical);
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $this->db->real_escape_string($descripcion);
    }

    public function getPresentacion() {
        return $this->presentacion;
    }

    public function setPresentacion($presentacion) {
        $this->presentacion = $this->db->real_escape_string($presentacion);
    }

    public function getImagenPresentacion() {
        return $this->imagen_presentacion;
    }

    public function setImagenPresentacion($imagen_presentacion) {
        $this->imagen_presentacion = $this->db->real_escape_string($imagen_presentacion);
    }

    public function getLogoArtista() {
        return $this->logo_artista;
    }

    public function setLogoArtista($logo_artista) {
        $this->logo_artista = $this->db->real_escape_string($logo_artista);
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $this->db->real_escape_string($estado);
    }

    /**
     * Guarda un nuevo artista en la base de datos
     * 
     * @return bool|int ID del artista creado o false si falla
     */
    public function save()
    {
        try {
            $sql = "INSERT INTO artistas (
                    empresa_id, nombre, genero_musical, descripcion, 
                    presentacion, imagen_presentacion, logo_artista, estado
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $estado = $this->estado ?: 'Activo';
            
            $stmt->bind_param(
                "issssss", 
                $this->empresa_id,
                $this->nombre, 
                $this->genero_musical, 
                $this->descripcion, 
                $this->presentacion, 
                $this->imagen_presentacion,
                $this->logo_artista,
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
     * Actualiza un artista existente en la base de datos
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            $sql = "UPDATE artistas SET 
                    empresa_id = ?,
                    nombre = ?, 
                    genero_musical = ?, 
                    descripcion = ?, 
                    presentacion = ?, 
                    imagen_presentacion = ?,
                    logo_artista = ?,
                    estado = ?
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param(
                "issssssi", 
                $this->empresa_id,
                $this->nombre, 
                $this->genero_musical, 
                $this->descripcion, 
                $this->presentacion, 
                $this->imagen_presentacion,
                $this->logo_artista,
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
     * Obtiene un artista por su ID
     * 
     * @param int $id ID del artista a buscar
     * @return object|false Objeto con datos del artista o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            $sql = "SELECT * FROM artistas WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows == 1) {
                $artista = $result->fetch_object();
                $stmt->close();
                return $artista;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una lista de artistas según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @param int $limit Límite de resultados (opcional)
     * @param int $offset Desplazamiento para paginación (opcional)
     * @return array Lista de artistas
     */
    public function getAll($filters = [], $limit = null, $offset = 0)
    {
        try {
            // Construir la consulta base
            $sql = "SELECT * FROM artistas WHERE 1=1";
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
                
                if (isset($filters['genero_musical']) && $filters['genero_musical']) {
                    $sql .= " AND genero_musical = ?";
                    $params[] = $filters['genero_musical'];
                    $types .= "s";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombre LIKE ? OR genero_musical LIKE ? OR descripcion LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "sss";
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
            
            $artistas = [];
            while ($artista = $result->fetch_object()) {
                $artistas[] = $artista;
            }
            
            $stmt->close();
            return $artistas;
        } catch (Exception $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Elimina un artista por su ID
     * 
     * @param int $id ID del artista a eliminar
     * @return bool Resultado de la operación
     */
    public function delete($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            $sql = "DELETE FROM artistas WHERE id = ?";
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
     * Cambia el estado de un artista
     * 
     * @param int $id ID del artista
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
            
            $sql = "UPDATE artistas SET estado = ? WHERE id = ?";
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
     * Verifica si un nombre de artista ya existe en la base de datos para esta empresa
     * 
     * @param string $nombre Nombre a verificar
     * @param int $empresa_id ID de la empresa
     * @param int $exclude_id ID de artista a excluir de la verificación (opcional)
     * @return bool True si el nombre ya existe
     */
    public function nombreExists($nombre, $empresa_id, $exclude_id = null)
    {
        try {
            $sql = "SELECT id FROM artistas WHERE nombre = ? AND empresa_id = ?";
            
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
                $stmt->bind_param("sii", $nombre, $empresa_id, $exclude_id);
            } else {
                $stmt->bind_param("si", $nombre, $empresa_id);
            }
            
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            
            return $exists;
        } catch (Exception $e) {
            error_log("Error en nombreExists: " . $e->getMessage());
            return true; // Devolver true por precaución
        }
    }

    /**
     * Cuenta el número total de artistas según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @return int Número total de artistas
     */
    public function countAll($filters = [])
    {
        try {
            // Construir la consulta base
            $sql = "SELECT COUNT(*) as total FROM artistas WHERE 1=1";
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
                
                if (isset($filters['genero_musical']) && $filters['genero_musical']) {
                    $sql .= " AND genero_musical = ?";
                    $params[] = $filters['genero_musical'];
                    $types .= "s";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombre LIKE ? OR genero_musical LIKE ? OR descripcion LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "sss";
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
    
    /**
     * Obtiene los géneros musicales disponibles
     * 
     * @param int $empresa_id ID de la empresa para filtrar
     * @return array Lista de géneros musicales
     */
    public function getGenerosMusicales($empresa_id)
    {
        try {
            $sql = "SELECT DISTINCT genero_musical FROM artistas WHERE empresa_id = ? ORDER BY genero_musical";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return [];
            }
            
            $stmt->bind_param("i", $empresa_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $generos = [];
            while ($row = $result->fetch_object()) {
                $generos[] = $row->genero_musical;
            }
            
            $stmt->close();
            return $generos;
        } catch (Exception $e) {
            error_log("Error en getGenerosMusicales: " . $e->getMessage());
            return [];
        }
    }
}