<?php
/**
 * Clase Router
 * 
 * Gestiona el enrutamiento de la aplicación, asociando URLs con controladores y acciones
 */
class Router
{
    /**
     * Almacena todas las rutas registradas
     * [ruta => [controlador, acción, middleware]]
     */
    private static $routes = [];
    
    /**
     * Almacena las rutas que usan parámetros
     * [patrón => [ruta, params]]
     */
    private static $paramRoutes = [];
    
    /**
     * Ruta por defecto cuando ninguna otra coincide
     */
    private static $defaultRoute = null;
    
    /**
     * Registra una nueva ruta
     * 
     * @param string $route Ruta a registrar (ej: 'productos/ver')
     * @param string $controller Controlador que maneja la ruta
     * @param string $action Acción del controlador
     * @param string|null $middleware Middleware para verificar antes de ejecutar (opcional)
     * @return void
     */
    public static function add($route, $controller, $action, $middleware = null)
    {
        // Asegurarse que la ruta comienza sin '/'
        $route = ltrim($route, '/');
        
        // Verificar si la ruta contiene parámetros (indicados con :)
        if (strpos($route, ':') !== false) {
            // Crear un patrón para hacer coincidir la ruta con parámetros
            $pattern = preg_replace('/:([^\/]+)/', '([^/]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            $pattern = '/^' . $pattern . '$/';
            
            // Extraer nombres de parámetros
            preg_match_all('/:([^\/]+)/', $route, $paramNames);
            
            // Guardar la ruta con parámetros
            self::$paramRoutes[$pattern] = [
                'route' => $route,
                'controller' => $controller,
                'action' => $action,
                'middleware' => $middleware,
                'params' => $paramNames[1]
            ];
        } else {
            // Guardar ruta normal
            self::$routes[$route] = [
                'controller' => $controller,
                'action' => $action,
                'middleware' => $middleware
            ];
        }
    }
    
    /**
     * Establece la ruta por defecto
     * 
     * @param string $controller Controlador por defecto
     * @param string $action Acción por defecto
     * @return void
     */
    public static function setDefault($controller, $action)
    {
        self::$defaultRoute = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => null
        ];
    }
    
    /**
     * Resuelve la ruta actual y devuelve el controlador y acción
     * 
     * @param string $uri URI a resolver
     * @return array [controlador, acción, parámetros]
     */
    public static function resolve($uri)
    {
        // Eliminar la base URL y query string
        $uri = self::getCleanUri($uri);
        
        // Comprobar si existe una ruta directa
        if (isset(self::$routes[$uri])) {
            $route = self::$routes[$uri];
            
            // Verificar middleware si existe
            if ($route['middleware'] && !self::checkMiddleware($route['middleware'], $route['controller'], $route['action'])) {
                // Redirección o error según la configuración
                header("Location: " . base_url . "admin/login");
                exit();
            }
            
            return [
                'controller' => $route['controller'],
                'action' => $route['action'],
                'params' => []
            ];
        }
        
        // Comprobar rutas con parámetros
        foreach (self::$paramRoutes as $pattern => $routeData) {
            if (preg_match($pattern, $uri, $matches)) {
                // Eliminar la coincidencia completa
                array_shift($matches);
                
                // Verificar middleware si existe
                if ($routeData['middleware'] && !self::checkMiddleware($routeData['middleware'], $routeData['controller'], $routeData['action'])) {
                    // Redirección o error según la configuración
                    header("Location: " . base_url . "admin/login");
                    exit();
                }
                
                // Crear array asociativo de parámetros
                $params = [];
                foreach ($routeData['params'] as $index => $paramName) {
                    if (isset($matches[$index])) {
                        $params[$paramName] = $matches[$index];
                    }
                }
                
                return [
                    'controller' => $routeData['controller'],
                    'action' => $routeData['action'],
                    'params' => $params
                ];
            }
        }
        
        // Si no hay coincidencias, usar ruta por defecto
        if (self::$defaultRoute) {
            return [
                'controller' => self::$defaultRoute['controller'],
                'action' => self::$defaultRoute['action'],
                'params' => []
            ];
        }
        
        // Si no hay ruta por defecto, mostrar error 404
        return [
            'controller' => 'ErrorController',
            'action' => 'index',
            'params' => []
        ];
    }
    
    /**
     * Verifica si el middleware permite el acceso a la ruta
     * 
     * @param string $middleware Nombre del middleware a verificar
     * @param string $controller Controlador asociado
     * @param string $action Acción asociada
     * @return bool True si el acceso está permitido
     */
    private static function checkMiddleware($middleware, $controller, $action)
    {
        switch ($middleware) {
            case 'auth':
                return isAdminLoggedIn();
            case 'guest':
                return !isAdminLoggedIn();
            case 'public':
                return true;
            default:
                return isPublicRoute($controller, $action);
        }
    }
    
    /**
     * Limpia la URI para el procesamiento
     * 
     * @param string $uri URI a limpiar
     * @return string URI limpia
     */
    private static function getCleanUri($uri)
    {
        // Obtener solo la parte de la ruta (sin query string)
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Eliminar la base URL
        $baseDir = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        $uri = substr($uri, strlen($baseDir));
        
        // Eliminar barras al inicio y final
        $uri = trim($uri, '/');
        
        return $uri;
    }
}