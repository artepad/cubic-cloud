<?php

/**
 * Funciones auxiliares para autenticación y autorización
 * 
 * Este archivo contiene funciones para verificar si los usuarios están autenticados,
 * comprobar roles y determinar qué rutas son públicas o protegidas.
 */

/**
 * Verifica si el usuario normal está logueado
 * 
 * @return bool True si el usuario está logueado
 */
function isLoggedIn()
{
    return isset($_SESSION['usuario']);
}

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
 * Verifica el rol del usuario para determinar si puede acceder a una sección
 * 
 * @param string $required_role Rol requerido para acceder
 * @return bool True si el usuario tiene permisos suficientes
 */
function checkUserRole($required_role)
{
    // Los administradores tienen acceso completo
    if (isset($_SESSION['admin'])) {
        return true;
    }

    // Verificar si el usuario tiene el rol requerido
    if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']->tipo_usuario)) {
        if ($_SESSION['usuario']->tipo_usuario == $required_role) {
            return true;
        }

        // Usuario sin permisos suficientes
        $_SESSION['error_login'] = "No tienes permisos para acceder a esta sección";
        redirectTo("");  // Redirección a la página principal
        return false;
    }

    // Si no hay sesión activa
    $_SESSION['error_login'] = "Debes iniciar sesión para acceder a esta sección";
    redirectTo("usuario/login");
    return false;
}

/**
 * Verifica si una ruta es pública (no requiere autenticación)
 * 
 * @param string $controller Nombre del controlador
 * @param string $action Nombre de la acción
 * @return bool True si la ruta es pública
 */
function isPublicRoute($controller, $action)
{
    $rutas_publicas = [
        'usuario' => ['login', 'registro', 'validar', 'recuperar'],
        'admin' => ['login', 'validate', 'recover', 'requestReset', 'reset', 'doReset'],
        'error' => ['index'],
        // Otras rutas públicas aquí
    ];

    return (isset($rutas_publicas[$controller]) && in_array($action, $rutas_publicas[$controller]));
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
 * Verifica si una cookie "Recuérdame" es válida y establece la sesión
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

    /**
     * Función adicional para verificar la cookie "Recuérdame" de usuarios normales
     * 
     * @param Usuario $usuarioModel Modelo de usuario
     * @return bool True si la cookie es válida y se estableció la sesión
     */
    function checkUserRememberCookie($usuarioModel)
    {
        if (isset($_COOKIE['usuario_remember'])) {
            $token = $_COOKIE['usuario_remember'];
            $usuario = $usuarioModel->validateRememberToken($token);

            if ($usuario) {
                $_SESSION['usuario'] = $usuario;
                regenerateSession();
                return true;
            } else {
                // Eliminar cookie inválida
                setcookie('usuario_remember', '', [
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
}
