<?php
require_once 'config/db.php';

/**
 * Clase Usuario
 * 
 * Gestiona todas las operaciones relacionadas con los usuarios del sistema,
 * incluyendo autenticación, gestión y persistencia.
 */
class Usuario
{
    private $id;
    private $nombre;
    private $apellido;
    private $email;
    private $telefono;
    private $pais;
    private $codigo_pais;
    private $numero_identificacion;
    private $tipo_identificacion;
    private $tipo_usuario;
    private $password;
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

    // Getters y setters
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }
    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * Verifica si un email ya existe en la base de datos
     * 
     * @param string $email Email a verificar
     * @return bool True si el email ya existe
     */
    public function emailExists($email)
    {
        try {
            $sql = "SELECT id FROM usuarios WHERE email = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $exists = ($result && $result->num_rows > 0);
            $stmt->close();

            return $exists;
        } catch (Exception $e) {
            error_log("Error en emailExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guarda un nuevo usuario en la base de datos
     * 
     * @param array $datos Datos del usuario a guardar
     * @return bool Resultado de la operación
     */
    public function save($datos)
    {
        try {
            $sql = "INSERT INTO usuarios (
            nombre, apellido, email, telefono, 
            pais, codigo_pais, numero_identificacion, tipo_identificacion,
            tipo_usuario, password, estado, fecha_creacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $this->db->error);
            }

            $stmt->bind_param(
                "sssssssssss",
                $datos['nombre'],
                $datos['apellido'],
                $datos['email'],
                $datos['telefono'],
                $datos['pais'],
                $datos['codigo_pais'],
                $datos['numero_identificacion'],
                $datos['tipo_identificacion'],
                $datos['tipo_usuario'],
                $datos['password'],
                $datos['estado']
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error en save(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios del sistema
     * 
     * @return array|false Array de usuarios o false en caso de error
     */
    public function getAll()
    {
        try {
            $sql = "SELECT id, nombre, apellido, email, telefono, tipo_usuario, estado, fecha_creacion 
                   FROM usuarios ORDER BY fecha_creacion DESC";

            $result = $this->db->query($sql);

            $usuarios = [];
            if ($result && $result->num_rows > 0) {
                while ($usuario = $result->fetch_object()) {
                    $usuarios[] = $usuario;
                }
            }

            return $usuarios;
        } catch (Exception $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por su ID
     * 
     * @param int $id ID del usuario
     * @return object|false Objeto usuario o false en caso de error
     */
    public function getById($id)
    {
        try {
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

    /**
     * Elimina un usuario por su ID
     * 
     * @param int $id ID del usuario a eliminar
     * @return bool Resultado de la operación
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $id);
            $result = $stmt->execute();

            if (!$result) {
                error_log("Error al eliminar usuario: " . $stmt->error);
            }

            $stmt->close();
            return $result;
        } catch (Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }
}
