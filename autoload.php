<?php
/**
 * Sistema de autoload para cargar controladores y modelos automáticamente
 * 
 * El helper de autenticación se cargará desde index.php para evitar conflictos de redeclaración
 */

/**
 * Función para carga automática de clases
 * 
 * @param string $classname Nombre de la clase a cargar
 */
function controllers_autoload($classname) {
    // Rutas posibles para cargar clases
    $paths = [
        'controllers/' . $classname . '.php',
        'models/' . $classname . '.php',
        'helpers/' . $classname . '.php',
        'core/' . $classname . '.php'  // Añadir esta línea
    ];
    
    // Buscar la clase en las rutas disponibles
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

// Registrar la función como autoloader
spl_autoload_register('controllers_autoload');