<?php
require_once 'config/db.php';

/**
 * Clase Empresa
 * 
 * Gestiona todas las operaciones relacionadas con las empresas en el sistema,
 * incluyendo creación, actualización, eliminación y consulta.
 */
class Empresa
{
    // Propiedades de la clase
    private $id;
    private $usuario_id;
    private $nombre;
    private $identificacion_fiscal;
    private $direccion;
    private $telefono;
    private $email_contacto;
    // Datos de facturación
    private $razon_social_facturacion;
    private $direccion_facturacion;
    private $ciudad_facturacion;
    private $codigo_postal;
    private $contacto_facturacion;
    private $email_facturacion;
    // Localización
    private $pais;
    private $codigo_pais;
    // Recursos visuales
    private $imagen_empresa;
    private $imagen_documento;
    private $imagen_firma;
    // Información adicional
    private $tipo_moneda;
    private $estado;
    private $es_demo;
    private $demo_inicio;
    private $demo_fin;
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
     * Getters y setters
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $this->db->real_escape_string($nombre);
    }

    public function getIdentificacionFiscal()
    {
        return $this->identificacion_fiscal;
    }

    public function setIdentificacionFiscal($identificacion_fiscal)
    {
        $this->identificacion_fiscal = $this->db->real_escape_string($identificacion_fiscal);
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $this->db->real_escape_string($direccion);
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $this->db->real_escape_string($telefono);
    }

    public function getEmailContacto()
    {
        return $this->email_contacto;
    }

    public function setEmailContacto($email_contacto)
    {
        $this->email_contacto = $this->db->real_escape_string($email_contacto);
    }

    // Getters y setters para datos de facturación
    public function getRazonSocialFacturacion()
    {
        return $this->razon_social_facturacion;
    }

    public function setRazonSocialFacturacion($razon_social)
    {
        $this->razon_social_facturacion = $this->db->real_escape_string($razon_social);
    }

    public function getDireccionFacturacion()
    {
        return $this->direccion_facturacion;
    }

    public function setDireccionFacturacion($direccion)
    {
        $this->direccion_facturacion = $this->db->real_escape_string($direccion);
    }

    public function getCiudadFacturacion()
    {
        return $this->ciudad_facturacion;
    }

    public function setCiudadFacturacion($ciudad)
    {
        $this->ciudad_facturacion = $this->db->real_escape_string($ciudad);
    }

    public function getCodigoPostal()
    {
        return $this->codigo_postal;
    }

    public function setCodigoPostal($codigo)
    {
        $this->codigo_postal = $this->db->real_escape_string($codigo);
    }

    public function getContactoFacturacion()
    {
        return $this->contacto_facturacion;
    }

    public function setContactoFacturacion($contacto)
    {
        $this->contacto_facturacion = $this->db->real_escape_string($contacto);
    }

    public function getEmailFacturacion()
    {
        return $this->email_facturacion;
    }

    public function setEmailFacturacion($email)
    {
        $this->email_facturacion = $this->db->real_escape_string($email);
    }

    public function getPais()
    {
        return $this->pais;
    }

    public function setPais($pais)
    {
        $this->pais = $this->db->real_escape_string($pais);
    }

    public function getCodigoPais()
    {
        return $this->codigo_pais;
    }

    public function setCodigoPais($codigo_pais)
    {
        $this->codigo_pais = $this->db->real_escape_string($codigo_pais);
    }

    public function getImagenEmpresa()
    {
        return $this->imagen_empresa;
    }

    public function setImagenEmpresa($imagen_empresa)
    {
        $this->imagen_empresa = $this->db->real_escape_string($imagen_empresa);
    }

    public function getImagenDocumento()
    {
        return $this->imagen_documento;
    }

    public function setImagenDocumento($imagen_documento)
    {
        $this->imagen_documento = $this->db->real_escape_string($imagen_documento);
    }

    public function getImagenFirma()
    {
        return $this->imagen_firma;
    }

    public function setImagenFirma($imagen_firma)
    {
        $this->imagen_firma = $this->db->real_escape_string($imagen_firma);
    }

    public function getTipoMoneda()
    {
        return $this->tipo_moneda;
    }

    public function setTipoMoneda($tipo_moneda)
    {
        $monedas_validas = ['CLP', 'USD', 'EUR'];
        if (in_array($tipo_moneda, $monedas_validas)) {
            $this->tipo_moneda = $tipo_moneda;
        } else {
            $this->tipo_moneda = 'CLP'; // Valor por defecto
        }
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $estados_validos = ['activa', 'suspendida'];
        if (in_array($estado, $estados_validos)) {
            $this->estado = $estado;
        } else {
            $this->estado = 'activa'; // Valor por defecto
        }
    }

    public function getEsDemo()
    {
        return $this->es_demo;
    }

    public function setEsDemo($es_demo)
    {
        $opciones_validas = ['Si', 'No'];
        if (in_array($es_demo, $opciones_validas)) {
            $this->es_demo = $es_demo;
        } else {
            $this->es_demo = 'No'; // Valor por defecto
        }
    }

    public function getDemoInicio()
    {
        return $this->demo_inicio;
    }

    public function setDemoInicio($demo_inicio)
    {
        $this->demo_inicio = $demo_inicio;
    }

    public function getDemoFin()
    {
        return $this->demo_fin;
    }

    public function setDemoFin($demo_fin)
    {
        $this->demo_fin = $demo_fin;
    }

    /**
     * Guarda una nueva empresa en la base de datos
     * 
     * @return bool|int ID de la empresa creada o false si falla
     */
    public function save()
    {
        try {
            $sql = "INSERT INTO empresas (
            usuario_id, nombre, identificacion_fiscal, direccion, telefono, 
            email_contacto, razon_social_facturacion, direccion_facturacion, 
            ciudad_facturacion, codigo_postal, contacto_facturacion, email_facturacion,
            pais, codigo_pais, imagen_empresa, imagen_documento, imagen_firma,
            tipo_moneda, estado, es_demo, demo_inicio, demo_fin
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            // Asegurar que el usuario_id es un entero
            $usuario_id = (int)$this->usuario_id;

            // Formatear fechas correctamente o dejarlas como NULL si no existen
            $demo_inicio = !empty($this->demo_inicio) ? date('Y-m-d', strtotime($this->demo_inicio)) : null;
            $demo_fin = !empty($this->demo_fin) ? date('Y-m-d', strtotime($this->demo_fin)) : null;

            // Binding de parámetros con tipos correctos
            $stmt->bind_param(
                "isssssssssssssssssssss",
                $usuario_id,
                $this->nombre,
                $this->identificacion_fiscal,
                $this->direccion,
                $this->telefono,
                $this->email_contacto,
                $this->razon_social_facturacion,
                $this->direccion_facturacion,
                $this->ciudad_facturacion,
                $this->codigo_postal,
                $this->contacto_facturacion,
                $this->email_facturacion,
                $this->pais,
                $this->codigo_pais,
                $this->imagen_empresa,
                $this->imagen_documento,
                $this->imagen_firma,
                $this->tipo_moneda,
                $this->estado,
                $this->es_demo,
                $demo_inicio,
                $demo_fin
            );

            if ($stmt->execute()) {
                $id = $this->db->insert_id;
                $stmt->close();
                return $id;
            }

            error_log("Error en execute: " . $stmt->error);
            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en save: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una empresa existente en la base de datos
     * 
     * @return bool Resultado de la operación
     */
    public function update()
    {
        try {
            $sql = "UPDATE empresas SET 
                usuario_id = ?,
                nombre = ?,
                identificacion_fiscal = ?,
                direccion = ?,
                telefono = ?,
                email_contacto = ?,
                razon_social_facturacion = ?,
                direccion_facturacion = ?,
                ciudad_facturacion = ?,
                codigo_postal = ?,
                contacto_facturacion = ?,
                email_facturacion = ?,
                pais = ?,
                codigo_pais = ?,
                imagen_empresa = ?,
                imagen_documento = ?,
                imagen_firma = ?,
                tipo_moneda = ?,
                estado = ?,
                es_demo = ?,
                demo_inicio = ?,
                demo_fin = ?
                WHERE id = ?";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            // Valores por defecto para campos NULL
            $demo_inicio = $this->demo_inicio ?: null;
            $demo_fin = $this->demo_fin ?: null;

            $stmt->bind_param(
                "issssssssssssssssssssssi",
                $this->usuario_id,
                $this->nombre,
                $this->identificacion_fiscal,
                $this->direccion,
                $this->telefono,
                $this->email_contacto,
                $this->razon_social_facturacion,
                $this->direccion_facturacion,
                $this->ciudad_facturacion,
                $this->codigo_postal,
                $this->contacto_facturacion,
                $this->email_facturacion,
                $this->pais,
                $this->codigo_pais,
                $this->imagen_empresa,
                $this->imagen_documento,
                $this->imagen_firma,
                $this->tipo_moneda,
                $this->estado,
                $this->es_demo,
                $demo_inicio,
                $demo_fin,
                $this->id
            );

            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una empresa por su ID
     * 
     * @param int $id ID de la empresa a buscar
     * @return object|false Objeto con datos de la empresa o false si no se encuentra
     */
    public function getById($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            $sql = "SELECT * FROM empresas WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $empresa = $result->fetch_object();
                $stmt->close();
                return $empresa;
            }

            $stmt->close();
            return false;
        } catch (Exception $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todas las empresas con su información de suscripción activa
     * 
     * @return array Lista de empresas
     */
    public function getAll()
    {
        try {
            // Construir la consulta base con JOIN a la suscripción activa
            $sql = "SELECT e.*, u.nombre as admin_nombre, u.apellido as admin_apellido, 
                u.email as admin_email, p.nombre as plan_nombre, s.id as suscripcion_id,
                s.estado as suscripcion_estado, s.periodo_facturacion
               FROM empresas e 
               LEFT JOIN usuarios u ON e.usuario_id = u.id 
               LEFT JOIN (
                   SELECT * FROM suscripciones 
                   WHERE estado IN ('Activa', 'Pendiente') 
                   ORDER BY id DESC
               ) AS s ON e.id = s.empresa_id AND s.estado IN ('Activa', 'Pendiente')
               LEFT JOIN planes p ON s.plan_id = p.id
               GROUP BY e.id
               ORDER BY e.id DESC";

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return [];
            }

            $stmt->execute();
            $result = $stmt->get_result();

            $empresas = [];
            while ($empresa = $result->fetch_object()) {
                // Si hay una suscripción activa, añadir la información completa
                if (!empty($empresa->suscripcion_id)) {
                    $empresa->suscripcion = new stdClass();
                    $empresa->suscripcion->id = $empresa->suscripcion_id;
                    $empresa->suscripcion->estado = $empresa->suscripcion_estado;
                    $empresa->suscripcion->periodo_facturacion = $empresa->periodo_facturacion;
                    $empresa->suscripcion->plan_nombre = $empresa->plan_nombre;
                }

                // Eliminar campos redundantes
                unset($empresa->suscripcion_id);
                unset($empresa->suscripcion_estado);
                unset($empresa->periodo_facturacion);

                $empresas[] = $empresa;
            }

            $stmt->close();
            return $empresas;
        } catch (Exception $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cuenta el número total de empresas según los criterios de filtrado
     * 
     * @param array $filters Criterios de filtrado (opcional)
     * @return int Número total de empresas
     */
    public function countAll($filters = [])
    {
        try {
            // Construir la consulta base
            $sql = "SELECT COUNT(*) as total FROM empresas WHERE 1=1";
            $params = [];
            $types = "";

            // Aplicar filtros si existen
            if (!empty($filters)) {
                if (isset($filters['estado']) && $filters['estado']) {
                    $sql .= " AND estado = ?";
                    $params[] = $filters['estado'];
                    $types .= "s";
                }

                if (isset($filters['es_demo']) && $filters['es_demo']) {
                    $sql .= " AND es_demo = ?";
                    $params[] = $filters['es_demo'];
                    $types .= "s";
                }

                if (isset($filters['pais']) && $filters['pais']) {
                    $sql .= " AND codigo_pais = ?";
                    $params[] = $filters['pais'];
                    $types .= "s";
                }

                if (isset($filters['busqueda']) && $filters['busqueda']) {
                    $busqueda = "%" . $filters['busqueda'] . "%";
                    $sql .= " AND (nombre LIKE ? OR identificacion_fiscal LIKE ?)";
                    $params[] = $busqueda;
                    $params[] = $busqueda;
                    $types .= "ss";
                }
            }

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return 0;
            }

            // Bind de parámetros si existen
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_object();

            $stmt->close();
            return (int)$row->total;
        } catch (Exception $e) {
            error_log("Error en countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Elimina una empresa por su ID
     * 
     * @param int $id ID de la empresa a eliminar
     * @return bool Resultado de la operación
     */
    public function delete($id)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            $sql = "DELETE FROM empresas WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("i", $id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en delete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cambia el estado de una empresa
     * 
     * @param int $id ID de la empresa
     * @param string $estado Nuevo estado ('activa' o 'suspendida')
     * @return bool Resultado de la operación
     */
    public function cambiarEstado($id, $estado)
    {
        try {
            $id = (int)$id; // Asegurar que es un entero

            // Validar estado
            if (!in_array($estado, ['activa', 'suspendida'])) {
                return false;
            }

            $sql = "UPDATE empresas SET estado = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }

            $stmt->bind_param("si", $estado, $id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        } catch (Exception $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si una identificación fiscal ya existe
     * 
     * @param string $identificacion Identificación fiscal a verificar
     * @param int $exclude_id ID de empresa a excluir de la verificación (opcional)
     * @return bool True si la identificación ya existe
     */
    public function identificacionExists($identificacion, $exclude_id = null)
    {
        try {
            // No verificar si está vacío
            if (empty($identificacion)) {
                return false;
            }

            $sql = "SELECT id FROM empresas WHERE identificacion_fiscal = ?";

            // Si se proporciona un ID para excluir, modificar la consulta
            if ($exclude_id) {
                $sql .= " AND id != ?";
            }

            $stmt = $this->db->prepare($sql);

            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return true; // Devolver true por precaución
            }

            if ($exclude_id) {
                $stmt->bind_param("si", $identificacion, $exclude_id);
            } else {
                $stmt->bind_param("s", $identificacion);
            }

            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();

            return $exists;
        } catch (Exception $e) {
            error_log("Error en identificacionExists: " . $e->getMessage());
            return true; // Devolver true por precaución
        }
    }
}
