<?php
require_once 'autoload.php';
require_once 'config/parameters.php';
require_once 'config/db.php';

// Este script crea un usuario regular con medidas de seguridad mejoradas
// Solo debe ejecutarse en entornos controlados y eliminarse después

// Función para registrar intentos de creación
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
    
    // Datos del usuario
    $nombre = "Usuario";
    $apellido = "Demo";
    $email = "usuario@demo.cl";
    $password = "8319";
    $telefono = "+56987654321";
    $pais = "Chile";
    $codigo_pais = "CL";
    $numero_identificacion = "12345678-9";
    $tipo_identificacion = "RUT";
    $tipo_usuario = "ADMIN";
    $estado = "Activo";
    $fecha_actual = date('Y-m-d H:i:s');
    
    // Encriptación segura
    $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Uso de prepared statements para prevenir inyección SQL
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        // Actualizar usuario existente usando prepared statements
        $usuario = $result->fetch_object();
        
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
            $nombre, 
            $apellido, 
            $password_hash, 
            $telefono, 
            $pais, 
            $codigo_pais, 
            $numero_identificacion, 
            $tipo_identificacion, 
            $tipo_usuario, 
            $estado, 
            $fecha_actual, 
            $usuario->id
        );
        
        if ($updateStmt->execute()) {
            echo "<div style='background:#e9ffe9;border:1px solid #3c3;padding:15px;margin:20px;border-radius:5px;'>";
            echo "<h3>Usuario actualizado correctamente</h3>";
            echo "<p><strong>Email:</strong> {$email}</p>";
            echo "<p><strong>Password:</strong> {$password}</p>";
            echo "<p>(Guarda esta información en un lugar seguro)</p>";
            echo "</div>";
            
            logAction("UPDATE_USER", "Usuario actualizado: $email");
        } else {
            throw new Exception("Error al actualizar el usuario: " . $updateStmt->error);
        }
        
        $updateStmt->close();
    } else {
        // Crear nuevo usuario con prepared statements
        $insertStmt = $db->prepare("INSERT INTO usuarios 
                                  (nombre, apellido, email, password, telefono, pais, codigo_pais, 
                                   numero_identificacion, tipo_identificacion, tipo_usuario, estado, fecha_creacion) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $insertStmt->bind_param("ssssssssssss", 
            $nombre, 
            $apellido, 
            $email, 
            $password_hash, 
            $telefono, 
            $pais, 
            $codigo_pais, 
            $numero_identificacion, 
            $tipo_identificacion, 
            $tipo_usuario, 
            $estado, 
            $fecha_actual
        );
        
        if ($insertStmt->execute()) {
            echo "<div style='background:#e9ffe9;border:1px solid #3c3;padding:15px;margin:20px;border-radius:5px;'>";
            echo "<h3>Usuario creado correctamente</h3>";
            echo "<p><strong>Email:</strong> {$email}</p>";
            echo "<p><strong>Password:</strong> {$password}</p>";
            echo "<p>(Guarda esta información en un lugar seguro y elimina este script)</p>";
            echo "</div>";
            
            logAction("CREATE_USER", "Usuario creado: $email");
        } else {
            throw new Exception("Error al crear el usuario: " . $insertStmt->error);
        }
        
        $insertStmt->close();
    }
    
    // Cerrar recursos
    $stmt->close();
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