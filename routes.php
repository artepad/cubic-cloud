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

// Rutas del AdminController
Router::add('admin/logout', 'AdminController', 'logout', 'auth');
Router::add('admin/dashboard', 'AdminController', 'dashboard', 'auth');
Router::add('admin/welcome', 'AdminController', 'welcome', 'auth');
Router::add('admin/configuracion', 'AdminController', 'configuracion', 'auth');

// Rutas del UsuarioController
Router::add('usuario/index', 'UsuarioController', 'index', 'auth');
Router::add('usuario/crear', 'UsuarioController', 'crear', 'auth');
Router::add('usuario/guardar', 'UsuarioController', 'guardar', 'auth');
Router::add('usuario/editar', 'UsuarioController', 'editar', 'auth');
Router::add('usuario/actualizar', 'UsuarioController', 'actualizar', 'auth');
Router::add('usuario/eliminar', 'UsuarioController', 'eliminar', 'auth');
Router::add('usuario/cambiarEstado', 'UsuarioController', 'cambiarEstado', 'auth');
Router::add('usuario/ver/:id', 'UsuarioController', 'ver', 'auth');

// Rutas para el controlador de Empresas
Router::add('empresa/index', 'EmpresaController', 'index', 'auth');
Router::add('empresa/crear', 'EmpresaController', 'crear', 'auth');
Router::add('empresa/save', 'EmpresaController', 'save', 'auth');
Router::add('empresa/editar', 'EmpresaController', 'editar', 'auth');
Router::add('empresa/update', 'EmpresaController', 'update', 'auth');
Router::add('empresa/delete', 'EmpresaController', 'delete', 'auth');
Router::add('empresa/cambiarEstado', 'EmpresaController', 'cambiarEstado', 'auth');
Router::add('empresa/ver', 'EmpresaController', 'ver', 'auth');

// Rutas para el controlador de Planes
Router::add('plan/index', 'PlanController', 'index', 'auth');
Router::add('plan/crear', 'PlanController', 'crear', 'auth');
Router::add('plan/save', 'PlanController', 'save', 'auth');
Router::add('plan/editar', 'PlanController', 'editar', 'auth');
Router::add('plan/update', 'PlanController', 'update', 'auth');
Router::add('plan/delete', 'PlanController', 'delete', 'auth');
Router::add('plan/cambiarEstado', 'PlanController', 'cambiarEstado', 'auth');
Router::add('plan/cambiarVisibilidad', 'PlanController', 'cambiarVisibilidad', 'auth');

// Rutas para el controlador de Suscripciones
Router::add('suscripcion/index', 'SuscripcionController', 'index', 'auth');
Router::add('suscripcion/crear', 'SuscripcionController', 'crear', 'auth');
Router::add('suscripcion/guardar', 'SuscripcionController', 'guardar', 'auth');
Router::add('suscripcion/editar', 'SuscripcionController', 'editar', 'auth');
Router::add('suscripcion/actualizar', 'SuscripcionController', 'actualizar', 'auth');
Router::add('suscripcion/cambiarEstado', 'SuscripcionController', 'cambiarEstado', 'auth');
Router::add('suscripcion/renovar', 'SuscripcionController', 'renovar', 'auth');
Router::add('suscripcion/historial', 'SuscripcionController', 'historial', 'auth');
Router::add('suscripcion/generarFactura', 'SuscripcionController', 'generarFactura', 'auth');
Router::add('suscripcion/verFactura', 'SuscripcionController', 'verFactura', 'auth');
