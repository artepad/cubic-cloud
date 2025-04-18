<?php
require_once 'autoload.php';
require_once 'config/parameters.php';
require_once 'config/db.php';

// Este script crea usuarios de prueba con roles específicos, una empresa y su suscripción
// Solo debe ejecutarse en entornos controlados y eliminarse después

// Función para registrar acciones
function logAction($action, $details) {
    $logFile = 'logs/user_creation.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $logMessage = "[$timestamp] IP: $ip | $action | $details" . PHP_EOL;
    
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Validación básica para prevenir ejecución accidental
if (!isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] !== 'localhost') {
    die('Este script solo puede ejecutarse en entorno local');
}

try {
    // Conexión a la base de datos con manejo de errores
    $db = Database::connect();
    
    // Definir datos de los usuarios
    $usuarios = [
        [
            'nombre' => 'Admin',
            'apellido' => 'Demo',
            'email' => 'admin@demo.cl',
            'password' => '8787',
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
            'password' => '8787',
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
            'password' => '8787',
            'telefono' => '+56987654323',
            'pais' => 'Chile',
            'codigo_pais' => 'CL',
            'numero_identificacion' => '12345678-7',
            'tipo_identificacion' => 'RUT',
            'tipo_usuario' => 'TOUR_MANAGER',
            'estado' => 'Activo'
        ]
    ];
    
    $fecha_actual = date('Y-m-d H:i:s');
    $usuarios_ids = [];
    $usuario_admin_id = null;
    
    // Crear o actualizar los usuarios
    foreach ($usuarios as $usuario) {
        // Hash seguro de la contraseña
        $password_hash = password_hash($usuario['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        
        // Verificar si el usuario existe
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $usuario['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            // Actualizar usuario existente
            $user = $result->fetch_object();
            $user_id = $user->id;
            
            $updateStmt = $db->prepare("UPDATE usuarios SET 
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
                $fecha_actual, 
                $user_id
            );
            
            if ($updateStmt->execute()) {
                logAction("UPDATE_USER", "Usuario actualizado: {$usuario['email']}");
                $usuarios_ids[] = $user_id;
                
                if ($usuario['tipo_usuario'] === 'ADMIN') {
                    $usuario_admin_id = $user_id;
                }
            } else {
                throw new Exception("Error al actualizar el usuario: " . $updateStmt->error);
            }
            
            $updateStmt->close();
        } else {
            // Crear nuevo usuario
            $insertStmt = $db->prepare("INSERT INTO usuarios 
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
                $fecha_actual
            );
            
            if ($insertStmt->execute()) {
                $user_id = $insertStmt->insert_id;
                logAction("CREATE_USER", "Usuario creado: {$usuario['email']}");
                $usuarios_ids[] = $user_id;
                
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
    
    // Verificar si ya existe la empresa para estos usuarios
    $empresa_id = null;
    if ($usuario_admin_id) {
        $checkEmpresa = $db->prepare("SELECT id FROM empresas WHERE usuario_id = ?");
        $checkEmpresa->bind_param("i", $usuario_admin_id);
        $checkEmpresa->execute();
        $empresaResult = $checkEmpresa->get_result();
        
        if ($empresaResult->num_rows > 0) {
            $empresa = $empresaResult->fetch_object();
            $empresa_id = $empresa->id;
            
            // Actualizar empresa existente
            $updateEmpresa = $db->prepare("UPDATE empresas SET 
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
                                
            $nombre_empresa = "Demo Producciones";
            $rut_empresa = "76123456-7";
            $direccion = "Av. Providencia 1234, Santiago";
            $telefono = "+56222222222";
            $email = "contacto@demoproducciones.cl";
            $pais = "Chile";
            $codigo_pais = "CL";
            $estado = "activa";
            
            $updateEmpresa->bind_param("sssssssssi", 
                $nombre_empresa,
                $rut_empresa,
                $direccion,
                $telefono,
                $email,
                $pais,
                $codigo_pais,
                $estado,
                $fecha_actual,
                $empresa_id
            );
            
            if ($updateEmpresa->execute()) {
                logAction("UPDATE_EMPRESA", "Empresa actualizada: $nombre_empresa");
            } else {
                throw new Exception("Error al actualizar la empresa: " . $updateEmpresa->error);
            }
            
            $updateEmpresa->close();
        } else {
            // Crear empresa nueva
            $insertEmpresa = $db->prepare("INSERT INTO empresas 
                                (usuario_id, nombre, identificacion_fiscal, direccion, telefono, 
                                email_contacto, pais, codigo_pais, estado, fecha_creacion) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                
            $nombre_empresa = "Demo Producciones";
            $rut_empresa = "76123456-7";
            $direccion = "Av. Providencia 1234, Santiago";
            $telefono = "+56222222222";
            $email = "contacto@demoproducciones.cl";
            $pais = "Chile";
            $codigo_pais = "CL";
            $estado = "activa";
            
            $insertEmpresa->bind_param("isssssssss", 
                $usuario_admin_id,
                $nombre_empresa,
                $rut_empresa,
                $direccion,
                $telefono,
                $email,
                $pais,
                $codigo_pais,
                $estado,
                $fecha_actual
            );
            
            if ($insertEmpresa->execute()) {
                $empresa_id = $insertEmpresa->insert_id;
                logAction("CREATE_EMPRESA", "Empresa creada: $nombre_empresa");
            } else {
                throw new Exception("Error al crear la empresa: " . $insertEmpresa->error);
            }
            
            $insertEmpresa->close();
        }
        
        $checkEmpresa->close();
    } else {
        throw new Exception("No se pudo identificar un usuario administrador para crear la empresa");
    }
    
    // Crear o verificar el plan "Eco"
    $plan_id = null;
    $checkPlan = $db->prepare("SELECT id FROM planes WHERE nombre = ?");
    $nombre_plan = "Eco";
    $checkPlan->bind_param("s", $nombre_plan);
    $checkPlan->execute();
    $planResult = $checkPlan->get_result();
    
    if ($planResult->num_rows > 0) {
        $plan = $planResult->fetch_object();
        $plan_id = $plan->id;
        
        // Actualizar el plan si ya existe
        $updatePlan = $db->prepare("UPDATE planes SET 
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
            $fecha_actual,
            $plan_id
        );
        
        if ($updatePlan->execute()) {
            logAction("UPDATE_PLAN", "Plan actualizado: $nombre_plan");
        } else {
            throw new Exception("Error al actualizar el plan: " . $updatePlan->error);
        }
        
        $updatePlan->close();
    } else {
        // Crear el plan si no existe
        $insertPlan = $db->prepare("INSERT INTO planes 
                          (nombre, descripcion, tipo_plan, precio_mensual, precio_semestral, 
                          precio_anual, moneda, max_usuarios, max_artistas, max_eventos, 
                          estado, fecha_creacion) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                          
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
            $fecha_actual
        );
        
        if ($insertPlan->execute()) {
            $plan_id = $insertPlan->insert_id;
            logAction("CREATE_PLAN", "Plan creado: $nombre_plan");
        } else {
            throw new Exception("Error al crear el plan: " . $insertPlan->error);
        }
        
        $insertPlan->close();
    }
    
    $checkPlan->close();
    
    // Crear o actualizar la suscripción
    if ($empresa_id && $plan_id) {
        $checkSuscripcion = $db->prepare("SELECT id FROM suscripciones WHERE empresa_id = ? AND estado IN ('Activa', 'Pendiente')");
        $checkSuscripcion->bind_param("i", $empresa_id);
        $checkSuscripcion->execute();
        $suscripcionResult = $checkSuscripcion->get_result();
        
        if ($suscripcionResult->num_rows > 0) {
            $suscripcion = $suscripcionResult->fetch_object();
            $suscripcion_id = $suscripcion->id;
            
            // Actualizar suscripción existente
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
                                    
            $updateSuscripcion = $db->prepare($sql);
                                    
            $estado = "Activa";
            $fecha_inicio = date('Y-m-d');
            $fecha_fin = date('Y-m-d', strtotime('+1 year'));
            $fecha_siguiente_factura = date('Y-m-d', strtotime('+1 month'));
            $periodo_facturacion = "Mensual";
            $precio_total = $precio_mensual;
            
            // Verificamos que hay 10 parámetros y 10 tipos
            $updateSuscripcion->bind_param("isssssdssi", 
                $plan_id,               // 1 (i)
                $estado,                // 2 (s)
                $fecha_inicio,          // 3 (s)
                $fecha_fin,             // 4 (s)
                $fecha_siguiente_factura, // 5 (s)
                $periodo_facturacion,   // 6 (s)
                $precio_total,          // 7 (d)
                $moneda,                // 8 (s)
                $fecha_actual,          // 9 (s)
                $suscripcion_id         // 10 (i)
            );
            
            if ($updateSuscripcion->execute()) {
                logAction("UPDATE_SUSCRIPCION", "Suscripción actualizada para la empresa $empresa_id");
            } else {
                throw new Exception("Error al actualizar la suscripción: " . $updateSuscripcion->error);
            }
            
            $updateSuscripcion->close();
        } else {
            // Crear nueva suscripción
            // Creación de suscripción con tipos correctos
            // Usamos consulta directa para simplificar
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
                                    
            $insertSuscripcion = $db->prepare($sql);
            
            // Generamos datos
            $numero_suscripcion = "SUB" . date('Ymd') . rand(1000, 9999);
            $estado = "Activa";
            $fecha_inicio = date('Y-m-d');
            $fecha_fin = date('Y-m-d', strtotime('+1 year'));
            $fecha_siguiente_factura = date('Y-m-d', strtotime('+1 month'));
            $periodo_facturacion = "Mensual";
            $precio_total = $precio_mensual;
            
            // String de tipos correcto: 11 tipos para 11 parámetros
            $insertSuscripcion->bind_param("iissssssdss", 
                $empresa_id,
                $plan_id,
                $numero_suscripcion,
                $estado,
                $fecha_inicio,
                $fecha_fin,
                $fecha_siguiente_factura,
                $periodo_facturacion,
                $precio_total,
                $moneda,
                $fecha_actual
            );
            
            if ($insertSuscripcion->execute()) {
                $suscripcion_id = $insertSuscripcion->insert_id;
                logAction("CREATE_SUSCRIPCION", "Suscripción creada para la empresa $empresa_id");
            } else {
                throw new Exception("Error al crear la suscripción: " . $insertSuscripcion->error);
            }
            
            $insertSuscripcion->close();
        }
        
        $checkSuscripcion->close();
    } else {
        throw new Exception("No se pudo crear la suscripción porque falta la empresa o el plan");
    }
    
    // Mostrar resumen de las operaciones realizadas
    echo "<div style='background:#e9ffe9;border:1px solid #3c3;padding:15px;margin:20px;border-radius:5px;'>";
    echo "<h3>Configuración completada</h3>";
    echo "<p><strong>Usuarios creados:</strong></p>";
    echo "<ul>";
    foreach ($usuarios as $usuario) {
        echo "<li><strong>{$usuario['tipo_usuario']}:</strong> {$usuario['email']} (Contraseña: {$usuario['password']})</li>";
    }
    echo "</ul>";
    echo "<p><strong>Empresa:</strong> Demo Producciones</p>";
    echo "<p><strong>Plan:</strong> Eco (Básico)</p>";
    echo "<p><strong>Suscripción:</strong> Activa hasta " . date('d/m/Y', strtotime('+1 year')) . "</p>";
    echo "<p><em>Todos los usuarios están asociados a la misma empresa.</em></p>";
    echo "</div>";
    
    // Cerrar conexión a la base de datos
    $db->close();
    
} catch (Exception $e) {
    echo "<div style='background:#ffe9e9;border:1px solid #c33;padding:15px;margin:20px;border-radius:5px;'>";
    echo "<h3>Error</h3>";
    echo "<p>{$e->getMessage()}</p>";
    echo "</div>";
    
    logAction("ERROR", $e->getMessage());
}

// Recordatorio de seguridad
echo "<div style='background:#fff9e9;border:1px solid #fc3;padding:15px;margin:20px;border-radius:5px;'>";
echo "<h3>Importante</h3>";
echo "<p>Por seguridad, este script debería ser eliminado después de su uso.</p>";
echo "</div>";
?>