-- ============================================================================
-- CUBIC SISTEMA DE GESTIÓN PARA EMPRESAS DE EVENTOS Y REPRESENTACIÓN ARTÍSTICA
-- ARQUITECTURA MULTI-TENANT PARA MODELO SAAS
-- ============================================================================
-- Versión con implementación de eliminación lógica (soft delete)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS cubic_bd 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE cubic_bd;

-- Eliminamos tablas existentes en orden inverso a las dependencias
DROP TABLE IF EXISTS evento_archivos;
DROP TABLE IF EXISTS artista_archivos;
DROP TABLE IF EXISTS eventos;
DROP TABLE IF EXISTS artistas;
DROP TABLE IF EXISTS giras;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS facturas;
DROP TABLE IF EXISTS historial_cambios_suscripcion;
DROP TABLE IF EXISTS suscripciones;
DROP TABLE IF EXISTS planes;
DROP TABLE IF EXISTS empresas;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS system_admins;

-- ============================================================================
-- SECCIÓN 1: ADMINISTRACIÓN DEL SISTEMA Y CONTROL DE ACCESO
-- ============================================================================

CREATE TABLE system_admins (
    id INT AUTO_INCREMENT,
    
    -- Información básica
    nombre VARCHAR(50) NOT NULL COMMENT 'Nombre del administrador',
    apellido VARCHAR(50) NOT NULL COMMENT 'Apellido del administrador',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Correo electrónico (usado para login)',
    telefono VARCHAR(20) NULL COMMENT 'Número de teléfono (opcional)',
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' 
        COMMENT 'Estado del administrador en el sistema',
    
    -- Seguridad avanzada
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña encriptada',
    token_recuperacion VARCHAR(100) NULL COMMENT 'Token para recuperación de contraseña',
    token_expiracion TIMESTAMP NULL COMMENT 'Fecha de expiración del token',
    remember_token VARCHAR(100) NULL COMMENT 'Token para funcionalidad Recuérdame',
    remember_token_expires TIMESTAMP NULL COMMENT 'Fecha de expiración del token Recuérdame',
    ultimo_login TIMESTAMP NULL COMMENT 'Fecha y hora del último acceso',
    ip_ultimo_acceso VARCHAR(45) NULL COMMENT 'IP del último acceso',
    intentos_fallidos INT DEFAULT 0 COMMENT 'Contador de intentos fallidos de login',
    two_factor_status ENUM('Activado', 'Desactivado') DEFAULT 'Desactivado' 
        COMMENT 'Estado de autenticación de dos factores',
    two_factor_secret VARCHAR(32) NULL COMMENT 'Secreto para 2FA',
    
    -- Preferencias y configuración
    idioma VARCHAR(5) DEFAULT 'es' COMMENT 'Código de idioma preferido',
    zona_horaria VARCHAR(50) DEFAULT 'America/Santiago' COMMENT 'Zona horaria del admin',
    notificaciones JSON COMMENT 'Preferencias de notificaciones {email: bool, sistema: bool}',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_admin_email (email),
    INDEX idx_admin_estado (estado),
    INDEX idx_admin_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados'
) ENGINE=InnoDB COMMENT='Administradores del sistema con acceso total a la plataforma';

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT,
    
    -- Información básica
    nombre VARCHAR(50) NOT NULL COMMENT 'Nombre del usuario',
    apellido VARCHAR(50) NOT NULL COMMENT 'Apellido del usuario',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Correo electrónico del usuario',
    telefono VARCHAR(20) NULL COMMENT 'Número de teléfono del usuario',
    
    -- Identificación internacional
    pais VARCHAR(50) NOT NULL DEFAULT 'Chile' COMMENT 'País de residencia del usuario',
    codigo_pais CHAR(2) NOT NULL DEFAULT 'CL' COMMENT 'Código ISO del país (ej: CL, US, ES)',
    numero_identificacion VARCHAR(30) NULL COMMENT 'Número de identificación según el país (RUT, DNI, SSN, etc.)',
    tipo_identificacion VARCHAR(20) DEFAULT 'RUT' COMMENT 'Tipo de documento de identificación (RUT, DNI, Pasaporte, etc.)',
    
    -- Configuración y permisos
    tipo_usuario ENUM('ADMIN') NOT NULL DEFAULT 'ADMIN' COMMENT 'Rol del usuario en el sistema',
    password VARCHAR(255) NOT NULL COMMENT 'Contraseña encriptada',
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' COMMENT 'Estado del usuario',
    
    -- Seguridad básica
    ultimo_login TIMESTAMP NULL COMMENT 'Último inicio de sesión',
    ip_ultimo_acceso VARCHAR(45) NULL COMMENT 'IP del último acceso',
    token_recuperacion VARCHAR(100) NULL COMMENT 'Token para recuperación de contraseña',
    token_expiracion TIMESTAMP NULL COMMENT 'Fecha de expiración del token',
    remember_token VARCHAR(100) NULL COMMENT 'Token para funcionalidad Recuérdame',
    remember_token_expires TIMESTAMP NULL COMMENT 'Fecha de expiración del token Recuérdame',
    intentos_fallidos INT DEFAULT 0 COMMENT 'Contador de intentos fallidos de login',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_usuario_email (email),
    INDEX idx_usuario_estado (estado),
    INDEX idx_usuario_identificacion (numero_identificacion),
    INDEX idx_usuario_pais (codigo_pais),
    INDEX idx_usuario_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados'
) ENGINE=InnoDB COMMENT='Usuarios administradores del sistema';

