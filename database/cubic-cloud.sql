-- ============================================================================
-- CUBIC SISTEMA DE GESTIÓN PARA EMPRESAS DE EVENTOS Y REPRESENTACIÓN ARTÍSTICA
-- ARQUITECTURA MULTI-TENANT PARA MODELO SAAS
-- ============================================================================
-- Este archivo contiene la estructura completa de la base de datos para el sistema
-- CUBIC, una plataforma SaaS para la gestión de empresas que organizan eventos
-- y/o representan artistas. La arquitectura está diseñada para soportar múltiples
-- empresas (multi-tenant), donde cada empresa tiene sus propios clientes, artistas,
-- eventos y recursos, todos separados de manera segura pero en una única instancia
-- de la aplicación.
--
-- La estructura implementa un sistema de suscripciones basado en planes con
-- diferentes niveles de servicio (Básico, Profesional, Premium), que pueden
-- ser contratados por diferentes períodos (mensual, semestral, anual) con
-- descuentos según el compromiso de tiempo.
-- ============================================================================

-- ========================================
-- Creación de la base de datos
-- Utilizamos UTF-8 para soporte completo de caracteres internacionales
-- ========================================
CREATE DATABASE IF NOT EXISTS cubic_bd 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE cubic_bd;

-- Eliminamos tablas existentes en orden inverso a las dependencias
-- para evitar conflictos de claves foráneas durante la reinstalación
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

-- ========================================
-- Tabla: system_admins
-- Propósito: Gestionar los administradores de la plataforma SaaS completa
-- ========================================
-- Esta tabla está separada de los usuarios regulares para mayor seguridad.
-- Los administradores del sistema tienen acceso a todas las empresas y 
-- funcionalidades de gestión de la plataforma. Se implementa seguridad avanzada
-- incluyendo 2FA, tokens de recuperación, y registro detallado de accesos.
-- ========================================
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
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_admin_email (email),
    INDEX idx_admin_estado (estado)
) ENGINE=InnoDB COMMENT='Administradores del sistema con acceso total a la plataforma';
-- ========================================
-- Tabla: usuarios
-- Propósito: Gestionar los usuarios que pueden acceder a las empresas
-- ========================================
-- Estos usuarios son los que gestionan empresas dentro del sistema.
-- Pueden tener diferentes roles (ADMIN, VENDEDOR, TOUR_MANAGER) y se
-- relacionan con empresas específicas. Incluye soporte internacional
-- con campos para diferentes tipos de identificación según el país.
-- ========================================
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
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_usuario_email (email),
    INDEX idx_usuario_estado (estado),
    INDEX idx_usuario_identificacion (numero_identificacion),
    INDEX idx_usuario_pais (codigo_pais)
) ENGINE=InnoDB COMMENT='Usuarios administradores del sistema';
-- ============================================================================
-- SECCIÓN 2: ESTRUCTURAS MULTI-TENANT - EMPRESAS Y CLIENTES
-- ============================================================================

