<?php

/**
 * Funciones auxiliares para autenticación y autorización
 * 
 * Este archivo contiene funciones para verificar si los administradores o usuarios están autenticados
 * y determinar qué rutas son públicas o protegidas.
 */

/**
 * Verifica si el administrador está logueado
 * 
 * @return bool True si el administrador está logueado
 */
function isAdminLoggedIn()
{
    return isset($_SESSION['admin']);
}

/**
 * Verifica si el usuario regular está logueado
 * 
 * @return bool True si el usuario está logueado
 */
function isUserLoggedIn()
{
    return isset($_SESSION['user']);
}

/**
 * Verifica si una ruta es pública (no requiere autenticación de administrador)
 * 
 * @param string $controller Nombre del controlador
 * @param string $action Nombre de la acción
 * @return bool True si la ruta es pública
 */
function isPublicRoute($controller, $action)
{
    $rutas_publicas = [
        'admin' => ['login', 'validate', 'recover', 'requestReset', 'reset', 'doReset'],
        'error' => ['index'],
        // Otras rutas públicas aquí
    ];

    return (isset($rutas_publicas[$controller]) && in_array($action, $rutas_publicas[$controller]));
}

/**
 * Verifica si una ruta es pública para usuarios regulares
 * 
 * @param string $controller Nombre del controlador
 * @param string $action Nombre de la acción
 * @return bool True si la ruta es pública para usuarios
 */
function isUserPublicRoute($controller, $action)
{
    $rutas_publicas_usuario = [
        'user' => ['login', 'validate', 'recover', 'requestReset', 'reset', 'doReset'],
        'error' => ['index'],
        // Otras rutas públicas para usuarios aquí
    ];

    return (isset($rutas_publicas_usuario[$controller]) && in_array($action, $rutas_publicas_usuario[$controller]));
}

/**
 * Función para verificar la fortaleza de una contraseña
 * 
 * @param string $password Contraseña a validar
 * @return bool True si cumple los requisitos mínimos
 */
function validatePasswordStrength($password)
{
    // Mínimo 8 caracteres
    if (strlen($password) < 8) {
        return false;
    }

    // Debe contener al menos una letra mayúscula, una minúscula y un número
    return (
        preg_match('@[A-Z]@', $password) &&
        preg_match('@[a-z]@', $password) &&
        preg_match('@[0-9]@', $password)
    );
}

/**
 * Función para redireccionar de forma segura
 * 
 * @param string $path Ruta relativa a la URL base
 */
function redirectTo($path)
{
    // Prevenir bucles de redirección verificando si ya estamos en esa ruta
    $current_path = isset($_GET['controller']) ? $_GET['controller'] : '';
    $current_action = isset($_GET['action']) ? $_GET['action'] : '';

    // Si ya estamos en la ruta a la que queremos redirigir, no hacer nada
    if ($path == "$current_path/$current_action") {
        // Evitar la redirección y mostrar error
        error_log("Intento de redirección a la misma página: $path");
        return;
    }

    header("Location: " . base_url . $path);
    exit();
}

/**
 * Regenera el ID de sesión para prevenir ataques de session fixation
 */
function regenerateSession()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Verifica si un token CSRF es válido
 * 
 * @param string $token Token enviado en el formulario
 * @return bool True si el token es válido
 */
function validateCsrfToken($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        error_log('Error CSRF: No hay token en sesión');
        return false;
    }

    if ($_SESSION['csrf_token'] !== $token) {
        error_log('Error CSRF: Token no coincide. En sesión: ' . $_SESSION['csrf_token'] . ' - Recibido: ' . $token);
        return false;
    }

    return true;
}

/**
 * Genera un nuevo token CSRF y lo almacena en la sesión
 * 
 * @return string Token CSRF generado
 */
function generateCsrfToken() {
    // Solo generar un nuevo token si no hay uno existente
    if (!isset($_SESSION['csrf_token'])) {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica si una cookie "Recuérdame" es válida y establece la sesión para administrador
 * 
 * @param SystemAdmin $adminModel Modelo de administrador
 * @return bool True si la cookie es válida y se estableció la sesión
 */
function checkRememberCookie($adminModel)
{
    if (isset($_COOKIE['admin_remember'])) {
        $token = $_COOKIE['admin_remember'];
        $admin = $adminModel->validateRememberToken($token);

        if ($admin) {
            $_SESSION['admin'] = $admin;
            regenerateSession();
            return true;
        } else {
            // Eliminar cookie inválida
            setcookie('admin_remember', '', [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }
    return false;
}

/**
 * Verifica si un middleware de autenticación está habilitado para una ruta
 * 
 * @param string $middleware Tipo de middleware a verificar
 * @return bool True si la autenticación es requerida
 */
function checkMiddleware($middleware)
{
    switch ($middleware) {
        case 'auth':
            return isAdminLoggedIn();
        case 'user_auth':
            return isUserLoggedIn();
        case 'guest':
            return true;
        case 'public':
            return true;
        default:
            return false;
    }
}