-- ============================================================================
-- SECCIÓN 2: ESTRUCTURAS MULTI-TENANT - EMPRESAS Y CLIENTES
-- ============================================================================

CREATE TABLE empresas (
    id INT AUTO_INCREMENT,
    usuario_id INT NOT NULL COMMENT 'Usuario administrador/dueño de la empresa',
    
    -- Información básica
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre o razón social de la empresa',
    identificacion_fiscal VARCHAR(30) NULL COMMENT 'Identificación fiscal según el país (RUT, NIF, EIN, etc.)',
    direccion VARCHAR(255) NOT NULL COMMENT 'Dirección física de la empresa',
    telefono VARCHAR(20) NULL COMMENT 'Teléfono de contacto principal',
    email_contacto VARCHAR(100) NULL COMMENT 'Email de contacto principal',
    
    -- Datos de facturación
    razon_social_facturacion VARCHAR(100) NULL COMMENT 'Razón social para facturación',
    direccion_facturacion VARCHAR(255) NULL COMMENT 'Dirección para facturación',
    ciudad_facturacion VARCHAR(100) NULL COMMENT 'Ciudad para facturación',
    codigo_postal VARCHAR(20) NULL COMMENT 'Código postal para facturación',
    contacto_facturacion VARCHAR(100) NULL COMMENT 'Nombre del contacto para facturación',
    email_facturacion VARCHAR(100) NULL COMMENT 'Email para envío de facturas',
    
    -- Localización
    pais VARCHAR(50) NOT NULL DEFAULT 'Chile' COMMENT 'País donde opera la empresa',
    codigo_pais CHAR(2) NOT NULL DEFAULT 'CL' COMMENT 'Código ISO del país (ej: CL, US, ES)',
    
    -- Recursos visuales
    imagen_empresa VARCHAR(255) NULL COMMENT 'Ruta del logo principal de la empresa',
    imagen_documento VARCHAR(255) NULL COMMENT 'Ruta de la imagen para membrete de documentos',
    imagen_firma VARCHAR(255) NULL COMMENT 'Ruta de la firma digital para documentos',
    
    -- Configuración del negocio
    tipo_moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda principal: CLP (Peso Chileno), USD (Dólar), EUR (Euro)',
    estado ENUM('activa', 'suspendida') DEFAULT 'activa' COMMENT 'Estado operacional de la empresa',
    
    -- Configuración demo
    es_demo ENUM('Si', 'No') DEFAULT 'No' COMMENT 'Indica si es cuenta de prueba',
    demo_inicio TIMESTAMP NULL COMMENT 'Inicio del período de prueba',
    demo_fin TIMESTAMP NULL COMMENT 'Fin del período de prueba',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_empresa_identificacion (identificacion_fiscal),
    INDEX idx_empresa_estado (estado),
    INDEX idx_empresa_demo (es_demo),
    INDEX idx_empresa_pais (codigo_pais),
    INDEX idx_empresa_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Empresas cliente del SaaS - Base de la arquitectura multi-tenant';

CREATE TABLE clientes (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa propietaria del cliente',
    
    -- Información personal
    nombres VARCHAR(100) NOT NULL COMMENT 'Nombres del cliente',
    apellidos VARCHAR(100) NOT NULL COMMENT 'Apellidos del cliente',
    numero_identificacion VARCHAR(30) NULL COMMENT 'Número de identificación según el país (RUT, DNI, SSN, etc.)',
    tipo_identificacion VARCHAR(20) DEFAULT 'RUT' COMMENT 'Tipo de documento de identificación (RUT, DNI, Pasaporte, etc.)',
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL COMMENT 'Género del cliente',
    
    -- Localización
    pais VARCHAR(50) NOT NULL DEFAULT 'Chile' COMMENT 'País de residencia del cliente',
    codigo_pais CHAR(2) NOT NULL DEFAULT 'CL' COMMENT 'Código ISO del país (ej: CL, US, ES)',
    
    -- Contacto
    correo VARCHAR(100) NULL COMMENT 'Correo electrónico del cliente',
    celular VARCHAR(15) NULL COMMENT 'Número de celular del cliente',
    
    -- Estado
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' 
        COMMENT 'Estado del cliente en el sistema',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_cliente_identificacion (numero_identificacion) COMMENT 'Búsquedas por identificación',
    INDEX idx_cliente_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_cliente_estado (estado) COMMENT 'Filtrado por estado',
    INDEX idx_cliente_pais (codigo_pais) COMMENT 'Filtrado por país',
    INDEX idx_cliente_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    CONSTRAINT fk_cliente_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Clientes separados por empresa (aislamiento multi-tenant)';

-- ============================================================================
-- SECCIÓN 3: GESTIÓN DE ARTISTAS Y SUS RECURSOS
-- ============================================================================

CREATE TABLE artistas (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa que representa al artista',
    
    -- Información básica
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre artístico o de presentación',
    genero_musical VARCHAR(50) NOT NULL COMMENT 'Género musical principal',
    descripcion TEXT COMMENT 'Biografía y descripción detallada',
    presentacion TEXT COMMENT 'Texto para incluir en cotizaciones',
    
    -- Recursos visuales
    imagen_presentacion VARCHAR(255) COMMENT 'Ruta de la imagen principal de presentación',
    logo_artista VARCHAR(255) COMMENT 'Ruta del logo del artista',
    
    -- Estado
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo' 
        COMMENT 'Estado del artista en el sistema',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_artista_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_artista_nombre (nombre) COMMENT 'Búsquedas por nombre',
    INDEX idx_artista_estado (estado) COMMENT 'Filtrado por estado',
    INDEX idx_artista_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    CONSTRAINT fk_artista_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Catálogo de artistas representados por cada empresa';

CREATE TABLE artista_archivos (
    id INT AUTO_INCREMENT,
    artista_id INT NOT NULL COMMENT 'Artista propietario del archivo',
    
    -- Información del archivo
    nombre_original VARCHAR(255) NOT NULL COMMENT 'Nombre original del archivo subido',
    nombre_archivo VARCHAR(255) NOT NULL COMMENT 'Nombre asignado en el sistema',
    extension VARCHAR(10) NOT NULL COMMENT 'Extensión del archivo (tipo)',
    ruta VARCHAR(255) NOT NULL COMMENT 'Ruta completa de almacenamiento',
    tamano INT NOT NULL COMMENT 'Tamaño en bytes',
    
    -- Auditoría
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_archivo_artista (artista_id) COMMENT 'Filtrado por artista',
    
    CONSTRAINT fk_archivo_artista 
        FOREIGN KEY (artista_id) 
        REFERENCES artistas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Archivos multimedia y documentos asociados a artistas';

-- ============================================================================
-- SECCIÓN 4: GESTIÓN DE GIRAS Y EVENTOS
-- ============================================================================

CREATE TABLE giras (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa organizadora de la gira',
    
    -- Información básica
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre o título de la gira',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_gira_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_gira_nombre (nombre) COMMENT 'Búsquedas por nombre',
    INDEX idx_gira_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    CONSTRAINT fk_gira_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Agrupación de eventos en giras o tours artísticos';

CREATE TABLE eventos (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa organizadora del evento',
    cliente_id INT COMMENT 'Cliente que solicita el evento',
    gira_id INT COMMENT 'Gira asociada al evento',
    artista_id INT COMMENT 'Artista que se presenta',
    
    -- Información básica
    nombre_evento VARCHAR(255) NOT NULL COMMENT 'Título del evento',
    tipo_evento VARCHAR(100) COMMENT 'Categoría o tipo de evento',
    valor_evento INT COMMENT 'Costo o valor del evento',
    
    -- Localización y tiempo
    fecha_evento DATE NOT NULL COMMENT 'Fecha programada',
    hora_evento TIME NULL COMMENT 'Hora de inicio',
    ciudad_evento VARCHAR(100) COMMENT 'Ciudad de realización',
    lugar_evento VARCHAR(255) COMMENT 'Ubicación específica',
    
    -- Servicios adicionales
    hotel ENUM('Si', 'No') DEFAULT 'No' COMMENT 'Incluye hospedaje',
    traslados ENUM('Si', 'No') DEFAULT 'No' COMMENT 'Incluye transporte',
    viaticos ENUM('Si', 'No') DEFAULT 'No' COMMENT 'Incluye viáticos',
    
    -- Estado y seguimiento
    estado_evento ENUM('Propuesta', 'Confirmado', 'Finalizado', 'Reagendado', 'Solicitado', 'Cancelado') 
        NOT NULL DEFAULT 'Propuesta' COMMENT 'Estado actual del evento',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_evento_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_evento_cliente (cliente_id) COMMENT 'Filtrado por cliente',
    INDEX idx_evento_artista (artista_id) COMMENT 'Filtrado por artista',
    INDEX idx_evento_gira (gira_id) COMMENT 'Filtrado por gira',
    INDEX idx_evento_fecha (fecha_evento) COMMENT 'Búsquedas por fecha',
    INDEX idx_evento_estado (estado_evento) COMMENT 'Filtrado por estado',
    INDEX idx_evento_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    CONSTRAINT fk_evento_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_evento_cliente 
        FOREIGN KEY (cliente_id) 
        REFERENCES clientes(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_evento_gira 
        FOREIGN KEY (gira_id) 
        REFERENCES giras(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_evento_artista 
        FOREIGN KEY (artista_id) 
        REFERENCES artistas(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
        
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Registro central de todos los eventos del sistema';

CREATE TABLE evento_archivos (
    id INT AUTO_INCREMENT,
    evento_id INT NOT NULL COMMENT 'Evento al que pertenece el archivo',
    
    -- Información del archivo
    nombre_original VARCHAR(255) NOT NULL COMMENT 'Nombre original del archivo',
    nombre_archivo VARCHAR(255) NOT NULL COMMENT 'Nombre en el sistema',
    extension VARCHAR(10) NOT NULL COMMENT 'Extensión del archivo',
    ruta VARCHAR(255) NOT NULL COMMENT 'Ruta de almacenamiento',
    tamano INT NOT NULL COMMENT 'Tamaño en bytes',
    
    -- Auditoría
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_archivo_evento (evento_id) COMMENT 'Filtrado por evento',
    
    CONSTRAINT fk_archivo_evento 
        FOREIGN KEY (evento_id) 
        REFERENCES eventos(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Archivos multimedia y documentos asociados a eventos';

-- ============================================================================
-- SECCIÓN 5: SISTEMA DE PLANES Y SUSCRIPCIONES
-- ============================================================================

CREATE TABLE planes (
    id INT AUTO_INCREMENT,
    
    -- Información básica del plan
    nombre VARCHAR(50) NOT NULL COMMENT 'Nombre comercial del plan (Básico, Profesional, Premium)',
    descripcion TEXT NOT NULL COMMENT 'Descripción detallada de lo que incluye el plan',
    tipo_plan ENUM('Básico', 'Profesional', 'Premium', 'Personalizado') NOT NULL DEFAULT 'Básico'
        COMMENT 'Categoría principal del plan',
    
    -- Precios según período de facturación
    precio_mensual DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación mensual',
    precio_semestral DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación semestral',
    precio_anual DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación anual',
    moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda del precio (CLP, USD, EUR)',
    
    -- Límites específicos de recursos por plan
    max_usuarios INT NOT NULL DEFAULT 1 COMMENT 'Cantidad máxima de usuarios permitidos',
    max_artistas INT NOT NULL DEFAULT 5 COMMENT 'Cantidad máxima de artistas',
    max_eventos INT NOT NULL DEFAULT 10 COMMENT 'Cantidad máxima de eventos mensuales',
    
    -- Características adicionales
    caracteristicas JSON COMMENT 'Características adicionales del plan',
    
    -- Estado del plan
    estado ENUM('Activo', 'Inactivo', 'Descontinuado') NOT NULL DEFAULT 'Activo'
        COMMENT 'Estado del plan para nuevas suscripciones',
    visible ENUM('Si', 'No') NOT NULL DEFAULT 'Si'
        COMMENT 'Si el plan es visible para nuevos clientes',
    
    -- Eliminación lógica
    deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si está eliminado (1=sí, 0=no)',
    deleted_at TIMESTAMP NULL COMMENT 'Fecha de eliminación lógica',
    deleted_by INT NULL COMMENT 'Usuario que realizó la eliminación',
    delete_reason VARCHAR(255) NULL COMMENT 'Motivo de la eliminación',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_plan_tipo (tipo_plan) COMMENT 'Filtrado por tipo de plan',
    INDEX idx_plan_estado (estado) COMMENT 'Filtrado por estado del plan',
    INDEX idx_plan_deleted (deleted) COMMENT 'Índice para filtrar registros eliminados',
    
    FOREIGN KEY (deleted_by)
        REFERENCES usuarios(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Catálogo de planes disponibles para suscripción';

CREATE TABLE suscripciones (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa suscrita',
    plan_id INT NOT NULL COMMENT 'Plan contratado',
    
    -- Información básica
    numero_suscripcion VARCHAR(20) NOT NULL COMMENT 'Identificador único para facturación',
    estado ENUM('Activa', 'Pendiente', 'Suspendida', 'Cancelada', 'Finalizada') 
        NOT NULL DEFAULT 'Activa' COMMENT 'Estado actual de la suscripción',
    
    -- Fechas clave
    fecha_inicio DATE NOT NULL COMMENT 'Fecha de inicio de la suscripción',
    fecha_fin DATE NULL COMMENT 'Fecha de finalización (si tiene)',
    fecha_siguiente_factura DATE NULL COMMENT 'Próxima fecha de facturación',
    fecha_cancelacion DATE NULL COMMENT 'Fecha en que se solicitó la cancelación',
    
    -- Datos de pago
    periodo_facturacion ENUM('Mensual', 'Semestral', 'Anual') NOT NULL DEFAULT 'Mensual',
    precio_total DECIMAL(10,2) NOT NULL COMMENT 'Precio total de la suscripción',
    moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda del precio',
    
    -- Metadatos
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    UNIQUE INDEX idx_suscripcion_numero (numero_suscripcion),
    INDEX idx_suscripcion_empresa (empresa_id),
    INDEX idx_suscripcion_plan (plan_id),
    INDEX idx_suscripcion_estado (estado),
    
    CONSTRAINT fk_suscripcion_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE,
        
    CONSTRAINT fk_suscripcion_plan 
        FOREIGN KEY (plan_id) 
        REFERENCES planes(id)
) ENGINE=InnoDB COMMENT='Suscripciones de empresas a planes';

CREATE TABLE facturas (
    id INT AUTO_INCREMENT,
    suscripcion_id INT NOT NULL COMMENT 'Suscripción relacionada',
    empresa_id INT NOT NULL COMMENT 'Empresa facturada',
    
    -- Información de la factura
    numero_factura VARCHAR(20) NOT NULL COMMENT 'Número único de factura',
    fecha_emision DATE NOT NULL COMMENT 'Fecha de emisión',
    fecha_vencimiento DATE NOT NULL COMMENT 'Fecha límite de pago',
    
    -- Detalles del pago
    monto_subtotal DECIMAL(10,2) NOT NULL COMMENT 'Monto antes de impuestos',
    monto_impuestos DECIMAL(10,2) NOT NULL COMMENT 'Monto de impuestos',
    monto_total DECIMAL(10,2) NOT NULL COMMENT 'Monto total a pagar',
    moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda de la factura',
    
    -- Periodo facturado
    periodo_inicio DATE NOT NULL COMMENT 'Inicio del período facturado',
    periodo_fin DATE NOT NULL COMMENT 'Fin del período facturado',
    
    -- Estado
    estado ENUM('Pendiente', 'Pagada', 'Vencida', 'Cancelada', 'Anulada') 
        NOT NULL DEFAULT 'Pendiente' COMMENT 'Estado actual de la factura',
    
    -- Pago
    fecha_pago DATE NULL COMMENT 'Fecha en que se realizó el pago',
    metodo_pago ENUM('Tarjeta de Crédito', 'Transferencia', 'PayPal', 'Otro') 
        NULL COMMENT 'Método utilizado para el pago',
    referencia_pago VARCHAR(100) NULL COMMENT 'Referencia o identificador del pago',
    
    -- Notas y comentarios
    notas TEXT NULL COMMENT 'Notas adicionales',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    UNIQUE INDEX idx_factura_numero (numero_factura) COMMENT 'Para búsquedas por número',
    INDEX idx_factura_suscripcion (suscripcion_id) COMMENT 'Filtrado por suscripción',
    INDEX idx_factura_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_factura_estado (estado) COMMENT 'Filtrado por estado',
    INDEX idx_factura_fechas (fecha_emision, fecha_vencimiento, fecha_pago) 
        COMMENT 'Búsquedas por fechas',
    
    CONSTRAINT fk_factura_suscripcion 
        FOREIGN KEY (suscripcion_id) 
        REFERENCES suscripciones(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_factura_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Sistema de facturación para suscripciones';

CREATE TABLE historial_cambios_suscripcion (
    id INT AUTO_INCREMENT,
    suscripcion_id INT NOT NULL COMMENT 'Suscripción modificada',
    usuario_id INT NULL COMMENT 'Usuario que realizó el cambio',
    
    -- Información del cambio
    tipo_cambio ENUM('Nuevo Plan', 'Cambio Periodo', 'Renovación', 'Cancelación', 
                    'Suspensión', 'Reactivación', 'Actualización Pago', 'Otro') 
        NOT NULL COMMENT 'Tipo de cambio realizado',
    
    -- Valores antes del cambio
    plan_anterior INT NULL COMMENT 'ID del plan anterior',
    periodo_anterior ENUM('Mensual', 'Semestral', 'Anual') NULL COMMENT 'Período anterior',
    estado_anterior VARCHAR(50) NULL COMMENT 'Estado anterior',
    
    -- Valores después del cambio
    plan_nuevo INT NULL COMMENT 'ID del nuevo plan',
    periodo_nuevo ENUM('Mensual', 'Semestral', 'Anual') NULL COMMENT 'Nuevo período',
    estado_nuevo VARCHAR(50) NULL COMMENT 'Nuevo estado',
    
    -- Detalles
    descripcion TEXT NULL COMMENT 'Descripción detallada del cambio',
    fecha_efectiva DATE NOT NULL COMMENT 'Fecha en que el cambio toma efecto',
    
    -- Auditoría
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_historial_suscripcion (suscripcion_id) COMMENT 'Filtrado por suscripción',
    INDEX idx_historial_usuario (usuario_id) COMMENT 'Filtrado por usuario',
    INDEX idx_historial_fecha (fecha_efectiva) COMMENT 'Búsquedas por fecha',
    INDEX idx_historial_tipo (tipo_cambio) COMMENT 'Filtrado por tipo de cambio',
    
    CONSTRAINT fk_historial_suscripcion 
        FOREIGN KEY (suscripcion_id) 
        REFERENCES suscripciones(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_historial_usuario 
        FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_historial_plan_anterior 
        FOREIGN KEY (plan_anterior) 
        REFERENCES planes(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_historial_plan_nuevo 
        FOREIGN KEY (plan_nuevo) 
        REFERENCES planes(id) 
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Registro histórico de todos los cambios en suscripciones';

-- ============================================================================
-- SECCIÓN 6: PROCEDIMIENTOS ALMACENADOS PARA ELIMINACIÓN LÓGICA
-- ============================================================================

DELIMITER //

-- Procedimiento para eliminar lógicamente una empresa y sus registros relacionados
CREATE PROCEDURE sp_eliminar_empresa_logica(
    IN p_empresa_id INT,
    IN p_usuario_id INT,
    IN p_motivo VARCHAR(255)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al realizar la eliminación lógica';
    END;
    
    START TRANSACTION;
    
    -- Actualizar la empresa
    UPDATE empresas 
    SET deleted = 1, 
        deleted_at = NOW(), 
        deleted_by = p_usuario_id, 
        delete_reason = p_motivo 
    WHERE id = p_empresa_id;
    
    -- Actualizar clientes de la empresa
    UPDATE clientes 
    SET deleted = 1, 
        deleted_at = NOW(), 
        deleted_by = p_usuario_id, 
        delete_reason = CONCAT('Eliminación en cascada: empresa ', p_empresa_id) 
    WHERE empresa_id = p_empresa_id;
    
    -- Actualizar artistas de la empresa
    UPDATE artistas 
    SET deleted = 1, 
        deleted_at = NOW(), 
        deleted_by = p_usuario_id, 
        delete_reason = CONCAT('Eliminación en cascada: empresa ', p_empresa_id) 
    WHERE empresa_id = p_empresa_id;
    
    -- Actualizar giras de la empresa
    UPDATE giras 
    SET deleted = 1, 
        deleted_at = NOW(), 
        deleted_by = p_usuario_id, 
        delete_reason = CONCAT('Eliminación en cascada: empresa ', p_empresa_id) 
    WHERE empresa_id = p_empresa_id;
    
    -- Actualizar eventos de la empresa
    UPDATE eventos 
    SET deleted = 1, 
        deleted_at = NOW(), 
        deleted_by = p_usuario_id, 
        delete_reason = CONCAT('Eliminación en cascada: empresa ', p_empresa_id) 
    WHERE empresa_id = p_empresa_id;
    
    COMMIT;
END //

-- Procedimiento para restaurar lógicamente una empresa y sus registros relacionados
CREATE PROCEDURE sp_restaurar_empresa_logica(
    IN p_empresa_id INT,
    IN p_usuario_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error al realizar la restauración lógica';
    END;
    
    START TRANSACTION;
    
    -- Restaurar la empresa
    UPDATE empresas 
    SET deleted = 0, 
        deleted_at = NULL, 
        deleted_by = NULL, 
        delete_reason = NULL 
    WHERE id = p_empresa_id;
    
    -- Restaurar clientes de la empresa (sólo los eliminados en cascada)
    UPDATE clientes 
    SET deleted = 0, 
        deleted_at = NULL, 
        deleted_by = NULL, 
        delete_reason = NULL 
    WHERE empresa_id = p_empresa_id 
    AND delete_reason LIKE CONCAT('Eliminación en cascada: empresa ', p_empresa_id, '%');
    
    -- Restaurar artistas de la empresa (sólo los eliminados en cascada)
    UPDATE artistas 
    SET deleted = 0, 
        deleted_at = NULL, 
        deleted_by = NULL, 
        delete_reason = NULL 
    WHERE empresa_id = p_empresa_id 
    AND delete_reason LIKE CONCAT('Eliminación en cascada: empresa ', p_empresa_id, '%');
    
    -- Restaurar giras de la empresa (sólo las eliminadas en cascada)
    UPDATE giras 
    SET deleted = 0, 
        deleted_at = NULL, 
        deleted_by = NULL, 
        delete_reason = NULL 
    WHERE empresa_id = p_empresa_id 
    AND delete_reason LIKE CONCAT('Eliminación en cascada: empresa ', p_empresa_id, '%');
    
    -- Restaurar eventos de la empresa (sólo los eliminados en cascada)
    UPDATE eventos 
    SET deleted = 0, 
        deleted_at = NULL, 
        deleted_by = NULL, 
        delete_reason = NULL 
    WHERE empresa_id = p_empresa_id 
    AND delete_reason LIKE CONCAT('Eliminación en cascada: empresa ', p_empresa_id, '%');
    
    COMMIT;
END //

-- Procedimiento para eliminar definitivamente registros marcados como eliminados
CREATE PROCEDURE sp_limpiar_registros_eliminados(
    IN p_dias_antiguedad INT
)
BEGIN
    DECLARE fecha_limite DATETIME;
    SET fecha_limite = DATE_SUB(NOW(), INTERVAL p_dias_antiguedad DAY);
    
    -- Eliminar eventos
    DELETE FROM eventos 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar artistas
    DELETE FROM artistas 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar giras
    DELETE FROM giras 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar clientes
    DELETE FROM clientes 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar empresas
    DELETE FROM empresas 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar planes
    DELETE FROM planes 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar usuarios
    DELETE FROM usuarios 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
    
    -- Eliminar administradores
    DELETE FROM system_admins 
    WHERE deleted = 1 AND deleted_at < fecha_limite;
END //

DELIMITER ;