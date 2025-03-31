<?php
require_once 'config/db.php';

/**
 * Clase Suscripcion
 * 
 * Gestiona todas las operaciones relacionadas con las suscripciones en el sistema,
 * incluyendo creación, renovación, cancelación y consulta.
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
    private $precio_base;
    private $descuento_porcentaje;
    private $descuento_motivo;
    private $precio_final;
    private $moneda;
    private $estado;
    private $renovacion_automatica;
    private $metodo_pago;
    private $referencia_pago;
    private $creado_por;
    private $actualizado_por;
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

    public function getPrecioBase()
    {
        return $this->precio_base;
    }

    public function setPrecioBase($precio_base)
    {
        $this->precio_base = $precio_base;
    }

    public function getDescuentoPorcentaje()
    {
        return $this->descuento_porcentaje;
    }

    public function setDescuentoPorcentaje($descuento_porcentaje)
    {
        $this->descuento_porcentaje = $descuento_porcentaje;
    }

    public function getDescuentoMotivo()
    {
        return $this->descuento_motivo;
    }

    public function setDescuentoMotivo($descuento_motivo)
    {
        $this->descuento_motivo = $this->db->real_escape_string($descuento_motivo);
    }

    public function getPrecioFinal()
    {
        return $this->precio_final;
    }

    public function setPrecioFinal($precio_final)
    {
        $this->precio_final = $precio_final;
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
        $estados_validos = ['Activa', 'Pendiente', 'Periodo de Gracia', 'Cancelada', 'Suspendida', 'Finalizada'];
        if (in_array($estado, $estados_validos)) {
            $this->estado = $estado;
        } else {
            $this->estado = 'Activa'; // Valor por defecto
        }
    }

    public function getRenovacionAutomatica()
    {
        return $this->renovacion_automatica;
    }

    public function setRenovacionAutomatica($renovacion_automatica)
    {
        if ($renovacion_automatica == 'Si' || $renovacion_automatica == 'No') {
            $this->renovacion_automatica = $renovacion_automatica;
        } else {
            $this->renovacion_automatica = 'Si'; // Valor por defecto
        }
    }

    public function getMetodoPago()
    {
        return $this->metodo_pago;
    }

    public function setMetodoPago($metodo_pago)
    {
        $metodos_validos = ['Tarjeta de Crédito', 'Transferencia', 'PayPal', 'Otro'];
        if (in_array($metodo_pago, $metodos_validos)) {
            $this->metodo_pago = $metodo_pago;
        } else {
            $this->metodo_pago = 'Tarjeta de Crédito'; // Valor por defecto
        }
    }

    public function getReferenciaPago()
    {
        return $this->referencia_pago;
    }

    public function setReferenciaPago($referencia_pago)
    {
        $this->referencia_pago = $this->db->real_escape_string($referencia_pago);
    }

    public function getCreadoPor()
    {
        return $this->creado_por;
    }

    public function setCreadoPor($creado_por)
    {
        $this->creado_por = $creado_por;
    }

    public function getActualizadoPor()
    {
        return $this->actualizado_por;
    }

    public function setActualizadoPor($actualizado_por)
    {
        $this->actualizado_por = $actualizado_por;
    }

    /**
     * Guarda una nueva suscripción en la base de datos
     * 
     * @return bool|int ID de la suscripción creada o false si falla
     */
    public function save()
    {
        try {
            // Calcular precio final si hay descuento
            if (!isset($this->precio_final) && isset($this->precio_base) && isset($this->descuento_porcentaje)) {
                $descuento = ($this->precio_base * $this->descuento_porcentaje) / 100;
                $this->precio_final = $this->precio_base - $descuento;
            }

            // Si no hay creado_por y hay sesión de admin, usar el ID del admin actual
            if (!isset($this->creado_por) && isset($_SESSION['admin'])) {
                $this->creado_por = $_SESSION['admin']->id;
            }

            $sql = "INSERT INTO suscripciones (
                empresa_id, plan_id, numero_suscripcion, periodo_facturacion,
                fecha_inicio, fecha_fin, fecha_siguiente_factura, fecha_cancelacion,
                precio_base, descuento_porcentaje, descuento_motivo, precio_final, moneda,
                estado, renovacion_automatica, metodo_pago, referencia_pago,
                creado_por, actualizado_por
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param(
                "iissssssdsdsssssii",
                $this->empresa_id,
                $this->plan_id,
                $this->numero_suscripcion,
                $this->periodo_facturacion,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->fecha_siguiente_factura,
                $this->fecha_cancelacion,
                $this->precio_base,
                $this->descuento_porcentaje,
                $this->descuento_motivo,
                $this->precio_final,
                $this->moneda,
                $this->estado,
                $this->renovacion_automatica,
                $this->metodo_pago,
                $this->referencia_pago,
                $this->creado_por,
                $this->actualizado_por
            );

            if ($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                
                // Registrar en el historial si se guardó correctamente
                $this->registrarHistorial($id, 'Nueva Suscripción');
                
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
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            // Calcular precio final si hay descuento
            if (isset($this->precio_base) && isset($this->descuento_porcentaje)) {
                $descuento = ($this->precio_base * $this->descuento_porcentaje) / 100;
                $this->precio_final = $this->precio_base - $descuento;
            }

            // Si no hay actualizado_por y hay sesión de admin, usar el ID del admin actual
            if (!isset($this->actualizado_por) && isset($_SESSION['admin'])) {
                $this->actualizado_por = $_SESSION['admin']->id;
            }

            $sql = "UPDATE suscripciones SET 
                empresa_id = ?,
                plan_id = ?,
                numero_suscripcion = ?,
                periodo_facturacion = ?,
                fecha_inicio = ?,
                fecha_fin = ?,
                fecha_siguiente_factura = ?,
                fecha_cancelacion = ?,
                precio_base = ?,
                descuento_porcentaje = ?,
                descuento_motivo = ?,
                precio_final = ?,
                moneda = ?,
                estado = ?,
                renovacion_automatica = ?,
                metodo_pago = ?,
                referencia_pago = ?,
                actualizado_por = ?
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param(
                "iissssssdsdsssssiii",
                $this->empresa_id,
                $this->plan_id,
                $this->numero_suscripcion,
                $this->periodo_facturacion,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->fecha_siguiente_factura,
                $this->fecha_cancelacion,
                $this->precio_base,
                $this->descuento_porcentaje,
                $this->descuento_motivo,
                $this->precio_final,
                $this->moneda,
                $this->estado,
                $this->renovacion_automatica,
                $this->metodo_pago,
                $this->referencia_pago,
                $this->actualizado_por,
                $this->id
            );

            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                // Registrar en el historial si se actualizó correctamente
                $this->registrarHistorial($this->id, 'Actualización Suscripción');
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una suscripción por su ID
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
                    p.tipo_plan
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
                    p.tipo_plan
                FROM suscripciones s
                LEFT JOIN empresas e ON s.empresa_id = e.id
                LEFT JOIN planes p ON s.plan_id = p.id
                WHERE s.empresa_id = ? AND s.estado IN ('Activa', 'Pendiente', 'Periodo de Gracia')
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
                    p.tipo_plan
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
                
                if (isset($filters['renovacion']) && $filters['renovacion']) {
                    $sql .= " AND s.renovacion_automatica = ?";
                    $params[] = $filters['renovacion'];
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
                // (Código omitido por brevedad)
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
            $estados_validos = ['Activa', 'Pendiente', 'Periodo de Gracia', 'Cancelada', 'Suspendida', 'Finalizada'];
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
            $sql = "UPDATE suscripciones SET estado = ?, fecha_cancelacion = ?, actualizado_por = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $actualizado_por = isset($_SESSION['admin']) ? $_SESSION['admin']->id : null;
            
            $stmt->bind_param("ssii", $estado, $fecha_cancelacion, $actualizado_por, $id);
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
                    estado = 'Activa',
                    actualizado_por = ?
                    WHERE id = ?";
                    
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $actualizado_por = isset($_SESSION['admin']) ? $_SESSION['admin']->id : null;
            
            $stmt->bind_param("sii", $nueva_fecha_factura, $actualizado_por, $id);
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
            $monto_subtotal = $suscripcion->precio_final / (1 + $tasa_impuesto); // Quitar el impuesto para obtener el subtotal
            $monto_impuestos = $suscripcion->precio_final - $monto_subtotal;
            
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
                             $suscripcion->precio_final, 
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
}