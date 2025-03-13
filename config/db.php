<?php
/**
 * Clase Database
 * 
 * Gestiona la conexión a la base de datos de la aplicación
 * Implementa el patrón Singleton para evitar múltiples conexiones
 */
class Database
{
    // Propiedades para implementar Singleton
    private static $instance = null;
    private $connection;
    
    // Configuración de la base de datos
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $database = 'cubic_bd';
    private $charset = 'utf8';
    
    /**
     * Constructor privado para implementar Singleton
     * Establece la conexión a la base de datos
     */
    private function __construct()
    {
        try {
            // Crear la conexión
            $this->connection = new mysqli(
                $this->host,
                $this->user,
                $this->password,
                $this->database
            );
            
            // Configurar charset
            $this->connection->set_charset($this->charset);
            
            // Verificar conexión
            if ($this->connection->connect_error) {
                throw new Exception('Error de conexión a la base de datos: ' . $this->connection->connect_error);
            }
        } catch (Exception $e) {
            // Registrar error y finalizar
            error_log('Error en la conexión a la base de datos: ' . $e->getMessage());
            die('Error: No se pudo conectar a la base de datos. Por favor, contacte al administrador.');
        }
    }
    
    /**
     * Evita que el objeto pueda ser clonado
     */
    private function __clone() {}
    
    /**
     * Método público para obtener la instancia de la conexión (Singleton)
     * 
     * @return mysqli Conexión a la base de datos
     */
    public static function connect()
    {
        // Si no existe una instancia, crearla
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance->connection;
    }
    
    /**
     * Cierra la conexión a la base de datos
     */
    public static function disconnect()
    {
        if (self::$instance !== null) {
            self::$instance->connection->close();
            self::$instance = null;
        }
    }
    
    /**
     * Escapa una cadena para prevenir inyección SQL
     * 
     * @param string $string Cadena a escapar
     * @return string Cadena escapada
     */
    public static function escape($string)
    {
        $db = self::connect();
        return $db->real_escape_string($string);
    }
    
    /**
     * Ejecuta una consulta preparada con los parámetros proporcionados
     * 
     * @param string $sql Consulta SQL con marcadores de posición
     * @param string $types Tipos de datos de los parámetros (s: string, i: integer, d: double, b: blob)
     * @param array $params Parámetros para la consulta preparada
     * @return mysqli_stmt|false Objeto de sentencia o false en caso de error
     */
    public static function prepareAndExecute($sql, $types = '', $params = [])
    {
        $db = self::connect();
        
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            error_log('Error en preparación de consulta: ' . $db->error);
            return false;
        }
        
        // Si hay parámetros, vincularlos
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            error_log('Error en ejecución de consulta: ' . $stmt->error);
            $stmt->close();
            return false;
        }
        
        return $stmt;
    }
}