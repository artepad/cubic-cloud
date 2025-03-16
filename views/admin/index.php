<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Panel Principal</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="<?=base_url?>">Inicio</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- Cards de información -->
    <div class="row">
        <!-- Empresas activas -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center bg-info">
                            <i class="fa fa-building"></i>
                        </div>
                        <div class="ms-2 align-self-center">
                            <h3 class="mb-0">15</h3>
                            <h5 class="text-muted mb-0">Empresas Activas</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Usuarios Totales -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center bg-warning">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="ms-2 align-self-center">
                            <h3 class="mb-0">54</h3>
                            <h5 class="text-muted mb-0">Usuarios Totales</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Eventos Activos -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center bg-primary">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="ms-2 align-self-center">
                            <h3 class="mb-0">32</h3>
                            <h5 class="text-muted mb-0">Eventos Activos</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ingresos Mensuales -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div class="round round-lg align-self-center bg-success">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="ms-2 align-self-center">
                            <h3 class="mb-0">$4,500</h3>
                            <h5 class="text-muted mb-0">Ingresos Mensuales</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="row">
        <!-- Gráfico de actividad -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Actividad Reciente</h4>
                    <div class="d-flex no-block align-items-center mb-4">
                        <h6 class="card-subtitle">Eventos y suscripciones del último mes</h6>
                    </div>
                    <div id="main-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        
        <!-- Lista de empresas recientes -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Nuevas Empresas</h4>
                    <h6 class="card-subtitle">Últimas 5 empresas registradas</h6>
                    
                    <div class="mt-4">
                        <div class="d-flex no-block align-items-center mb-3">
                            <span class="btn btn-primary btn-circle d-flex align-items-center justify-content-center">
                                <i class="fa fa-building"></i>
                            </span>
                            <div class="ms-3">
                                <h5 class="mb-0">Eventos Santiago SPA</h5>
                                <span class="text-muted">Registrada: 12/03/2025</span>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success">Activa</span>
                            </div>
                        </div>
                        
                        <div class="d-flex no-block align-items-center mb-3">
                            <span class="btn btn-primary btn-circle d-flex align-items-center justify-content-center">
                                <i class="fa fa-building"></i>
                            </span>
                            <div class="ms-3">
                                <h5 class="mb-0">Productor Nacional Ltda.</h5>
                                <span class="text-muted">Registrada: 05/03/2025</span>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success">Activa</span>
                            </div>
                        </div>
                        
                        <div class="d-flex no-block align-items-center mb-3">
                            <span class="btn btn-primary btn-circle d-flex align-items-center justify-content-center">
                                <i class="fa fa-building"></i>
                            </span>
                            <div class="ms-3">
                                <h5 class="mb-0">Música en Vivo SPA</h5>
                                <span class="text-muted">Registrada: 28/02/2025</span>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-warning">Pendiente</span>
                            </div>
                        </div>
                        
                        <div class="d-flex no-block align-items-center mb-3">
                            <span class="btn btn-primary btn-circle d-flex align-items-center justify-content-center">
                                <i class="fa fa-building"></i>
                            </span>
                            <div class="ms-3">
                                <h5 class="mb-0">Representante Sur SPA</h5>
                                <span class="text-muted">Registrada: 20/02/2025</span>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success">Activa</span>
                            </div>
                        </div>
                        
                        <div class="d-flex no-block align-items-center">
                            <span class="btn btn-primary btn-circle d-flex align-items-center justify-content-center">
                                <i class="fa fa-building"></i>
                            </span>
                            <div class="ms-3">
                                <h5 class="mb-0">Eventos Corporativos SA</h5>
                                <span class="text-muted">Registrada: 15/02/2025</span>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success">Activa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Más contenido para el dashboard -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Actividad Reciente del Sistema</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Acción</th>
                                    <th>Usuario</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Nuevo usuario creado</td>
                                    <td>admin@sistema.com</td>
                                    <td>16/03/2025 10:25</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Plan actualizado</td>
                                    <td>admin@sistema.com</td>
                                    <td>16/03/2025 09:15</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Nueva empresa registrada</td>
                                    <td>ventas@empresa.com</td>
                                    <td>15/03/2025 16:30</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Inicio de sesión</td>
                                    <td>admin@sistema.com</td>
                                    <td>15/03/2025 10:05</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Recuperación de contraseña</td>
                                    <td>usuario@empresa.com</td>
                                    <td>14/03/2025 14:20</td>
                                    <td><span class="badge bg-success">Completado</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para el gráfico principal (usando un CDN de Chart.js) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Código para inicializar el gráfico
    // Nota: En producción, sería mejor cargar estos datos dinámicamente
    // desde el controlador y pasarlos como variables PHP a la vista
});
</script>