-- ========================================
-- Tabla: empresas
-- Propósito: Componente central de la arquitectura multi-tenant
-- ========================================
-- Esta tabla es fundamental para el modelo SaaS. Cada empresa representa
-- un "tenant" (inquilino) en el sistema y tiene sus propios recursos,
-- configuraciones y límites según el plan contratado.
-- 
-- La tabla incluye información completa para la facturación, configuración
-- regional, recursos visuales personalizados, y límites de uso según el
-- plan de suscripción activo.
-- ========================================
CREATE TABLE empresas (
    id INT AUTO_INCREMENT,
    usuario_id INT NOT NULL COMMENT 'Usuario administrador/dueño de la empresa',
    
    -- Información básica
    nombre VARCHAR(100) NOT NULL COMMENT 'Nombre o razón social de la empresa',
    identificacion_fiscal VARCHAR(30) NULL COMMENT 'Identificación fiscal según el país (RUT, NIF, EIN, etc.)',
    direccion VARCHAR(255) NOT NULL COMMENT 'Dirección física de la empresa',
    telefono VARCHAR(20) NULL COMMENT 'Teléfono de contacto principal',
    email_contacto VARCHAR(100) NULL COMMENT 'Email de contacto principal',
    
    -- Datos de facturación (formato JSON para mayor flexibilidad)
    datos_facturacion JSON NULL COMMENT 'Datos para facturación: 
    {
      "razon_social": "string",
      "direccion_facturacion": "string",
      "ciudad_facturacion": "string",
      "codigo_postal": "string",
      "contacto_facturacion": "string",
      "email_facturacion": "string"
    }',
    
    -- Localización
    pais VARCHAR(50) NOT NULL DEFAULT 'Chile' COMMENT 'País donde opera la empresa',
    codigo_pais CHAR(2) NOT NULL DEFAULT 'CL' COMMENT 'Código ISO del país (ej: CL, US, ES)',
    
    -- Recursos visuales
    imagen_empresa VARCHAR(255) NULL COMMENT 'Ruta del logo principal de la empresa',
    imagen_documento VARCHAR(255) NULL COMMENT 'Ruta de la imagen para membrete de documentos',
    imagen_firma VARCHAR(255) NULL COMMENT 'Ruta de la firma digital para documentos',
    
    -- Límites según el plan de suscripción
    limite_usuarios INT DEFAULT 1 COMMENT 'Límite de usuarios según plan actual',
    limite_eventos INT DEFAULT 10 COMMENT 'Límite de eventos según plan actual',
    limite_artistas INT DEFAULT 5 COMMENT 'Límite de artistas según plan actual',
    limite_almacenamiento INT DEFAULT 100 COMMENT 'Límite de almacenamiento en MB',
    
    -- Configuración del negocio
    tipo_moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda principal: CLP (Peso Chileno), USD (Dólar), EUR (Euro)',
    estado ENUM('activa', 'suspendida') DEFAULT 'activa' COMMENT 'Estado operacional de la empresa',
    
    -- Configuración demo
    es_demo ENUM('Si', 'No') DEFAULT 'No' COMMENT 'Indica si es cuenta de prueba',
    demo_inicio TIMESTAMP NULL COMMENT 'Inicio del período de prueba',
    demo_fin TIMESTAMP NULL COMMENT 'Fin del período de prueba',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_empresa_identificacion (identificacion_fiscal),
    INDEX idx_empresa_estado (estado),
    INDEX idx_empresa_demo (es_demo),
    INDEX idx_empresa_pais (codigo_pais),
    INDEX idx_empresa_limites (limite_usuarios, limite_eventos, limite_artistas),
    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Empresas cliente del SaaS - Base de la arquitectura multi-tenant';
-- ========================================
-- Tabla: clientes
-- Propósito: Gestionar la cartera de clientes de cada empresa
-- ========================================
-- Los clientes pertenecen a empresas específicas (tenant isolation).
-- Cada empresa gestiona su propia cartera de clientes de forma independiente.
-- Se incluye soporte internacional con campos para diferentes tipos de
-- identificación y localización según el país.
-- ========================================
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
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_cliente_identificacion (numero_identificacion) COMMENT 'Búsquedas por identificación',
    INDEX idx_cliente_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_cliente_estado (estado) COMMENT 'Filtrado por estado',
    INDEX idx_cliente_pais (codigo_pais) COMMENT 'Filtrado por país',
    
    CONSTRAINT fk_cliente_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Clientes separados por empresa (aislamiento multi-tenant)';
-- ============================================================================
-- SECCIÓN 3: GESTIÓN DE ARTISTAS Y SUS RECURSOS
-- ============================================================================

-- ========================================
-- Tabla: artistas
-- Propósito: Catálogo de artistas gestionados por cada empresa
-- ========================================
-- Esta tabla almacena la información de los artistas representados por cada
-- empresa. Mantiene el aislamiento multi-tenant, donde cada empresa solo
-- puede ver y gestionar sus propios artistas.
--
-- Incluye campos para almacenar información detallada como biografía,
-- género musical, textos de presentación para cotizaciones, y recursos
-- visuales como imágenes y logos.
-- ========================================
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
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_artista_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_artista_nombre (nombre) COMMENT 'Búsquedas por nombre',
    INDEX idx_artista_estado (estado) COMMENT 'Filtrado por estado',
    
    CONSTRAINT fk_artista_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Catálogo de artistas representados por cada empresa';

-- ========================================
-- Tabla: artista_archivos
-- Propósito: Gestión de archivos multimedia relacionados con artistas
-- ========================================
-- Almacena los diferentes archivos asociados a cada artista, como fotos
-- adicionales, videos, archivos de audio, contratos, riders técnicos, etc.
--
-- Esta tabla permite un control detallado sobre el almacenamiento utilizado
-- por cada empresa, facilitando la verificación de límites según el plan contratado.
-- ========================================
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

-- ========================================
-- Tabla: giras
-- Propósito: Organizar eventos relacionados en giras artísticas
-- ========================================
-- Permite agrupar múltiples eventos bajo una misma gira o tour,
-- facilitando la organización y logística para eventos que forman
-- parte de una misma serie o recorrido artístico.
--
-- Cada gira pertenece a una empresa específica (aislamiento multi-tenant).
-- ========================================
CREATE TABLE giras (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa organizadora de la gira',
    
    -- Información básica
    nombre VARCHAR(255) NOT NULL COMMENT 'Nombre o título de la gira',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_gira_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_gira_nombre (nombre) COMMENT 'Búsquedas por nombre',
    
    CONSTRAINT fk_gira_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Agrupación de eventos en giras o tours artísticos';
-- ========================================
-- Tabla: eventos
-- Propósito: Registro principal de todos los eventos
-- ========================================
-- Tabla central para la gestión de eventos. Cada evento puede estar asociado
-- a un cliente, un artista y opcionalmente a una gira específica.
--
-- Incluye información detallada sobre cada evento: ubicación, fecha, hora,
-- tipo, valor económico, servicios adicionales incluidos y estado actual.
--
-- Mantiene el aislamiento multi-tenant, donde cada empresa solo puede ver
-- y gestionar sus propios eventos.
-- ========================================
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
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Registro central de todos los eventos del sistema';
-- ========================================
-- Tabla: evento_archivos
-- Propósito: Gestión de archivos relacionados con eventos
-- ========================================
-- Almacena los diferentes archivos asociados a cada evento, como contratos,
-- riders técnicos, planos, fotografías, grabaciones, comprobantes, etc.
--
-- Esta tabla permite un control detallado sobre el almacenamiento utilizado
-- por cada empresa, facilitando la verificación de límites según el plan contratado.
-- ========================================
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
-- Esta sección implementa un completo sistema de suscripciones basado en planes,
-- permitiendo diferentes niveles de servicio y períodos de facturación.
-- El modelo permite:
--   1. Ofrecer distintos niveles (Básico, Profesional, Premium)
--   2. Varias opciones de facturación (mensual, semestral, anual) con descuentos
--   3. Control detallado de límites y características por plan
--   4. Seguimiento completo del ciclo de vida de las suscripciones
--   5. Historial de cambios y renovaciones
--   6. Sistema de facturación integrado
-- ============================================================================

-- ========================================
-- Tabla: planes
-- Propósito: Definir los diferentes niveles de servicio disponibles
-- ========================================
-- Almacena la información completa de cada plan ofrecido, incluyendo:
-- - Niveles de servicio (Básico, Profesional, Premium, Personalizado)
-- - Precios según período de facturación (mensual, semestral, anual)
-- - Límites de recursos específicos (usuarios, eventos, artistas, almacenamiento)
-- - Características adicionales mediante un campo JSON para mayor flexibilidad
--
-- Esta estructura permite crear fácilmente nuevos planes o modificar existentes
-- sin alterar la estructura de la base de datos.
-- ========================================
CREATE TABLE planes (
    id INT AUTO_INCREMENT,
    
    -- Información básica del plan
    nombre VARCHAR(50) NOT NULL COMMENT 'Nombre comercial del plan (Básico, Profesional, Premium)',
    descripcion TEXT NOT NULL COMMENT 'Descripción detallada de lo que incluye el plan',
    tipo_plan ENUM('Básico', 'Profesional', 'Premium', 'Personalizado') NOT NULL DEFAULT 'Básico' 
        COMMENT 'Categoría principal del plan',
    
    -- Precios según período de facturación (con descuentos para compromisos más largos)
    precio_mensual DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación mensual',
    precio_semestral DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación semestral',
    precio_anual DECIMAL(10,2) NOT NULL COMMENT 'Precio para facturación anual',
    moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda del precio (CLP, USD, EUR)',
    
    -- Límites específicos de recursos por plan
    max_usuarios INT NOT NULL DEFAULT 1 COMMENT 'Cantidad máxima de usuarios permitidos',
    max_eventos INT NOT NULL DEFAULT 10 COMMENT 'Cantidad máxima de eventos mensuales',
    max_artistas INT NOT NULL DEFAULT 5 COMMENT 'Cantidad máxima de artistas',
    max_almacenamiento INT NOT NULL DEFAULT 100 COMMENT 'Almacenamiento máximo en MB',
    
    -- Características adicionales usando JSON para flexibilidad y extensibilidad
    caracteristicas JSON COMMENT 'Características adicionales del plan: 
        {
          "api_access": true/false, 
          "reportes_avanzados": true/false,
          "integraciones": true/false,
          "soporte_prioritario": true/false,
          "personalizacion": true/false
        }',
    
    -- Estado del plan
    estado ENUM('Activo', 'Inactivo', 'Descontinuado') NOT NULL DEFAULT 'Activo' 
        COMMENT 'Estado del plan para nuevas suscripciones',
    visible ENUM('Si', 'No') NOT NULL DEFAULT 'Si' 
        COMMENT 'Si el plan es visible para nuevos clientes',
    
    -- Auditoría
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    INDEX idx_plan_tipo (tipo_plan) COMMENT 'Filtrado por tipo de plan',
    INDEX idx_plan_estado (estado) COMMENT 'Filtrado por estado del plan'
) ENGINE=InnoDB COMMENT='Catálogo de planes disponibles para suscripción';
-- ========================================
-- Tabla: suscripciones
-- Propósito: Gestionar el ciclo de vida de las suscripciones
-- ========================================
-- Esta tabla conecta empresas con planes específicos y gestiona todo el
-- ciclo de vida de cada suscripción, desde su inicio hasta su finalización.
-- 
-- Características principales:
-- - Identificación única para facturación
-- - Control de fechas importantes (inicio, fin, próxima facturación)
-- - Gestión de precios con soporte para descuentos personalizados
-- - Estados que reflejan el ciclo completo (Activa, Pendiente, Periodo de Gracia, etc.)
-- - Configuración de renovación automática
-- - Información de pagos
-- ========================================
CREATE TABLE suscripciones (
    id INT AUTO_INCREMENT,
    empresa_id INT NOT NULL COMMENT 'Empresa suscrita',
    plan_id INT NOT NULL COMMENT 'Plan contratado',
    
    -- Información de la suscripción
    numero_suscripcion VARCHAR(20) NOT NULL COMMENT 'Identificador único para facturación',
    periodo_facturacion ENUM('Mensual', 'Semestral', 'Anual') NOT NULL DEFAULT 'Mensual' 
        COMMENT 'Período de facturación elegido',
    
    -- Fechas importantes
    fecha_inicio DATE NOT NULL COMMENT 'Fecha de inicio de la suscripción',
    fecha_fin DATE NULL COMMENT 'Fecha de finalización (si tiene)',
    fecha_siguiente_factura DATE NULL COMMENT 'Próxima fecha de facturación',
    fecha_cancelacion DATE NULL COMMENT 'Fecha en que se solicitó la cancelación',
    
    -- Precios y descuentos
    precio_base DECIMAL(10,2) NOT NULL COMMENT 'Precio base según plan y período',
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de descuento aplicado',
    descuento_motivo VARCHAR(100) NULL COMMENT 'Motivo del descuento',
    precio_final DECIMAL(10,2) NOT NULL COMMENT 'Precio final tras descuentos',
    moneda CHAR(3) NOT NULL DEFAULT 'CLP' COMMENT 'Moneda del precio',
    
    -- Estado y configuración
    estado ENUM('Activa', 'Pendiente', 'Periodo de Gracia', 'Cancelada', 'Suspendida', 'Finalizada') 
        NOT NULL DEFAULT 'Activa' COMMENT 'Estado actual de la suscripción',
    renovacion_automatica ENUM('Si', 'No') NOT NULL DEFAULT 'Si' 
        COMMENT 'Indica si la suscripción se renueva automáticamente',
    
    -- Información de pago
    metodo_pago ENUM('Tarjeta de Crédito', 'Transferencia', 'PayPal', 'Otro') 
        DEFAULT 'Tarjeta de Crédito' COMMENT 'Método de pago utilizado',
    referencia_pago VARCHAR(100) NULL COMMENT 'Referencia al método de pago (ej: últimos 4 dígitos)',
    
    -- Auditoría
    creado_por INT NULL COMMENT 'Usuario que creó la suscripción',
    actualizado_por INT NULL COMMENT 'Usuario que actualizó por última vez',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (id),
    UNIQUE INDEX idx_suscripcion_numero (numero_suscripcion) COMMENT 'Para búsquedas por número',
    INDEX idx_suscripcion_empresa (empresa_id) COMMENT 'Filtrado por empresa',
    INDEX idx_suscripcion_plan (plan_id) COMMENT 'Filtrado por plan',
    INDEX idx_suscripcion_estado (estado) COMMENT 'Filtrado por estado',
    INDEX idx_suscripcion_fechas (fecha_inicio, fecha_fin, fecha_siguiente_factura) 
        COMMENT 'Búsquedas por fechas',
    
    CONSTRAINT fk_suscripcion_empresa 
        FOREIGN KEY (empresa_id) 
        REFERENCES empresas(id) 
        ON DELETE CASCADE
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_suscripcion_plan 
        FOREIGN KEY (plan_id) 
        REFERENCES planes(id) 
        ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Gestión completa del ciclo de vida de suscripciones';
-- ========================================
-- Tabla: facturas
-- Propósito: Registro de facturación por suscripciones
-- ========================================
-- Almacena todas las facturas generadas por las suscripciones, permitiendo
-- un seguimiento detallado de los pagos, estados y vencimientos.
--
-- Características principales:
-- - Identificación única de cada factura
-- - Cálculo detallado de montos (subtotal, impuestos, total)
-- - Control del período facturado
-- - Seguimiento del estado de pago
-- - Información detallada de la transacción
-- ========================================
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
-- ========================================
-- Tabla: historial_cambios_suscripcion
-- Propósito: Registrar todos los cambios en las suscripciones
-- ========================================
-- Mantiene un registro detallado de todos los cambios realizados en las
-- suscripciones a lo largo del tiempo, ofreciendo una auditoría completa
-- y permitiendo reconstruir el historial de cada suscripción.
--
-- Registra información como:
-- - Cambios de plan
-- - Cambios de período de facturación
-- - Renovaciones
-- - Cancelaciones
-- - Suspensiones y reactivaciones
-- ========================================
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
-- FIN DEL SCRIPT DE CREACIÓN DE LA BASE DE DATOS
-- ============================================================================
-- Con esta estructura, el sistema CUBIC queda preparado para funcionar como
-- una plataforma SaaS completa para la gestión de empresas dedicadas a
-- eventos y representación artística.
--
-- El diseño multi-tenant asegura que cada empresa tenga sus datos aislados,
-- mientras que el sistema de planes y suscripciones permite monetizar el
-- servicio con diferentes niveles y períodos de facturación.
--
-- La próxima fase debería incluir la creación de procedimientos almacenados,
-- vistas y disparadores para manejar la lógica de negocio más compleja.
-- ============================================================================