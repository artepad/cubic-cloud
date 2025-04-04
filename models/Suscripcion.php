<?php
require_once 'config/db.php';

/**
 * Clase Suscripcion
 * 
 * Gestiona todas las operaciones relacionadas con las suscripciones en el sistema,
 * incluyendo creación, renovación, cancelación y consulta.
 * 
 * Actualizado para reflejar los cambios en la estructura simplificada de la tabla suscripciones.
 */
class Suscripcion
{
    // Propiedades de la clase
    private $id;
    private $empresa_id;
    private $plan_id;
    private $numero_suscripcion;
    private $periodo_facturacion;
    private $fecha_inicio;
    private $fecha_fin;
    private $fecha_siguiente_factura;
    private $fecha_cancelacion;
    private $precio_total;
    private $moneda;
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
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getEmpresaId()
    {
        return $this->empresa_id;
    }

    public function setEmpresaId($empresa_id)
    {
        $this->empresa_id = $empresa_id;
    }

    public function getPlanId()
    {
        return $this->plan_id;
    }

    public function setPlanId($plan_id)
    {
        $this->plan_id = $plan_id;
    }

    public function getNumeroSuscripcion()
    {
        return $this->numero_suscripcion;
    }

    public function setNumeroSuscripcion($numero_suscripcion)
    {
        $this->numero_suscripcion = $this->db->real_escape_string($numero_suscripcion);
    }

    public function getPeriodoFacturacion()
    {
        return $this->periodo_facturacion;
    }

    public function setPeriodoFacturacion($periodo_facturacion)
    {
        $periodos_validos = ['Mensual', 'Semestral', 'Anual'];
        if (in_array($periodo_facturacion, $periodos_validos)) {
            $this->periodo_facturacion = $periodo_facturacion;
        } else {
            $this->periodo_facturacion = 'Mensual'; // Valor por defecto
        }
    }

    public function getFechaInicio()
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio($fecha_inicio)
    {
        $this->fecha_inicio = $fecha_inicio;
    }

    public function getFechaFin()
    {
        return $this->fecha_fin;
    }

