<?php
require_once 'config/db.php';

/**
 * Clase Agenda
 * 
 * Modelo para la gestión de eventos en la agenda
 * Permite consultar, filtrar y organizar eventos
 */
class Agenda
{
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
     * Obtiene todos los eventos de una empresa específica
     * 
     * @param int $empresa_id ID de la empresa
     * @param string $estado_evento Estado de los eventos a filtrar (opcional)
     * @param string $fecha_inicio Fecha de inicio para el filtro (opcional)
     * @param string $fecha_fin Fecha fin para el filtro (opcional)
     * @return array Listado de eventos
     */
    public function getAllEventos($empresa_id, $estado_evento = null, $fecha_inicio = null, $fecha_fin = null)
    {
        try {
            // Construir la consulta base con JOIN para obtener información relacionada
            $sql = "SELECT e.*, 
                    c.nombres AS cliente_nombre, c.apellidos AS cliente_apellido,
                    a.nombre AS artista_nombre,
                    g.nombre AS gira_nombre
                    FROM eventos e
                    LEFT JOIN clientes c ON e.cliente_id = c.id
                    LEFT JOIN artistas a ON e.artista_id = a.id
                    LEFT JOIN giras g ON e.gira_id = g.id
                    WHERE e.empresa_id = ?";
            
            // Array para almacenar los parámetros
            $params = [$empresa_id];
            $types = "i";
            
            // Agregar filtros si se proporcionan
            if ($estado_evento) {
                $sql .= " AND e.estado_evento = ?";
                $params[] = $estado_evento;
                $types .= "s";
            }
            
            if ($fecha_inicio) {
                $sql .= " AND e.fecha_evento >= ?";
                $params[] = $fecha_inicio;
                $types .= "s";
            }
            
            if ($fecha_fin) {
                $sql .= " AND e.fecha_evento <= ?";
                $params[] = $fecha_fin;
                $types .= "s";
            }
            
            // Ordenar por fecha del evento (más reciente primero)
            $sql .= " ORDER BY e.fecha_evento DESC";
            
            // Ejecutar la consulta preparada
            $stmt = Database::prepareAndExecute($sql, $types, $params);
            
            if (!$stmt) {
                return [];
            }
            
            $result = $stmt->get_result();
            $eventos = [];
            
            while ($evento = $result->fetch_object()) {
                $eventos[] = $evento;
            }
            
            $stmt->close();
            return $eventos;
            
        } catch (Exception $e) {
            error_log("Error en getAllEventos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene un evento específico por su ID
     * 
     * @param int $id ID del evento
     * @param int $empresa_id ID de la empresa (para seguridad)
     * @return object|false Objeto con datos del evento o false si no existe
     */
    public function getEventoById($id, $empresa_id)
    {
        try {
            $id = (int)$id;
            $empresa_id = (int)$empresa_id;
            
            $sql = "SELECT e.*, 
                    c.nombres AS cliente_nombre, c.apellidos AS cliente_apellido,
                    a.nombre AS artista_nombre,
                    g.nombre AS gira_nombre
                    FROM eventos e
                    LEFT JOIN clientes c ON e.cliente_id = c.id
                    LEFT JOIN artistas a ON e.artista_id = a.id
                    LEFT JOIN giras g ON e.gira_id = g.id
                    WHERE e.id = ? AND e.empresa_id = ?";
            
            $stmt = Database::prepareAndExecute($sql, "ii", [$id, $empresa_id]);
            
            if (!$stmt) {
                return false;
            }
            
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows == 1) {
                $evento = $result->fetch_object();
                $stmt->close();
                return $evento;
            }
            
            $stmt->close();
            return false;
            
        } catch (Exception $e) {
            error_log("Error en getEventoById: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtiene eventos para un rango de fechas (calendario)
     * 
     * @param int $empresa_id ID de la empresa
     * @param string $fecha_inicio Fecha de inicio
     * @param string $fecha_fin Fecha fin
     * @return array Listado de eventos
     */
    public function getEventosCalendario($empresa_id, $fecha_inicio, $fecha_fin)
    {
        try {
            $empresa_id = (int)$empresa_id;
            
            $sql = "SELECT e.*, 
                    c.nombres AS cliente_nombre, c.apellidos AS cliente_apellido,
                    a.nombre AS artista_nombre
                    FROM eventos e
                    LEFT JOIN clientes c ON e.cliente_id = c.id
                    LEFT JOIN artistas a ON e.artista_id = a.id
                    WHERE e.empresa_id = ? 
                    AND e.fecha_evento BETWEEN ? AND ?
                    ORDER BY e.fecha_evento ASC, e.hora_evento ASC";
            
            $stmt = Database::prepareAndExecute($sql, "iss", [$empresa_id, $fecha_inicio, $fecha_fin]);
            
            if (!$stmt) {
                return [];
            }
            
            $result = $stmt->get_result();
            $eventos = [];
            
            while ($evento = $result->fetch_object()) {
                $eventos[] = $evento;
            }
            
            $stmt->close();
            return $eventos;
            
        } catch (Exception $e) {
            error_log("Error en getEventosCalendario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cuenta el número total de eventos por estado
     * 
     * @param int $empresa_id ID de la empresa
     * @return array Contadores por estado
     */
    public function countEventosPorEstado($empresa_id)
    {
        try {
            $empresa_id = (int)$empresa_id;
            
            $sql = "SELECT estado_evento, COUNT(*) as total 
                    FROM eventos 
                    WHERE empresa_id = ? 
                    GROUP BY estado_evento";
            
            $stmt = Database::prepareAndExecute($sql, "i", [$empresa_id]);
            
            if (!$stmt) {
                return [];
            }
            
            $result = $stmt->get_result();
            $contadores = [];
            
            while ($row = $result->fetch_object()) {
                $contadores[$row->estado_evento] = $row->total;
            }
            
            $stmt->close();
            return $contadores;
            
        } catch (Exception $e) {
            error_log("Error en countEventosPorEstado: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtiene próximos eventos (agenda)
     * 
     * @param int $empresa_id ID de la empresa
     * @param int $limit Límite de resultados
     * @return array Listado de próximos eventos
     */
    public function getProximosEventos($empresa_id, $limit = 5)
    {
        try {
            $empresa_id = (int)$empresa_id;
            $limit = (int)$limit;
            
            $fecha_actual = date('Y-m-d');
            
            $sql = "SELECT e.*, 
                    c.nombres AS cliente_nombre, c.apellidos AS cliente_apellido,
                    a.nombre AS artista_nombre
                    FROM eventos e
                    LEFT JOIN clientes c ON e.cliente_id = c.id
                    LEFT JOIN artistas a ON e.artista_id = a.id
                    WHERE e.empresa_id = ? 
                    AND e.fecha_evento >= ?
                    AND e.estado_evento IN ('Confirmado', 'Propuesta')
                    ORDER BY e.fecha_evento ASC, e.hora_evento ASC
                    LIMIT ?";
            
            $stmt = Database::prepareAndExecute($sql, "isi", [$empresa_id, $fecha_actual, $limit]);
            
            if (!$stmt) {
                return [];
            }
            
            $result = $stmt->get_result();
            $eventos = [];
            
            while ($evento = $result->fetch_object()) {
                $eventos[] = $evento;
            }
            
            $stmt->close();
            return $eventos;
            
        } catch (Exception $e) {
            error_log("Error en getProximosEventos: " . $e->getMessage());
            return [];
        }
    }
}