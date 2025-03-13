<?php
require_once 'autoload.php';
require_once 'config/parameters.php';

// Este script crea un administrador de prueba para facilitar el desarrollo

// Conexión directa a la base de datos para este script
require_once 'config/db.php';
$db = Database::connect();

// Datos para el administrador de prueba
$nombre = "Admin";
$apellido = "Test";
$email = "admin@cubic.com";
$password = "8787"; // Esta será la contraseña para acceder
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Verificar si ya existe
$sql = "SELECT id FROM system_admins WHERE email = '{$email}'";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    // Actualizar contraseña si el admin ya existe
    $admin = $result->fetch_object();
    $sql = "UPDATE system_admins SET 
            password = '{$password_hash}',
            estado = 'Activo',
            intentos_fallidos = 0
            WHERE id = {$admin->id}";
    
    if ($db->query($sql)) {
        echo "Administrador actualizado correctamente.<br>";
        echo "Email: {$email}<br>";
        echo "Password: {$password}<br>";
    } else {
        echo "Error al actualizar el administrador: " . $db->error;
    }
} else {
    // Crear nuevo administrador
    $sql = "INSERT INTO system_admins (nombre, apellido, email, password, estado) 
           VALUES ('{$nombre}', '{$apellido}', '{$email}', '{$password_hash}', 'Activo')";
    
    if ($db->query($sql)) {
        echo "Administrador creado correctamente.<br>";
        echo "Email: {$email}<br>";
        echo "Password: {$password}<br>";
    } else {
        echo "Error al crear el administrador: " . $db->error;
    }
}

// Cerrar conexión
$db->close();