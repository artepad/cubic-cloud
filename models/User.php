<?php
require_once 'config/db.php';

/**
 * Clase User
 * 
 * Modelo para la gestión de usuarios regulares del sistema,
 * enfocado en autenticación y operaciones básicas.
 */
class User
{
    private $id;
    private $nombre;
    private $apellido;
    private $email;
    private $password;
    private $empresa_id;
    private $estado;
    private $db;

    /**
     * Constructor de la clase
     * Inicializa la conexión a la base de datos
     */
    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Getters y setters básicos
     */
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $this->db->real_escape_string($nombre);
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function setApellido($apellido) {
        $this->apellido = $this->db->real_escape_string($apellido);
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $this->db->real_escape_string($email);
    }

    public function getEmpresaId() {
        return $this->empresa_id;
    }

    public function setEmpresaId($empresa_id) {
        $this->empresa_id = (int)$empresa_id;
    }

    /**
     * Autenticación de usuario
     * 
     * Verifica las credenciales del usuario y actualiza información de login
     * 
     * @param string $email Email del usuario
     * @param string $password Contraseña en texto plano
     * @return object|false Objeto con datos del usuario o false si falla la autenticación
     */
    public function login($email, $password)
    {
        try {
            // Consulta usando preparación para mayor seguridad
            $sql = "SELECT * FROM usuarios WHERE email = ? AND estado = 'Activo'";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows == 1) {
                $usuario = $result->fetch_object();

                // Verificar la contraseña usando el algoritmo seguro
                $verify = password_verify($password, $usuario->password);

                if ($verify) {
                    // Actualizar último login y dirección IP con consulta preparada
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $update_sql = "UPDATE usuarios SET 
                                    ultimo_login = NOW(), 
                                    ip_ultimo_acceso = ?, 
                                    intentos_fallidos = 0 
                                    WHERE id = ?";
                    
                    $update_stmt = $this->db->prepare($update_sql);
                    $update_stmt->bind_param("si", $ip, $usuario->id);
                    $update_stmt->execute();
                    $update_stmt->close();

                    return $usuario;
                }
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $id ID del usuario a buscar
     * @return object|false Objeto con datos del usuario o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero
            
            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows == 1) {
                $usuario = $result->fetch_object();
                $stmt->close();
                return $usuario;
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }
}