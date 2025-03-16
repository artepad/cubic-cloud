<?php
// Archivo para diagnosticar problemas con password_hash/password_verify
echo "<h1>Prueba de hashing de contraseñas</h1>";

// Verificar que las extensiones necesarias estén habilitadas
echo "<h2>Extensiones PHP:</h2>";
echo "<p>Extensión Crypt: " . (extension_loaded('crypt') ? 'DISPONIBLE' : 'NO DISPONIBLE') . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Probar hashing y verificación
$password = "Test123456";
echo "<h2>Prueba con contraseña: $password</h2>";

// Generar hash
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
echo "<p>Hash generado: $hash</p>";

// Verificar hash
$verify = password_verify($password, $hash);
echo "<p>password_verify dice: " . ($verify ? 'CORRECTO' : 'INCORRECTO') . "</p>";

// Probar con codificaciones alternativas
echo "<h2>Pruebas con diferentes codificaciones:</h2>";

$encodings = [
    'utf8_encode' => utf8_encode($password),
    'mb_convert_encoding UTF-8' => mb_convert_encoding($password, 'UTF-8'),
    'mb_convert_encoding ISO-8859-1' => mb_convert_encoding($password, 'ISO-8859-1')
];

foreach ($encodings as $name => $encoded) {
    echo "<h3>Usando $name</h3>";
    echo "<p>Valor: $encoded</p>";
    $hash_encoded = password_hash($encoded, PASSWORD_BCRYPT, ['cost' => 12]);
    echo "<p>Hash: $hash_encoded</p>";
    $verify_encoded = password_verify($encoded, $hash_encoded);
    echo "<p>Verificación: " . ($verify_encoded ? 'CORRECTO' : 'INCORRECTO') . "</p>";
}
?>