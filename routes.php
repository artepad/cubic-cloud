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
Router::add('usuario/editar/:id', 'UsuarioController', 'editar', 'auth');
Router::add('usuario/actualizar', 'UsuarioController', 'actualizar', 'auth');
Router::add('usuario/eliminar/:id', 'UsuarioController', 'eliminar', 'auth');
Router::add('usuario/cambiarEstado/:id/:estado', 'UsuarioController', 'cambiarEstado', 'auth');
Router::add('usuario/ver/:id', 'UsuarioController', 'ver', 'auth');

// Rutas para el controlador de Empresas
Router::add('empresa/index', 'EmpresaController', 'index', 'auth');
Router::add('empresa/crear', 'EmpresaController', 'crear', 'auth');
Router::add('empresa/guardar', 'EmpresaController', 'guardar', 'auth');
Router::add('empresa/editar/:id', 'EmpresaController', 'editar', 'auth');
Router::add('empresa/actualizar', 'EmpresaController', 'actualizar', 'auth');
Router::add('empresa/eliminar/:id', 'EmpresaController', 'eliminar', 'auth');
Router::add('empresa/cambiarEstado/:id/:estado', 'EmpresaController', 'cambiarEstado', 'auth');
Router::add('empresa/ver/:id', 'EmpresaController', 'ver', 'auth');

// Rutas para el controlador de Planes
Router::add('plan/index', 'PlanController', 'index', 'auth');
Router::add('plan/crear', 'PlanController', 'crear', 'auth');
Router::add('plan/guardar', 'PlanController', 'guardar', 'auth');     
Router::add('plan/editar/:id', 'PlanController', 'editar', 'auth');
Router::add('plan/actualizar', 'PlanController', 'actualizar', 'auth'); 
Router::add('plan/eliminar/:id', 'PlanController', 'eliminar', 'auth');
Router::add('plan/cambiarEstado/:id/:estado', 'PlanController', 'cambiarEstado', 'auth');
Router::add('plan/cambiarVisibilidad/:id/:visibilidad', 'PlanController', 'cambiarVisibilidad', 'auth');
Router::add('plan/ver/:id', 'PlanController', 'ver', 'auth');

// Rutas para el controlador de Suscripciones
Router::add('suscripcion/index', 'SuscripcionController', 'index', 'auth');
Router::add('suscripcion/crear', 'SuscripcionController', 'crear', 'auth');
Router::add('suscripcion/guardar', 'SuscripcionController', 'guardar', 'auth');
Router::add('suscripcion/editar/:id', 'SuscripcionController', 'editar', 'auth');
Router::add('suscripcion/actualizar', 'SuscripcionController', 'actualizar', 'auth');
Router::add('suscripcion/cambiarEstado/:id/:estado', 'SuscripcionController', 'cambiarEstado', 'auth');
Router::add('suscripcion/renovar/:id', 'SuscripcionController', 'renovar', 'auth');
Router::add('suscripcion/historial/:id', 'SuscripcionController', 'historial', 'auth');
Router::add('suscripcion/generarFactura/:id', 'SuscripcionController', 'generarFactura', 'auth');
Router::add('suscripcion/verFactura/:id', 'SuscripcionController', 'verFactura', 'auth');
Router::add('suscripcion/ver/:id', 'SuscripcionController', 'ver', 'auth');


// Rutas para UserController (login y operaciones de usuario)
Router::add('user', 'UserController', 'index', 'guest');
Router::add('user/login', 'UserController', 'login', 'guest');
Router::add('user/validate', 'UserController', 'validate', 'guest');
Router::add('user/recover', 'UserController', 'recover', 'guest');
Router::add('user/requestReset', 'UserController', 'requestReset', 'guest');
Router::add('user/reset', 'UserController', 'reset', 'guest');
Router::add('user/doReset', 'UserController', 'doReset', 'guest');
Router::add('user/welcome', 'UserController', 'welcome', 'user_auth');
Router::add('user/logout', 'UserController', 'logout', 'user_auth');
Router::add('user/dashboard', 'UserController', 'dashboard', 'user_auth');
Router::add('user/profile', 'UserController', 'profile', 'user_auth');
Router::add('user/updateProfile', 'UserController', 'updateProfile', 'user_auth');
Router::add('user/changePassword', 'UserController', 'changePassword', 'user_auth');
Router::add('user/updatePassword', 'UserController', 'updatePassword', 'user_auth');