    public function setFechaFin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;
    }

    public function getFechaSiguienteFactura()
    {
        return $this->fecha_siguiente_factura;
    }

    public function setFechaSiguienteFactura($fecha_siguiente_factura)
    {
        $this->fecha_siguiente_factura = $fecha_siguiente_factura;
    }

    public function getFechaCancelacion()
    {
        return $this->fecha_cancelacion;
    }

    public function setFechaCancelacion($fecha_cancelacion)
    {
        $this->fecha_cancelacion = $fecha_cancelacion;
    }

    public function getPrecioTotal()
    {
        return $this->precio_total;
    }

    public function setPrecioTotal($precio_total)
    {
        $this->precio_total = $precio_total;
    }

    public function getMoneda()
    {
        return $this->moneda;
    }

    public function setMoneda($moneda)
    {
        $monedas_validas = ['CLP', 'USD', 'EUR'];
        if (in_array($moneda, $monedas_validas)) {
            $this->moneda = $moneda;
        } else {
            $this->moneda = 'CLP'; // Valor por defecto
        }
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $estados_validos = ['Activa', 'Pendiente', 'Suspendida', 'Cancelada', 'Finalizada'];
        if (in_array($estado, $estados_validos)) {
            $this->estado = $estado;
        } else {
            $this->estado = 'Activa'; // Valor por defecto
        }
    }

    /**
     * Guarda una nueva suscripción en la base de datos
     * Adaptado para la estructura simplificada de la tabla
     * 
     * @return bool|int ID de la suscripción creada o false si falla
     */
    public function save()
    {
        try {
            $sql = "INSERT INTO suscripciones (
                empresa_id, plan_id, numero_suscripcion, periodo_facturacion,
                fecha_inicio, fecha_fin, fecha_siguiente_factura, fecha_cancelacion,
                precio_total, moneda, estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param(
                "iisssssdsss",
                $this->empresa_id,
                $this->plan_id,
                $this->numero_suscripcion,
                $this->periodo_facturacion,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->fecha_siguiente_factura,
                $this->fecha_cancelacion,
                $this->precio_total,
                $this->moneda,
                $this->estado
            );

            if ($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                
                // Registrar en el historial si se guardó correctamente
                $this->registrarHistorial($id, 'Nueva Suscripción');
                
                // Actualizar límites en la empresa según el plan
                $this->actualizarLimitesEmpresa($this->empresa_id, $this->plan_id);
                
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
     * Actualiza una suscripción existente en la base de datos
     * Adaptado para la estructura simplificada de la tabla
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            $sql = "UPDATE suscripciones SET 
                    empresa_id = ?,
                    plan_id = ?,
                    numero_suscripcion = ?,
                    periodo_facturacion = ?,
                    fecha_inicio = ?,
                    fecha_fin = ?,
                    fecha_siguiente_factura = ?,
                    fecha_cancelacion = ?,
                    precio_total = ?,
                    moneda = ?,
                    estado = ?
                    WHERE id = ?";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param(
                "iisssssdssi",
                $this->empresa_id,
                $this->plan_id,
                $this->numero_suscripcion,
                $this->periodo_facturacion,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->fecha_siguiente_factura,
                $this->fecha_cancelacion,
                $this->precio_total,
                $this->moneda,
                $this->estado,
                $this->id
            );

            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                // Registrar en el historial si se actualizó correctamente
                $this->registrarHistorial($this->id, 'Actualización Suscripción');
                
                // Actualizar límites en la empresa si cambió el plan
                $this->actualizarLimitesEmpresa($this->empresa_id, $this->plan_id);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza los límites de una empresa según el plan de suscripción
     * Obtiene los límites directamente desde la tabla planes, ya que se eliminaron
     * de la tabla empresas para evitar redundancia
     * 
     * @param int $empresa_id ID de la empresa
     * @param int $plan_id ID del plan de suscripción
     * @return bool Resultado de la operación
     */
    private function actualizarLimitesEmpresa($empresa_id, $plan_id)
    {
        try {
            // Obtener información del plan
            $sql_plan = "SELECT max_usuarios, max_artistas, max_eventos FROM planes WHERE id = ?";
            $stmt_plan = $this->db->prepare($sql_plan);
            
            if (!$stmt_plan) {
                error_log("Error preparando consulta de plan: " . $this->db->error);
                return false;
            }
            
            $stmt_plan->bind_param("i", $plan_id);
            $stmt_plan->execute();
            $result_plan = $stmt_plan->get_result();
            
            if ($result_plan && $result_plan->num_rows == 1) {
                $plan = $result_plan->fetch_object();
                $stmt_plan->close();
                
                // Aunque ya no actualizamos los límites directamente en la empresa,
                // podemos hacer otras actualizaciones relacionadas con el cambio de plan
                // Por ejemplo, actualizar el estado de la empresa o algún otro campo
                
                return true;
            }
            
            $stmt_plan->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en actualizarLimitesEmpresa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una suscripción por su ID
     * Actualizado para incluir información del plan que contiene los límites
     * 
     * @param int $id ID de la suscripción a buscar
     * @return object|false Objeto con datos de la suscripción o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            $sql = "SELECT s.*, 
                    e.nombre as empresa_nombre, 
                    p.nombre as plan_nombre, 
                    p.tipo_plan,
                    p.max_usuarios,
                    p.max_artistas,
                    p.max_eventos
                FROM suscripciones s
                LEFT JOIN empresas e ON s.empresa_id = e.id
                LEFT JOIN planes p ON s.plan_id = p.id
                WHERE s.id = ?";
                
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $suscripcion = $result->fetch_object();
                $stmt->close();
                return $suscripcion;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la suscripción activa de una empresa
     * Incluye también los límites desde la tabla planes
     * 
     * @param int $empresa_id ID de la empresa
     * @return object|false Objeto con datos de la suscripción o false si no hay ninguna activa
     */
    public function getActivaByEmpresa($empresa_id)
    {
        try {
            $empresa_id = (int)$empresa_id; // Asegurar que es un entero

            $sql = "SELECT s.*, 
                    e.nombre as empresa_nombre, 
                    p.nombre as plan_nombre, 
                    p.tipo_plan,
                    p.max_usuarios,
                    p.max_artistas,
                    p.max_eventos
                FROM suscripciones s
                LEFT JOIN empresas e ON s.empresa_id = e.id
                LEFT JOIN planes p ON s.plan_id = p.id
                WHERE s.empresa_id = ? AND s.estado IN ('Activa', 'Pendiente')
                ORDER BY s.id DESC LIMIT 1";
                
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $empresa_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $suscripcion = $result->fetch_object();
                $stmt->close();
                return $suscripcion;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getActivaByEmpresa: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todas las suscripciones con posibilidad de filtrado
     * Incluye información de límites desde la tabla planes
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @param int $limit Límite de resultados (opcional)
     * @param int $offset Desplazamiento para paginación (opcional)
     * @return array Lista de suscripciones
     */
    public function getAll($filters = [], $limit = null, $offset = 0)
    {
        try {
            // Construir la consulta base
            $sql = "SELECT s.*, 
                    e.nombre as empresa_nombre, 
                    p.nombre as plan_nombre, 
                    p.tipo_plan,
                    p.max_usuarios,
                    p.max_artistas,
                    p.max_eventos
                FROM suscripciones s
                LEFT JOIN empresas e ON s.empresa_id = e.id
                LEFT JOIN planes p ON s.plan_id = p.id
                WHERE 1=1";
                
            $params = [];
            $types = "";
            
            // Aplicar filtros si existen
            if (!empty($filters)) {
                if (isset($filters['empresa_id']) && $filters['empresa_id']) {
                    $sql .= " AND s.empresa_id = ?";
                    $params[] = $filters['empresa_id'];
                    $types .= "i";
                }
                
                if (isset($filters['plan_id']) && $filters['plan_id']) {
                    $sql .= " AND s.plan_id = ?";
                    $params[] = $filters['plan_id'];
                    $types .= "i";
                }
                
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND s.estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }
                
                if (isset($filters['periodo']) && $filters['periodo']) {
                    $sql .= " AND s.periodo_facturacion = ?";
                    $params[] = $filters['periodo'];
                    $types .= "s";
                }
                
                if (isset($filters['vencidas']) && $filters['vencidas'] === true) {
                    $sql .= " AND s.fecha_siguiente_factura < CURDATE() AND s.estado NOT IN ('Cancelada', 'Finalizada')";
                }
                
                if (isset($filters['proximo_vencimiento']) && $filters['proximo_vencimiento']) {
                    $dias = (int)$filters['proximo_vencimiento'];
                    $sql .= " AND s.fecha_siguiente_factura BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
                    $params[] = $dias;
                    $types .= "i";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (s.numero_suscripcion LIKE ? OR e.nombre LIKE ? OR p.nombre LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "sss";
                }
            }
            
            // Ordenar resultados
            $sql .= " ORDER BY s.id DESC";
            
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
            
            $suscripciones = [];
            while ($suscripcion = $result->fetch_object()) {
                $suscripciones[] = $suscripcion;
            }
            
            $stmt->close();
            return $suscripciones;
        } catch (Exception $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cuenta el número total de suscripciones según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @return int Número total de suscripciones
     */
    public function countAll($filters = [])
    {
        try {
            // Construir la consulta base
            $sql = "SELECT COUNT(*) as total FROM suscripciones s
                    LEFT JOIN empresas e ON s.empresa_id = e.id
                    LEFT JOIN planes p ON s.plan_id = p.id
                    WHERE 1=1";
                    
            $params = [];
            $types = "";
            
            // Aplicar filtros si existen (igual que en getAll)
            if (!empty($filters)) {
                if (isset($filters['empresa_id']) && $filters['empresa_id']) {
                    $sql .= " AND s.empresa_id = ?";
                    $params[] = $filters['empresa_id'];
                    $types .= "i";
                }
                
                // Agregar el resto de filtros igual que en getAll...
                if (isset($filters['plan_id']) && $filters['plan_id']) {
                    $sql .= " AND s.plan_id = ?";
                    $params[] = $filters['plan_id'];
                    $types .= "i";
                }
                
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND s.estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }
                
                if (isset($filters['periodo']) && $filters['periodo']) {
                    $sql .= " AND s.periodo_facturacion = ?";
                    $params[] = $filters['periodo'];
                    $types .= "s";
                }
                
                if (isset($filters['vencidas']) && $filters['vencidas'] === true) {
                    $sql .= " AND s.fecha_siguiente_factura < CURDATE() AND s.estado NOT IN ('Cancelada', 'Finalizada')";
                }
                
                if (isset($filters['proximo_vencimiento']) && $filters['proximo_vencimiento']) {
                    $dias = (int)$filters['proximo_vencimiento'];
                    $sql .= " AND s.fecha_siguiente_factura BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
                    $params[] = $dias;
                    $types .= "i";
                }
                
                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (s.numero_suscripcion LIKE ? OR e.nombre LIKE ? OR p.nombre LIKE ?)";
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
     * Cambia el estado de una suscripción
     * 
     * @param int $id ID de la suscripción
     * @param string $estado Nuevo estado
     * @param string $motivo Motivo del cambio (opcional)
     * @return bool Resultado de la operación
     */
    public function cambiarEstado($id, $estado, $motivo = '')
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            // Validar estado
            $estados_validos = ['Activa', 'Pendiente', 'Suspendida', 'Cancelada', 'Finalizada'];
            if (!in_array($estado, $estados_validos)) {
                return false;
            }
            
            // Obtener datos actuales para el historial
            $suscripcion_actual = $this->getById($id);
            if (!$suscripcion_actual) {
                return false;
            }
            
            // Si estado es Cancelada, establecer fecha_cancelacion
            $fecha_cancelacion = null;
            if ($estado == 'Cancelada') {
                $fecha_cancelacion = date('Y-m-d');
            }
            
            // Actualizar el estado
            $sql = "UPDATE suscripciones SET estado = ?, fecha_cancelacion = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("ssi", $estado, $fecha_cancelacion, $id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                // Registrar en el historial
                $tipo_cambio = 'Cambio Estado';
                if ($estado == 'Cancelada') {
                    $tipo_cambio = 'Cancelación';
                } elseif ($estado == 'Suspendida') {
                    $tipo_cambio = 'Suspensión';
                } elseif ($estado == 'Activa' && $suscripcion_actual->estado != 'Activa') {
                    $tipo_cambio = 'Reactivación';
                }
                
                $this->registrarHistorial($id, $tipo_cambio, $suscripcion_actual->estado, $estado, $motivo);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Renueva una suscripción, actualizando sus fechas y creando registro en el historial
     * 
     * @param int $id ID de la suscripción
     * @return bool Resultado de la operación
     */
    public function renovar($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            // Obtener datos actuales
            $suscripcion = $this->getById($id);
            if (!$suscripcion || $suscripcion->estado == 'Cancelada' || $suscripcion->estado == 'Finalizada') {
                return false;
            }
            
            // Calcular nueva fecha de facturación según el período
            $nueva_fecha = date('Y-m-d');
            $periodo = $suscripcion->periodo_facturacion;
            
            switch ($periodo) {
                case 'Mensual':
                    $nueva_fecha_factura = date('Y-m-d', strtotime('+1 month'));
                    break;
                case 'Semestral':
                    $nueva_fecha_factura = date('Y-m-d', strtotime('+6 months'));
                    break;
                case 'Anual':
                    $nueva_fecha_factura = date('Y-m-d', strtotime('+1 year'));
                    break;
                default:
                    $nueva_fecha_factura = date('Y-m-d', strtotime('+1 month'));
            }
            
            // Actualizar la suscripción
            $sql = "UPDATE suscripciones SET 
                    fecha_siguiente_factura = ?,
                    estado = 'Activa'
                    WHERE id = ?";
                    
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("si", $nueva_fecha_factura, $id);
            $result = $stmt->execute();
            $stmt->close();
            
            if ($result) {
                // Registrar en el historial
                $this->registrarHistorial($id, 'Renovación', 
                                          $suscripcion->estado, 'Activa', 
                                          'Renovación automática. Nuevo vencimiento: ' . $nueva_fecha_factura);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en renovar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra un cambio en el historial de la suscripción
     * 
     * @param int $suscripcion_id ID de la suscripción
     * @param string $tipo_cambio Tipo de cambio
     * @param string $estado_anterior Estado anterior (opcional)
     * @param string $estado_nuevo Estado nuevo (opcional)
     * @param string $descripcion Descripción del cambio (opcional)
     * @return bool Resultado de la operación
     */
    public function registrarHistorial($suscripcion_id, $tipo_cambio, $estado_anterior = null, $estado_nuevo = null, $descripcion = '')
    {
        try {
            // Verificar si existe la tabla de historial
            $check_sql = "SHOW TABLES LIKE 'historial_cambios_suscripcion'";
            $check_result = $this->db->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                // Si no existe la tabla, no podemos registrar el historial pero no es un error crítico
                return true;
            }
            
            $sql = "INSERT INTO historial_cambios_suscripcion (
                    suscripcion_id, usuario_id, tipo_cambio,
                    estado_anterior, estado_nuevo, descripcion, fecha_efectiva
                ) VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
                
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta historial: " . $this->db->error);
                return false;
            }
            
            $usuario_id = isset($_SESSION['admin']) ? $_SESSION['admin']->id : null;
            
            $stmt->bind_param("iissss", 
                             $suscripcion_id, 
                             $usuario_id, 
                             $tipo_cambio, 
                             $estado_anterior, 
                             $estado_nuevo, 
                             $descripcion);
                             
            $result = $stmt->execute();
            $stmt->close();
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en registrarHistorial: " . $e->getMessage());
            // No propagamos este error ya que es secundario
            return false;
        }
    }

    /**
     * Crea una factura para una suscripción
     * 
     * @param int $suscripcion_id ID de la suscripción
     * @return int|bool ID de la factura creada o false si falla
     */
    public function crearFactura($suscripcion_id)
    {
        try {
            // Verificar si existe la tabla de facturas
            $check_sql = "SHOW TABLES LIKE 'facturas'";
            $check_result = $this->db->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                // Si no existe la tabla, no podemos crear la factura
                return false;
            }
            
            // Obtener datos de la suscripción
            $suscripcion = $this->getById($suscripcion_id);
            if (!$suscripcion) {
                return false;
            }
            
            // Generar número de factura único
            $numero_factura = 'F-' . date('Ymd') . '-' . $suscripcion_id;
            
            // Calcular impuestos (19% en Chile, por ejemplo)
            $tasa_impuesto = 0.19; // 19% de IVA
            $monto_subtotal = $suscripcion->precio_total / (1 + $tasa_impuesto); // Quitar el impuesto para obtener el subtotal
            $monto_impuestos = $suscripcion->precio_total - $monto_subtotal;
            
            // Calcular período facturado
            $periodo_inicio = date('Y-m-d');
            $periodo_fin = null;
            
            switch ($suscripcion->periodo_facturacion) {
                case 'Mensual':
                    $periodo_fin = date('Y-m-d', strtotime('+1 month'));
                    break;
                case 'Semestral':
                    $periodo_fin = date('Y-m-d', strtotime('+6 months'));
                    break;
                case 'Anual':
                    $periodo_fin = date('Y-m-d', strtotime('+1 year'));
                    break;
            }
            
            // Insertar factura
            $sql = "INSERT INTO facturas (
                    suscripcion_id, empresa_id, numero_factura,
                    fecha_emision, fecha_vencimiento, monto_subtotal,
                    monto_impuestos, monto_total, moneda,
                    periodo_inicio, periodo_fin, estado
                ) VALUES (?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 10 DAY), ?, ?, ?, ?, ?, ?, 'Pendiente')";
                
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta factura: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("iisdddsss", 
                             $suscripcion_id, 
                             $suscripcion->empresa_id, 
                             $numero_factura, 
                             $monto_subtotal, 
                             $monto_impuestos, 
                             $suscripcion->precio_total, 
                             $suscripcion->moneda, 
                             $periodo_inicio, 
                             $periodo_fin);
                             
            if ($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                return $id;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en crearFactura: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene los límites actuales de una empresa según su plan activo
     * Es útil para sustituir los campos eliminados de la tabla empresas
     * 
     * @param int $empresa_id ID de la empresa
     * @return object|false Objeto con límites o false si no hay suscripción activa
     */
    public function getLimitesEmpresaByPlan($empresa_id)
    {
        try {
            // Obtener suscripción activa
            $suscripcion = $this->getActivaByEmpresa($empresa_id);
            
            if (!$suscripcion) {
                return false;
            }
            
            // Crear objeto con límites
            $limites = new stdClass();
            $limites->usuarios = $suscripcion->max_usuarios;
            $limites->artistas = $suscripcion->max_artistas;
            $limites->eventos = $suscripcion->max_eventos;
            
            return $limites;
        } catch (Exception $e) {
            error_log("Error en getLimitesEmpresaByPlan: " . $e->getMessage());
            return false;
        }
    }
}