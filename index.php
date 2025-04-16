<?php
/**
 * Punto de entrada principal de la aplicación
 * 
 * Este archivo maneja el enrutamiento principal, autenticación y carga de layouts
 */

// Cargar dependencias
require_once 'config/parameters.php';
require_once 'autoload.php';
require_once 'helpers/auth_helper.php';
require_once 'core/Router.php';  // Añadir esta línea

// Configurar tiempo de vida de la sesión
ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
session_set_cookie_params(SESSION_TIMEOUT);

// Iniciar sesión con configuraciones seguras
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
session_start();

// Verificar timeout de sesión si hay un admin logueado
if (isset($_SESSION['admin']) && isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        // La sesión ha expirado
        session_unset();
        session_destroy();
        $_SESSION['error_login'] = "Tu sesión ha expirado por inactividad";
        
        // Redirigir a la página de login de admin
        header("Location: " . base_url . "admin/login");
        exit();
    }
}

// Actualizar timestamp de última actividad para el admin
if (isset($_SESSION['admin'])) {
    $_SESSION['last_activity'] = time();
}

// Configurar protección XSS global
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; img-src 'self' data:;");

// Verificar cookies de "Recuérdame" si no hay sesión activa
if (!isset($_SESSION['admin'])) {
    // Verificar cookie de administrador
    if (isset($_COOKIE['admin_remember'])) {
        $adminModel = new SystemAdmin();
        checkRememberCookie($adminModel);
    }
}

// Cargar archivo de rutas
require_once 'routes.php'; // Añadir esta línea

// Resolver la ruta actual
$currentUri = $_SERVER['REQUEST_URI'];
$routeInfo = Router::resolve($currentUri);

$controller_name = $routeInfo['controller'];
$action = $routeInfo['action'];
$params = $routeInfo['params'];

// Determinar si es una vista de login o parte del sistema principal
$es_login = determinarSiEsLoginRoute($controller_name, $action);

// Cargar layouts principales solo si NO es una vista de login
if (!$es_login) {
    loadLayouts();
}

// Verificar si el controlador existe e invocar la acción solicitada
if (class_exists($controller_name)) {
    $controlador = new $controller_name();

    // Verificar si existe el método solicitado
    if (method_exists($controlador, $action)) {
        // Si hay parámetros, pasarlos a la acción
        if (!empty($params)) {
            call_user_func_array([$controlador, $action], $params);
        } else {
            // Ejecutar la acción del controlador
            $controlador->$action();
        }
    } else {
        show_error();
    }
} else {
    show_error();
}

// Cargar el footer solo si NO es login
if (!$es_login) {
    require_once 'views/layout/footer.php';
}

/**
 * Muestra la página de error 404
 */
function show_error() {
    $error = new ErrorController();
    $error->index();
}

/**
 * Determina si una ruta pertenece al proceso de login/registro
 * Renombrada para evitar conflicto con auth_helper.php
 * 
 * @param string $controller Nombre del controlador
 * @param string $action Nombre de la acción
 * @return bool True si es una ruta de login
 */
function determinarSiEsLoginRoute($controller, $action) {
    $login_routes = [
        'AdminController' => ['login', 'validate', 'recover', 'requestReset', 'reset', 'doReset', 'logout'],
        'UserController' => ['login', 'validate', 'recover', 'requestReset', 'reset', 'doReset', 'logout']
    ];
    
    return (isset($login_routes[$controller]) && in_array($action, $login_routes[$controller]));
}

/**
 * Carga los layouts principales de la aplicación
 */
function loadLayouts() {
    require_once 'views/layout/header.php';
    require_once 'views/layout/nav.php';
    require_once 'views/layout/sidebar.php';
    require_once 'views/layout/content.php';
}