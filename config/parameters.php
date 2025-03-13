<?php
/**
 * Configuración global de la aplicación
 * 
 * Este archivo contiene las constantes y parámetros de configuración
 * principales que se utilizan en toda la aplicación
 */

// Detectar automáticamente si estamos en HTTP o HTTPS
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$subdirectory = '/cubic-cloud/';

// URL base de la aplicación
define("base_url", $protocol . $host . $subdirectory);

// Controlador y acción por defecto
define("controller_default", "DashboardController");
define("action_default", "index");

// Configuración de seguridad
define("SESSION_TIMEOUT", 3600); // Tiempo de inactividad en segundos (1 hora)
define("COOKIE_LIFETIME", 30); // Duración de cookies "Recuérdame" en días

// Configuración de correo electrónico
define("EMAIL_FROM", "noreply@cubiccloud.com");
define("EMAIL_SUBJECT_PREFIX", "Cubic Cloud - ");

// Configuración de entorno
define("ENVIRONMENT", "development"); // Opciones: development, testing, production

// Mostrar o no errores según el entorno
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}