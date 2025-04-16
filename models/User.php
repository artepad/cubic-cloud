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

    // ========== GETTERS Y SETTERS ==========

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
    
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getEmpresaId() {
        return $this->empresa_id;
    }

    public function setEmpresaId($empresa_id) {
        $this->empresa_id = (int)$empresa_id;
    }
    
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $this->db->real_escape_string($estado);
    }

    // ========== MÉTODOS DE AUTENTICACIÓN ==========

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
                } else {
                    // Incrementar contador de intentos fallidos
                    $this->incrementFailedAttempts($email);
                }
            } else {
                // Incrementar contador de intentos fallidos si el email existe
                $this->incrementFailedAttempts($email);
            }
            
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Registra el cierre de sesión de un usuario
     * 
     * @param int $user_id ID del usuario
     * @return bool Resultado de la operación
     */
    public function registerLogout($user_id)
    {
        try {
            // Sanitizar la entrada
            $user_id = (int)$user_id;

            // Obtener la IP del cliente
            $ip = $_SERVER['REMOTE_ADDR'];

            // Actualizar el registro del usuario
            $sql = "UPDATE usuarios SET 
                ultimo_login = NOW(), 
                ip_ultimo_acceso = ? 
                WHERE id = ?";

            // Usar consultas preparadas para mayor seguridad
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $ip, $user_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en registerLogout: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Incrementa el contador de intentos fallidos de inicio de sesión
     * Bloquea la cuenta después de 5 intentos fallidos
     * 
     * @param string $email Email del usuario
     */
    private function incrementFailedAttempts($email)
    {
        try {
            // Verificar si el email existe antes de incrementar (consulta preparada)
            $check_sql = "SELECT id, intentos_fallidos FROM usuarios WHERE email = ?";
            $check_stmt = $this->db->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result && $check_result->num_rows > 0) {
                $usuario = $check_result->fetch_object();
                $intentos = $usuario->intentos_fallidos + 1;
                $user_id = $usuario->id;
                
                // Incrementar contador con consulta preparada
                $update_sql = "UPDATE usuarios SET intentos_fallidos = ? WHERE id = ?";
                $update_stmt = $this->db->prepare($update_sql);
                $update_stmt->bind_param("ii", $intentos, $user_id);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Bloquear la cuenta después de 5 intentos fallidos
                if ($intentos >= 5) {
                    $block_sql = "UPDATE usuarios SET estado = 'Inactivo' WHERE id = ?";
                    $block_stmt = $this->db->prepare($block_sql);
                    $block_stmt->bind_param("i", $user_id);
                    $block_stmt->execute();
                    $block_stmt->close();
                }
            }
            
            $check_stmt->close();
        } catch (Exception $e) {
            error_log("Error al incrementar intentos fallidos: " . $e->getMessage());
        }
    }

    // ========== MÉTODOS DE GESTIÓN DE TOKENS ==========

    /**
     * Valida un token de "Recuérdame" para inicio de sesión automático
     * 
     * @param string $token Token a verificar
     * @return object|false Objeto del usuario o false si es inválido
     */
    public function validateRememberToken($token)
    {
        try {
            // Buscar el token válido en la base de datos
            $sql = "SELECT * FROM usuarios 
                WHERE remember_token = ? 
                AND remember_token_expires > NOW() 
                AND estado = 'Activo'";
                
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $usuario = $result->fetch_object();

                // Actualizar el último login
                $ip = $_SERVER['REMOTE_ADDR'];
                $update_sql = "UPDATE usuarios SET 
                        ultimo_login = NOW(), 
                        ip_ultimo_acceso = ? 
                        WHERE id = ?";
                        
                $update_stmt = $this->db->prepare($update_sql);
                $update_stmt->bind_param("si", $ip, $usuario->id);
                $update_stmt->execute();
                $update_stmt->close();

                $stmt->close();
                return $usuario;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en validateRememberToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera y guarda un token para la funcionalidad "Recuérdame"
     * 
     * @param int $user_id ID del usuario
     * @param int $days Número de días que durará la cookie (opcional)
     * @return string|false El token generado o false en caso de error
     */
    public function createRememberToken($user_id, $days = null)
    {
        // Usar el valor predeterminado si no se especifica
        if ($days === null) {
            $days = COOKIE_LIFETIME;
        }

        try {
            // Sanear la entrada
            $user_id = (int)$user_id;

            // Generar token aleatorio
            $token = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales

            // Calcular fecha de expiración
            $expires = date('Y-m-d H:i:s', strtotime("+{$days} days"));

            // Guardar en la base de datos usando consultas preparadas
            $sql = "UPDATE usuarios SET 
            remember_token = ?, 
            remember_token_expires = ? 
            WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssi", $token, $expires, $user_id);

            if ($stmt->execute()) {
                $stmt->close();
                return $token;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en createRememberToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina el token de "Recuérdame" de un usuario
     * 
     * @param int $user_id ID del usuario
     * @return bool Resultado de la operación
     */
    public function clearRememberToken($user_id)
    {
        try {
            // Sanear la entrada
            $user_id = (int)$user_id;

            // Eliminar token con consulta preparada
            $sql = "UPDATE usuarios SET 
                remember_token = NULL, 
                remember_token_expires = NULL 
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en clearRememberToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genera un token para recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @return string|false Token generado o false en caso de error
     */
    public function generateRecoveryToken($email)
    {
        try {
            // Verificar que el email existe y el usuario está activo
            $sql = "SELECT id FROM usuarios WHERE email = ? AND estado = 'Activo'";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $usuario = $result->fetch_object();

                // Generar token seguro
                $token = bin2hex(random_bytes(32));

                // Calcular fecha de expiración (1 hora)
                $expires = date('Y-m-d H:i:s', strtotime("+1 hour"));

                // Guardar token con consulta preparada
                $update_sql = "UPDATE usuarios SET 
                    token_recuperacion = ?, 
                    token_expiracion = ? 
                    WHERE id = ?";

                $update_stmt = $this->db->prepare($update_sql);
                $update_stmt->bind_param("ssi", $token, $expires, $usuario->id);

                if ($update_stmt->execute()) {
                    $update_stmt->close();
                    $stmt->close();
                    return $token;
                }
                
                $update_stmt->close();
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en generateRecoveryToken: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Valida un token de recuperación de contraseña
     * 
     * @param string $token Token a validar
     * @return object|false Objeto del usuario o false si es inválido
     */
    public function validateRecoveryToken($token)
    {
        try {
            // Buscar el token válido en la base de datos
            $sql = "SELECT * FROM usuarios 
                WHERE token_recuperacion = ? 
                AND token_expiracion > NOW() 
                AND estado = 'Activo'";
                
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $token);
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
            error_log("Error en validateRecoveryToken: " . $e->getMessage());
            return false;
        }
    }

    // ========== MÉTODOS DE GESTIÓN DE CONTRASEÑAS ==========

    /**
     * Actualiza la contraseña de un usuario
     * 
     * @param int $user_id ID del usuario
     * @param string $new_password Nueva contraseña en texto plano
     * @return bool Resultado de la operación
     */
    public function updatePassword($user_id, $new_password)
    {
        try {
            // Sanear la entrada
            $user_id = (int)$user_id;

            // Encriptar la nueva contraseña con un costo adecuado
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Actualizar contraseña y limpiar tokens con consulta preparada
            $sql = "UPDATE usuarios SET 
                password = ?, 
                token_recuperacion = NULL, 
                token_expiracion = NULL 
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("si", $hashed_password, $user_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en updatePassword: " . $e->getMessage());
            return false;
        }
    }

    // ========== MÉTODOS DE CONSULTA ==========

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

    /**
     * Envía un correo con el enlace de recuperación de contraseña
     * 
     * @param string $email Email del usuario
     * @param string $token Token de recuperación
     * @return bool Resultado del envío
     */
    public function sendRecoveryEmail($email, $token)
    {
        try {
            // Sanitizar email
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Error en sendRecoveryEmail: formato de email inválido");
                return false;
            }

            // Construir el enlace de recuperación
            $recovery_link = base_url . "user/reset?token=" . urlencode($token);

            // Construir el mensaje
            $subject = EMAIL_SUBJECT_PREFIX . "Recuperación de contraseña";
            $message = "Hola,\n\n";
            $message .= "Has solicitado restablecer tu contraseña en Cubic Cloud.\n\n";
            $message .= "Haz clic en el siguiente enlace para crear una nueva contraseña:\n";
            $message .= $recovery_link . "\n\n";
            $message .= "Este enlace expirará en 1 hora.\n\n";
            $message .= "Si no solicitaste este cambio, puedes ignorar este correo.\n\n";
            $message .= "Saludos,\nEquipo de Cubic Cloud";

            // Cabeceras para el correo
            $headers = "From: " . EMAIL_FROM . "\r\n";
            $headers .= "Reply-To: " . EMAIL_FROM . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            // Enviar el correo
            $sent = mail($email, $subject, $message, $headers);

            // Registrar el resultado
            if (!$sent) {
                error_log("Error al enviar correo de recuperación a $email");
            }

            return $sent;
        } catch (Exception $e) {
            error_log("Error en sendRecoveryEmail: " . $e->getMessage());
            return false;
        }
    }
}