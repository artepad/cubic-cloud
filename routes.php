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
Router::add('admin/dashboard', 'AdminController', 'dashboard', 'auth');
Router::add('admin/welcome', 'AdminController', 'welcome', 'auth');
Router::add('admin/empresas', 'AdminController', 'empresas', 'auth');
Router::add('admin/usuarios', 'AdminController', 'usuarios', 'auth');
Router::add('admin/planes', 'AdminController', 'planes', 'auth');
Router::add('admin/suscripciones', 'AdminController', 'suscripciones', 'auth');
Router::add('admin/configuracion', 'AdminController', 'configuracion', 'auth');
Router::add('admin/crearUsuario', 'AdminController', 'crearUsuario', 'auth');
Router::add('admin/saveUsuario', 'AdminController', 'saveUsuario', 'auth');