<?php
/**
 * CUBIC SETUP - Configuración inicial del sistema
 * 
 * Este script configura todos los elementos necesarios para iniciar el sistema:
 * - Administrador del sistema (Super Admin)
 * - Empresa de prueba
 * - Plan básico "Eco"
 * - Suscripción activa
 * - Usuarios (Admin, Vendedor, Tour Manager)
 * 
 * IMPORTANTE: Por seguridad, este script debe ejecutarse solo en un entorno local 
 * controlado y eliminarse inmediatamente después de su uso.
 */

require_once 'autoload.php';
require_once 'config/parameters.php';
require_once 'config/db.php';

// Constantes de configuración
define('ADMIN_PASSWORD', '8787');
define('USER_PASSWORD', '8787');
define('LOG_FILE', 'logs/cubic_setup.log');

/**
 * Función para registrar acciones en el log
 */
function logAction($action, $details) {
    $logFile = LOG_FILE;
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $logMessage = "[$timestamp] IP: $ip | $action | $details" . PHP_EOL;
    
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Función para mostrar mensajes de éxito
 */
function mostrarExito($titulo, $contenido) {
    echo "<div style='background:#e9ffe9;border:1px solid #3c3;padding:15px;margin:20px;border-radius:5px;'>";
    echo "<h3>$titulo</h3>";
    echo $contenido;
    echo "</div>";
}

/**
 * Función para mostrar mensajes de error
 */
function mostrarError($titulo, $mensaje) {
    echo "<div style='background:#ffe9e9;border:1px solid #c33;padding:15px;margin:20px;border-radius:5px;'>";
    echo "<h3>$titulo</h3>";
    echo "<p>$mensaje</p>";
    echo "</div>";
}

/**
 * Función para mostrar alertas informativas
 */
function mostrarAlerta($titulo, $mensaje) {
    echo "<div style='background:#fff9e9;border:1px solid #fc3;padding:15px;margin:20px;border-radius:5px;'>";
    echo "<h3>$titulo</h3>";
    echo "<p>$mensaje</p>";
    echo "</div>";
}

/**
 * Clase para gestionar la configuración inicial del sistema
 */
class CubicSetup {
    private $db;
    private $fecha_actual;
    
    // Resultados de configuración
    private $admin_id = null;
    private $empresa_id = null;
    private $plan_id = null;
    private $suscripcion_id = null;
    private $usuarios_ids = [];
    private $usuario_admin_id = null; // Variable para almacenar el ID del usuario administrador
    
    // Constructor
    public function __construct() {
        // Validación de seguridad
        if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] !== 'localhost') {
            die('Este script solo puede ejecutarse en entorno local');
        }
        
        // Inicializar conexión y fecha
        $this->db = Database::connect();
        $this->fecha_actual = date('Y-m-d H:i:s');
    }
    
    /**
     * Crea o actualiza el administrador del sistema
     */
    public function crearSuperAdmin() {
        try {
            // Datos del administrador
            $nombre = "Miguel";
            $apellido = "Saavedra";
            $email = "admin@admin.cl";
            $password = ADMIN_PASSWORD;
            $telefono = "+56987879312";
            
            // Encriptación segura
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Verificar si existe
            $stmt = $this->db->prepare("SELECT id FROM system_admins WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                // Actualizar admin existente
                $admin = $result->fetch_object();
                $this->admin_id = $admin->id;
                
                $updateStmt = $this->db->prepare("UPDATE system_admins SET 
                                               nombre = ?, 
                                               apellido = ?, 
                                               password = ?, 
                                               telefono = ?,
                                               estado = 'Activo', 
                                               intentos_fallidos = 0,
                                               fecha_actualizacion = ?
                                               WHERE id = ?");
                
                $updateStmt->bind_param("sssssi", 
                    $nombre, 
                    $apellido, 
                    $password_hash, 
                    $telefono, 
                    $this->fecha_actual, 
                    $admin->id
                );
                
                if ($updateStmt->execute()) {
                    logAction("UPDATE_ADMIN", "Super Admin actualizado: $email");
                    return "Administrador del sistema actualizado: $email";
                } else {
                    throw new Exception("Error al actualizar el administrador: " . $updateStmt->error);
                }
                
                $updateStmt->close();
            } else {
                // Crear nuevo administrador
                $insertStmt = $this->db->prepare("INSERT INTO system_admins 
                                              (nombre, apellido, email, password, telefono, estado, fecha_creacion) 
                                              VALUES (?, ?, ?, ?, ?, 'Activo', ?)");
                
                $insertStmt->bind_param("ssssss", 
                    $nombre, 
                    $apellido, 
                    $email, 
                    $password_hash, 
                    $telefono, 
                    $this->fecha_actual
                );
                
                if ($insertStmt->execute()) {
                    $this->admin_id = $insertStmt->insert_id;
                    logAction("CREATE_ADMIN", "Super Admin creado: $email");
                    return "Administrador del sistema creado: $email";
                } else {
                    throw new Exception("Error al crear el administrador: " . $insertStmt->error);
                }
                
                $insertStmt->close();
            }
            
            $stmt->close();
        } catch (Exception $e) {
            logAction("ERROR", "Error en crearSuperAdmin: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Crea o actualiza los usuarios del sistema
     */
    public function crearUsuarios() {
        try {
            // Definir datos de los usuarios
            $usuarios = [
                [
                    'nombre' => 'Admin',
                    'apellido' => 'Demo',
                    'email' => 'admin@demo.cl',
                    'password' => USER_PASSWORD,
                    'telefono' => '+56987654321',
                    'pais' => 'Chile',
                    'codigo_pais' => 'CL',
                    'numero_identificacion' => '12345678-9',
                    'tipo_identificacion' => 'RUT',
                    'tipo_usuario' => 'ADMIN',
                    'estado' => 'Activo'
                ],
                [
                    'nombre' => 'Vendedor',
                    'apellido' => 'Demo',
                    'email' => 'vendedor@demo.cl',
                    'password' => USER_PASSWORD,
                    'telefono' => '+56987654322',
                    'pais' => 'Chile',
                    'codigo_pais' => 'CL',
                    'numero_identificacion' => '12345678-8',
                    'tipo_identificacion' => 'RUT',
                    'tipo_usuario' => 'VENDEDOR',
                    'estado' => 'Activo'
                ],
                [
                    'nombre' => 'Tour',
                    'apellido' => 'Manager',
                    'email' => 'tour@demo.cl',
                    'password' => USER_PASSWORD,
                    'telefono' => '+56987654323',
                    'pais' => 'Chile',
                    'codigo_pais' => 'CL',
                    'numero_identificacion' => '12345678-7',
                    'tipo_identificacion' => 'RUT',
                    'tipo_usuario' => 'TOUR_MANAGER',
                    'estado' => 'Activo'
                ]
            ];
            
            $usuario_admin_id = null;
            $usuarios_creados = 0;
            $usuarios_actualizados = 0;
            
            // Crear o actualizar los usuarios
            foreach ($usuarios as $usuario) {
                // Hash seguro de la contraseña
                $password_hash = password_hash($usuario['password'], PASSWORD_BCRYPT, ['cost' => 12]);
                
                // Verificar si el usuario existe
                $stmt = $this->db->prepare("SELECT id FROM usuarios WHERE email = ?");
                $stmt->bind_param("s", $usuario['email']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    // Actualizar usuario existente
                    $user = $result->fetch_object();
                    $user_id = $user->id;
                    
                    $updateStmt = $this->db->prepare("UPDATE usuarios SET 
                                           nombre = ?, 
                                           apellido = ?, 
                                           password = ?, 
                                           telefono = ?,
                                           pais = ?,
                                           codigo_pais = ?,
                                           numero_identificacion = ?,
                                           tipo_identificacion = ?,
                                           tipo_usuario = ?,
                                           estado = ?,
                                           intentos_fallidos = 0,
                                           fecha_actualizacion = ?
                                           WHERE id = ?");
                    
                    $updateStmt->bind_param("sssssssssssi", 
                        $usuario['nombre'], 
                        $usuario['apellido'], 
                        $password_hash, 
                        $usuario['telefono'], 
                        $usuario['pais'], 
                        $usuario['codigo_pais'], 
                        $usuario['numero_identificacion'], 
                        $usuario['tipo_identificacion'], 
                        $usuario['tipo_usuario'], 
                        $usuario['estado'], 
                        $this->fecha_actual, 
                        $user_id
                    );
                    
                    if ($updateStmt->execute()) {
                        logAction("UPDATE_USER", "Usuario actualizado: {$usuario['email']}");
                        $this->usuarios_ids[] = $user_id;
                        $usuarios_actualizados++;
                        
                        if ($usuario['tipo_usuario'] === 'ADMIN') {
                            $usuario_admin_id = $user_id;
                        }
                    } else {
                        throw new Exception("Error al actualizar el usuario: " . $updateStmt->error);
                    }
                    
                    $updateStmt->close();
                } else {
                    // Crear nuevo usuario
                    $insertStmt = $this->db->prepare("INSERT INTO usuarios 
                                          (nombre, apellido, email, password, telefono, pais, codigo_pais, 
                                           numero_identificacion, tipo_identificacion, tipo_usuario, estado, fecha_creacion) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $insertStmt->bind_param("ssssssssssss", 
                        $usuario['nombre'], 
                        $usuario['apellido'], 
                        $usuario['email'], 
                        $password_hash, 
                        $usuario['telefono'], 
                        $usuario['pais'], 
                        $usuario['codigo_pais'], 
                        $usuario['numero_identificacion'], 
                        $usuario['tipo_identificacion'], 
                        $usuario['tipo_usuario'], 
                        $usuario['estado'], 
                        $this->fecha_actual
                    );
                    
                    if ($insertStmt->execute()) {
                        $user_id = $insertStmt->insert_id;
                        logAction("CREATE_USER", "Usuario creado: {$usuario['email']}");
                        $this->usuarios_ids[] = $user_id;
                        $usuarios_creados++;
                        
                        if ($usuario['tipo_usuario'] === 'ADMIN') {
                            $usuario_admin_id = $user_id;
                        }
                    } else {
                        throw new Exception("Error al crear el usuario: " . $insertStmt->error);
                    }
                    
                    $insertStmt->close();
                }
                
                $stmt->close();
            }
            
            // Guardar el ID del usuario administrador
            if ($usuario_admin_id) {
                $this->usuario_admin_id = $usuario_admin_id;
            } else {
                throw new Exception("No se pudo identificar un usuario administrador");
            }
            
            return "Se crearon $usuarios_creados y actualizaron $usuarios_actualizados usuarios";
        } catch (Exception $e) {
            logAction("ERROR", "Error en crearUsuarios: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Crea o actualiza la empresa
     */
    public function crearEmpresa() {
        try {
            if (!$this->usuario_admin_id) {
                throw new Exception("No se puede crear la empresa sin un usuario administrador");
            }
            
            // Verificar si ya existe la empresa para este usuario
            $checkEmpresa = $this->db->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
            $checkEmpresa->bind_param("i", $this->usuario_admin_id);
            $checkEmpresa->execute();
            $empresaResult = $checkEmpresa->get_result();
            
            $nombre_empresa = "Demo Producciones";
            $rut_empresa = "76123456-7";
            $direccion = "Av. Providencia 1234, Santiago";
            $telefono = "+56222222222";
            $email = "contacto@demoproducciones.cl";
            $pais = "Chile";
            $codigo_pais = "CL";
            $estado = "activa";
            
            if ($empresaResult->num_rows > 0) {
                // Actualizar empresa existente
                $empresa = $empresaResult->fetch_object();
                $this->empresa_id = $empresa->id;
                
                $updateEmpresa = $this->db->prepare("UPDATE empresas SET 
                                            nombre = ?, 
                                            identificacion_fiscal = ?,
                                            direccion = ?,
                                            telefono = ?,
                                            email_contacto = ?,
                                            pais = ?,
                                            codigo_pais = ?,
                                            estado = ?,
                                            fecha_actualizacion = ?
                                            WHERE id = ?");
                
                $updateEmpresa->bind_param("sssssssssi", 
                    $nombre_empresa,
                    $rut_empresa,
                    $direccion,
                    $telefono,
                    $email,
                    $pais,
                    $codigo_pais,
                    $estado,
                    $this->fecha_actual,
                    $this->empresa_id
                );
                
                if ($updateEmpresa->execute()) {
                    logAction("UPDATE_EMPRESA", "Empresa actualizada: $nombre_empresa");
                    $updateEmpresa->close();
                    return "Empresa actualizada: $nombre_empresa";
                } else {
                    throw new Exception("Error al actualizar la empresa: " . $updateEmpresa->error);
                }
            } else {
                // Crear empresa nueva
                $insertEmpresa = $this->db->prepare("INSERT INTO empresas 
                                            (usuario_id, nombre, identificacion_fiscal, direccion, telefono, 
                                            email_contacto, pais, codigo_pais, estado, fecha_creacion) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $insertEmpresa->bind_param("isssssssss", 
                    $this->usuario_admin_id,
                    $nombre_empresa,
                    $rut_empresa,
                    $direccion,
                    $telefono,
                    $email,
                    $pais,
                    $codigo_pais,
                    $estado,
                    $this->fecha_actual
                );
                
                if ($insertEmpresa->execute()) {
                    $this->empresa_id = $insertEmpresa->insert_id;
                    logAction("CREATE_EMPRESA", "Empresa creada: $nombre_empresa");
                    $insertEmpresa->close();
                    return "Empresa creada: $nombre_empresa";
                } else {
                    throw new Exception("Error al crear la empresa: " . $insertEmpresa->error);
                }
            }
            
            $checkEmpresa->close();
        } catch (Exception $e) {
            logAction("ERROR", "Error en crearEmpresa: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Crea o actualiza el plan básico "Eco"
     */
    public function crearPlan() {
        try {
            // Verificar si el plan ya existe
            $checkPlan = $this->db->prepare("SELECT id FROM planes WHERE nombre = ?");
            $nombre_plan = "Eco";
            $checkPlan->bind_param("s", $nombre_plan);
            $checkPlan->execute();
            $planResult = $checkPlan->get_result();
            
            $descripcion = "Plan básico para pequeñas productoras";
            $tipo_plan = "Básico";
            $precio_mensual = 29900;
            $precio_semestral = 27900 * 6;
            $precio_anual = 24900 * 12;
            $moneda = "CLP";
            $max_usuarios = 3;
            $max_artistas = 5;
            $max_eventos = 10;
            $estado = "Activo";
            
            if ($planResult->num_rows > 0) {
                // Actualizar el plan existente
                $plan = $planResult->fetch_object();
                $this->plan_id = $plan->id;
                
                $updatePlan = $this->db->prepare("UPDATE planes SET 
                                      descripcion = ?,
                                      tipo_plan = ?,
                                      precio_mensual = ?,
                                      precio_semestral = ?,
                                      precio_anual = ?,
                                      moneda = ?,
                                      max_usuarios = ?,
                                      max_artistas = ?,
                                      max_eventos = ?,
                                      estado = ?,
                                      fecha_actualizacion = ?
                                      WHERE id = ?");
                
                $updatePlan->bind_param("ssdddsiiiisi", 
                    $descripcion,
                    $tipo_plan,
                    $precio_mensual,
                    $precio_semestral,
                    $precio_anual,
                    $moneda,
                    $max_usuarios,
                    $max_artistas,
                    $max_eventos,
                    $estado,
                    $this->fecha_actual,
                    $this->plan_id
                );
                
                if ($updatePlan->execute()) {
                    logAction("UPDATE_PLAN", "Plan actualizado: $nombre_plan");
                    $updatePlan->close();
                    return "Plan actualizado: $nombre_plan";
                } else {
                    throw new Exception("Error al actualizar el plan: " . $updatePlan->error);
                }
            } else {
                // Crear nuevo plan
                $insertPlan = $this->db->prepare("INSERT INTO planes 
                                      (nombre, descripcion, tipo_plan, precio_mensual, precio_semestral, 
                                      precio_anual, moneda, max_usuarios, max_artistas, max_eventos, 
                                      estado, fecha_creacion) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $insertPlan->bind_param("sssdddsiiiss", 
                    $nombre_plan,
                    $descripcion,
                    $tipo_plan,
                    $precio_mensual,
                    $precio_semestral,
                    $precio_anual,
                    $moneda,
                    $max_usuarios,
                    $max_artistas,
                    $max_eventos,
                    $estado,
                    $this->fecha_actual
                );
                
                if ($insertPlan->execute()) {
                    $this->plan_id = $insertPlan->insert_id;
                    logAction("CREATE_PLAN", "Plan creado: $nombre_plan");
                    $insertPlan->close();
                    return "Plan creado: $nombre_plan";
                } else {
                    throw new Exception("Error al crear el plan: " . $insertPlan->error);
                }
            }
            
            $checkPlan->close();
        } catch (Exception $e) {
            logAction("ERROR", "Error en crearPlan: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Crea o actualiza la suscripción
     */
    public function crearSuscripcion() {
        try {
            if (!$this->empresa_id || !$this->plan_id) {
                throw new Exception("No se puede crear la suscripción sin una empresa y un plan");
            }
            
            // Verificar si la suscripción ya existe
            $checkSuscripcion = $this->db->prepare("SELECT id FROM suscripciones WHERE empresa_id = ? AND estado IN ('Activa', 'Pendiente')");
            $checkSuscripcion->bind_param("i", $this->empresa_id);
            $checkSuscripcion->execute();
            $suscripcionResult = $checkSuscripcion->get_result();
            
            $estado = "Activa";
            $fecha_inicio = date('Y-m-d');
            $fecha_fin = date('Y-m-d', strtotime('+1 year'));
            $fecha_siguiente_factura = date('Y-m-d', strtotime('+1 month'));
            $periodo_facturacion = "Mensual";
            $precio_total = 29900; // precio_mensual
            $moneda = "CLP";
            
            if ($suscripcionResult->num_rows > 0) {
                // Actualizar suscripción existente
                $suscripcion = $suscripcionResult->fetch_object();
                $this->suscripcion_id = $suscripcion->id;
                
                $sql = "UPDATE suscripciones SET 
                        plan_id = ?,
                        estado = ?,
                        fecha_inicio = ?,
                        fecha_fin = ?,
                        fecha_siguiente_factura = ?,
                        periodo_facturacion = ?,
                        precio_total = ?,
                        moneda = ?,
                        fecha_actualizacion = ?
                        WHERE id = ?";
                
                $updateSuscripcion = $this->db->prepare($sql);
                
                $updateSuscripcion->bind_param("isssssdssi", 
                    $this->plan_id,
                    $estado,
                    $fecha_inicio,
                    $fecha_fin,
                    $fecha_siguiente_factura,
                    $periodo_facturacion,
                    $precio_total,
                    $moneda,
                    $this->fecha_actual,
                    $this->suscripcion_id
                );
                
                if ($updateSuscripcion->execute()) {
                    logAction("UPDATE_SUSCRIPCION", "Suscripción actualizada para la empresa {$this->empresa_id}");
                    $updateSuscripcion->close();
                    return "Suscripción actualizada para la empresa";
                } else {
                    throw new Exception("Error al actualizar la suscripción: " . $updateSuscripcion->error);
                }
            } else {
                // Crear nueva suscripción
                $numero_suscripcion = "SUB" . date('Ymd') . rand(1000, 9999);
                
                $sql = "INSERT INTO suscripciones (
                        empresa_id, 
                        plan_id, 
                        numero_suscripcion, 
                        estado, 
                        fecha_inicio, 
                        fecha_fin, 
                        fecha_siguiente_factura, 
                        periodo_facturacion, 
                        precio_total, 
                        moneda, 
                        fecha_creacion
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $insertSuscripcion = $this->db->prepare($sql);
                
                $insertSuscripcion->bind_param("iissssssdss", 
                    $this->empresa_id,
                    $this->plan_id,
                    $numero_suscripcion,
                    $estado,
                    $fecha_inicio,
                    $fecha_fin,
                    $fecha_siguiente_factura,
                    $periodo_facturacion,
                    $precio_total,
                    $moneda,
                    $this->fecha_actual
                );
                
                if ($insertSuscripcion->execute()) {
                    $this->suscripcion_id = $insertSuscripcion->insert_id;
                    logAction("CREATE_SUSCRIPCION", "Suscripción creada para la empresa {$this->empresa_id}");
                    $insertSuscripcion->close();
                    return "Suscripción creada para la empresa";
                } else {
                    throw new Exception("Error al crear la suscripción: " . $insertSuscripcion->error);
                }
            }
            
            $checkSuscripcion->close();
        } catch (Exception $e) {
            logAction("ERROR", "Error en crearSuscripcion: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Ejecuta toda la configuración inicial
     */
    public function ejecutar() {
        $resultados = [];
        
        try {
            // 1. Crear administrador del sistema
            $resultados['admin'] = $this->crearSuperAdmin();
            
            // 2. Crear usuarios
            $resultados['usuarios'] = $this->crearUsuarios();
            
            // 3. Crear empresa
            $resultados['empresa'] = $this->crearEmpresa();
            
            // 4. Crear plan
            $resultados['plan'] = $this->crearPlan();
            
            // 5. Crear suscripción
            $resultados['suscripcion'] = $this->crearSuscripcion();
            
            // 6. Cerrar conexión a la base de datos
            $this->db->close();
            
            return $resultados;
        } catch (Exception $e) {
            if ($this->db) {
                $this->db->close();
            }
            throw $e;
        }
    }
    
    /**
     * Genera un resumen HTML de la configuración
     */
    public function generarResumen() {
        $html = "";
        $html .= "<h4>Configuración completada</h4>";
        
        // Super Admin
        $html .= "<p><strong>Super Admin:</strong> admin@admin.cl (Contraseña: " . ADMIN_PASSWORD . ")</p>";
        
        // Usuarios
        $html .= "<p><strong>Usuarios de la empresa:</strong></p>";
        $html .= "<ul>";
        $html .= "<li><strong>ADMIN:</strong> admin@demo.cl (Contraseña: " . USER_PASSWORD . ")</li>";
        $html .= "<li><strong>VENDEDOR:</strong> vendedor@demo.cl (Contraseña: " . USER_PASSWORD . ")</li>";
        $html .= "<li><strong>TOUR_MANAGER:</strong> tour@demo.cl (Contraseña: " . USER_PASSWORD . ")</li>";
        $html .= "</ul>";
        
        // Empresa
        $html .= "<p><strong>Empresa:</strong> Demo Producciones</p>";
        
        // Plan y suscripción
        $html .= "<p><strong>Plan:</strong> Eco (Básico)</p>";
        $html .= "<p><strong>Suscripción:</strong> Activa hasta " . date('d/m/Y', strtotime('+1 year')) . "</p>";
        
        // Aviso
        $html .= "<p><em>Todos los usuarios están asociados a la misma empresa con plan activo.</em></p>";
        
        return $html;
    }
}

// Ejecutar la configuración
$configuracion = new CubicSetup();

try {
    // Iniciar encabezado HTML con estilo mejorado
    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>CUBIC SETUP - Configuración inicial</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }';
    echo 'h1 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }';
    echo 'h3 { margin-top: 0; }';
    echo '.success-box { background: #e9ffe9; border: 1px solid #3c3; padding: 15px; margin: 20px 0; border-radius: 5px; }';
    echo '.error-box { background: #ffe9e9; border: 1px solid #c33; padding: 15px; margin: 20px 0; border-radius: 5px; }';
    echo '.warning-box { background: #fff9e9; border: 1px solid #fc3; padding: 15px; margin: 20px 0; border-radius: 5px; }';
    echo '.info-box { background: #e9f5ff; border: 1px solid #3498db; padding: 15px; margin: 20px 0; border-radius: 5px; }';
    echo 'ul { margin: 10px 0; padding-left: 20px; }';
    echo 'li { margin-bottom: 5px; }';
    echo '.footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #eee; font-size: 0.8em; color: #777; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<h1>🔧 CUBIC SETUP - Configuración inicial del sistema</h1>';

    // Ejecutar la configuración
    $resultados = $configuracion->ejecutar();
    
    // Mostrar resumen de configuración
    echo '<div class="success-box">';
    echo $configuracion->generarResumen();
    echo '</div>';
    
    // Mostrar advertencia de seguridad
    echo '<div class="warning-box">';
    echo '<h3>⚠️ Importante</h3>';
    echo '<p>Por seguridad, este script debería ser <strong>eliminado</strong> después de su uso.</p>';
    echo '<p>La información mostrada es confidencial. Guárdela en un lugar seguro antes de eliminar este archivo.</p>';
    echo '</div>';
    
    // Mostrar información de acceso
    echo '<div class="info-box">';
    echo '<h3>ℹ️ Acceso al sistema</h3>';
    echo '<p>Para acceder al sistema utilice las siguientes URLs:</p>';
    echo '<ul>';
    echo '<li><strong>Panel de Super Admin:</strong> ' . base_url . 'admin/login</li>';
    echo '<li><strong>Panel de Usuarios:</strong> ' . base_url . 'user/login</li>';
    echo '</ul>';
    echo '</div>';
    
    // Footer
    echo '<div class="footer">';
    echo '<p>CUBIC CLOUD - Sistema de gestión para empresas de eventos y representación artística</p>';
    echo '<p>Archivo de configuración generado el: ' . date('d/m/Y H:i:s') . '</p>';
    echo '</div>';
    
    echo '</body>';
    echo '</html>';
    
} catch (Exception $e) {
    mostrarError("Error en la configuración", $e->getMessage());
    
    // Advertencia de seguridad incluso en caso de error
    mostrarAlerta("Importante", "Por seguridad, este script debería ser eliminado después de su uso.");
}
?>