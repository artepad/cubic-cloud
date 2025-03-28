<?php
/**
 * Archivo de definición de rutas
 * 
 * Este archivo contiene todas las rutas de la aplicación
 * Formato: Router::add('ruta', 'controlador', 'accion', 'middleware');
 */

// Establecer ruta por defecto
Router::setDefault(controller_default, action_default);

// Rutas públicas (no requieren autenticación)
Router::add('', 'AdminController', 'index', 'guest');
Router::add('admin', 'AdminController', 'index', 'guest');
Router::add('admin/login', 'AdminController', 'login', 'guest');
Router::add('admin/validate', 'AdminController', 'validate', 'guest');
Router::add('admin/recover', 'AdminController', 'recover', 'guest');
Router::add('admin/requestReset', 'AdminController', 'requestReset', 'guest');
Router::add('admin/reset', 'AdminController', 'reset', 'guest');
Router::add('admin/doReset', 'AdminController', 'doReset', 'guest');
Router::add('error', 'ErrorController', 'index', 'public');

// Rutas protegidas (requieren autenticación)
Router::add('admin/logout', 'AdminController', 'logout', 'auth');
Router::add('systemDashboard', 'SystemDashboardController', 'index', 'auth');
Router::add('systemDashboard/index', 'SystemDashboardController', 'index', 'auth');
Router::add('systemDashboard/welcome', 'SystemDashboardController', 'welcome', 'auth');
Router::add('systemDashboard/empresas', 'SystemDashboardController', 'empresas', 'auth');
Router::add('systemDashboard/usuarios', 'SystemDashboardController', 'usuarios', 'auth');
Router::add('systemDashboard/planes', 'SystemDashboardController', 'planes', 'auth');
Router::add('systemDashboard/suscripciones', 'SystemDashboardController', 'suscripciones', 'auth');
Router::add('systemDashboard/configuracion', 'SystemDashboardController', 'configuracion', 'auth');
Router::add('systemDashboard/crearUsuario', 'SystemDashboardController', 'crearUsuario', 'auth');
Router::add('systemDashboard/redirectAfterSave', 'SystemDashboardController', 'redirectAfterSave', 'auth');

// Rutas para la gestión de usuarios
Router::add('systemDashboard/crearUsuario', 'SystemDashboardController', 'crearUsuario', 'auth');
Router::add('systemDashboard/saveUsuario', 'SystemDashboardController', 'saveUsuario', 'auth');