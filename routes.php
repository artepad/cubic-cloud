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

Router::add('admin/usuarios', 'AdminController', 'usuarios', 'auth');
Router::add('admin/crearUsuario', 'AdminController', 'crearUsuario', 'auth');
Router::add('admin/saveUsuario', 'AdminController', 'saveUsuario', 'auth');
Router::add('admin/configuracion', 'AdminController', 'configuracion', 'auth'); // crear logica y borrar 




// Alias para funciones de empresa desde AdminController
Router::add('admin/crearEmpresa', 'EmpresaController', 'crear', 'auth');
Router::add('admin/saveEmpresa', 'EmpresaController', 'save', 'auth');
Router::add('admin/editarEmpresa', 'EmpresaController', 'editar', 'auth');
Router::add('admin/updateEmpresa', 'EmpresaController', 'update', 'auth');
Router::add('admin/eliminarEmpresa', 'EmpresaController', 'delete', 'auth');
Router::add('admin/cambiarEstadoEmpresa', 'EmpresaController', 'cambiarEstado', 'auth');
Router::add('admin/verEmpresa', 'EmpresaController', 'ver', 'auth');



// Rutas para el controlador de Planes
Router::add('plan/index', 'PlanController', 'index', 'auth');
Router::add('plan/crear', 'PlanController', 'crear', 'auth');
Router::add('plan/save', 'PlanController', 'save', 'auth');
Router::add('plan/editar', 'PlanController', 'editar', 'auth');
Router::add('plan/update', 'PlanController', 'update', 'auth');
Router::add('plan/delete', 'PlanController', 'delete', 'auth');
Router::add('plan/cambiarEstado', 'PlanController', 'cambiarEstado', 'auth');
Router::add('plan/cambiarVisibilidad', 'PlanController', 'cambiarVisibilidad', 'auth');

// Rutas para el controlador de Empresas
Router::add('empresa/index', 'EmpresaController', 'index', 'auth');
Router::add('empresa/crear', 'EmpresaController', 'crear', 'auth');
Router::add('empresa/save', 'EmpresaController', 'save', 'auth');
Router::add('empresa/editar', 'EmpresaController', 'editar', 'auth');
Router::add('empresa/update', 'EmpresaController', 'update', 'auth');
Router::add('empresa/delete', 'EmpresaController', 'delete', 'auth');
Router::add('empresa/cambiarEstado', 'EmpresaController', 'cambiarEstado', 'auth');
Router::add('empresa/ver', 'EmpresaController', 'ver', 'auth');


// Rutas para Suscripciones
Router::add('suscripciones/index', 'EmpresaController', 'index', 'auth');