<?php

class DashboardController {
    
    public function __construct() {
        // La verificación de autenticación ahora se hace en index.php
        // por lo que este constructor puede simplificarse
    }
    
    public function index() {
        // Contenido del dashboard
        echo "<div class='container-fluid'>";
        echo "<div class='row'>";
        echo "<div class='col-md-12'>";
        echo "<div class='white-box'>";
        echo "<h3>Bienvenido al Panel de Administración</h3>";
        echo "<p>Gestiona tu plataforma CUBIC desde aquí.</p>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }
    
    public function welcome() {
        // Contenido de bienvenida personalizado
        echo "<div class='container-fluid'>";
        echo "<div class='row'>";
        echo "<div class='col-md-12'>";
        echo "<div class='white-box'>";
        
        // Mensaje de bienvenida personalizado
        echo "<div class='alert alert-success'>";
        echo "<h4><i class='fa fa-check-circle'></i> ¡Inicio de sesión exitoso!</h4>";
        echo "<p>Bienvenido/a, " . $_SESSION['admin']->nombre . " " . $_SESSION['admin']->apellido . ".</p>";
        echo "<p>Has accedido al panel de administración de CUBIC Cloud. Desde aquí podrás gestionar:</p>";
        echo "<ul>";
        echo "<li>Empresas y sus recursos</li>";
        echo "<li>Planes y suscripciones</li>";
        echo "<li>Usuarios y permisos</li>";
        echo "<li>Configuración del sistema</li>";
        echo "</ul>";
        echo "<p>Tu último acceso fue: " . ($_SESSION['admin']->ultimo_login ? date('d/m/Y H:i', strtotime($_SESSION['admin']->ultimo_login)) : 'Este es tu primer acceso') . "</p>";
        echo "</div>";
        
        // Resumen de estadísticas (esto sería dinámico en una implementación real)
        echo "<h3>Resumen del Sistema</h3>";
        echo "<div class='row'>";
        
        // Empresas activas
        echo "<div class='col-lg-3 col-md-6 col-sm-6 col-xs-12'>";
        echo "<div class='white-box analytics-info'>";
        echo "<h3 class='box-title'>Empresas Activas</h3>";
        echo "<ul class='list-inline two-part'>";
        echo "<li><i class='fa fa-building text-success'></i></li>";
        echo "<li class='text-right'><span class='counter text-success'>15</span></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        
        // Usuarios Totales
        echo "<div class='col-lg-3 col-md-6 col-sm-6 col-xs-12'>";
        echo "<div class='white-box analytics-info'>";
        echo "<h3 class='box-title'>Usuarios Totales</h3>";
        echo "<ul class='list-inline two-part'>";
        echo "<li><i class='fa fa-users text-info'></i></li>";
        echo "<li class='text-right'><span class='counter text-info'>54</span></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        
        // Eventos Activos
        echo "<div class='col-lg-3 col-md-6 col-sm-6 col-xs-12'>";
        echo "<div class='white-box analytics-info'>";
        echo "<h3 class='box-title'>Eventos Activos</h3>";
        echo "<ul class='list-inline two-part'>";
        echo "<li><i class='fa fa-calendar text-purple'></i></li>";
        echo "<li class='text-right'><span class='counter text-purple'>32</span></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        
        // Ingresos Mensuales
        echo "<div class='col-lg-3 col-md-6 col-sm-6 col-xs-12'>";
        echo "<div class='white-box analytics-info'>";
        echo "<h3 class='box-title'>Ingresos Mensuales</h3>";
        echo "<ul class='list-inline two-part'>";
        echo "<li><i class='fa fa-money text-danger'></i></li>";
        echo "<li class='text-right'><span class='counter text-danger'>$4,500</span></li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>"; // Fin row
        
        echo "</div>"; // Fin white-box
        echo "</div>"; // Fin col-md-12
        echo "</div>"; // Fin row
        echo "</div>"; // Fin container-fluid
    }
}