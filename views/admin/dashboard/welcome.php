<!-- Mensaje de bienvenida personalizado con estilo mejorado -->
<div class="row">
    <div class="col-md-12">
        <div class="white-box welcome-container">
            <div class="welcome-header">
                <div class="welcome-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="welcome-title">
                    <h2>¡Bienvenido de nuevo!</h2>
                    <h3><?= $admin->nombre ?> <?= $admin->apellido ?></h3>
                </div>
            </div>
            
            <div class="welcome-content">
                <p class="lead">Has accedido al panel de administración de CUBIC Cloud.</p>
                
                <div class="welcome-features">
                    <div class="feature-item">
                        <i class="fa fa-building"></i>
                        <span>Gestión de empresas y recursos</span>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-credit-card"></i>
                        <span>Planes y suscripciones</span>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-users"></i>
                        <span>Usuarios y permisos</span>
                    </div>
                    <div class="feature-item">
                        <i class="fa fa-cogs"></i>
                        <span>Configuración del sistema</span>
                    </div>
                </div>
                
                <div class="welcome-footer">
                    <div class="last-login">
                        <i class="fa fa-clock-o"></i> Último acceso: <strong><?= $ultimo_login ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumen de estadísticas -->
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Resumen del Sistema</h3>
            <div class="row m-t-30">
                <!-- Empresas activas -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="stats-card">
                        <div class="stats-icon bg-primary">
                            <i class="fa fa-building"></i>
                        </div>
                        <div class="stats-info">
                            <h3 class="counter"><?= $empresas_count ?></h3>
                            <p>Empresas Activas</p>
                        </div>
                    </div>
                </div>
                
                <!-- Usuarios Totales -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="stats-card">
                        <div class="stats-icon bg-info">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="stats-info">
                            <h3 class="counter"><?= $usuarios_count ?></h3>
                            <p>Usuarios Totales</p>
                        </div>
                    </div>
                </div>
                
                <!-- Eventos Activos -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="stats-card">
                        <div class="stats-icon bg-warning">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <div class="stats-info">
                            <h3 class="counter"><?= $eventos_count ?></h3>
                            <p>Eventos Activos</p>
                        </div>
                    </div>
                </div>
                
                <!-- Ingresos Mensuales -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="stats-card">
                        <div class="stats-icon bg-success">
                            <i class="fa fa-money"></i>
                        </div>
                        <div class="stats-info">
                            <h3><?= $ingresos ?></h3>
                            <p>Ingresos Mensuales</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para el bloque de bienvenida */
.welcome-container {
    border: none;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    background: #28a745; /* Color verde sólido en lugar del degradado */
    padding: 0;
    margin-bottom: 30px;
    position: relative;
    color: white;
}

.welcome-header {
    display: flex;
    align-items: center;
    padding: 25px 30px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.welcome-icon {
    font-size: 56px;
    margin-right: 20px;
    color: rgba(255, 255, 255, 0.9);
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.welcome-title {
    flex: 1;
}

.welcome-title h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 300;
    letter-spacing: -0.5px;
    color: white !important;
}

.welcome-title h3 {
    margin: 5px 0 0;
    font-size: 24px;
    font-weight: 600;
    color: white !important;
}

.welcome-content {
    padding: 25px 30px;
}

.welcome-content .lead {
    font-size: 18px;
    margin-bottom: 25px;
    font-weight: 300;
}

.welcome-features {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px 20px;
}

.feature-item {
    flex: 1 0 50%;
    padding: 10px;
    display: flex;
    align-items: center;
    font-size: 16px;
}

.feature-item i {
    font-size: 20px;
    margin-right: 12px;
    background: rgba(255, 255, 255, 0.15);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 15px;
    font-size: 14px;
    display: flex;
    align-items: center;
}

.last-login {
    color: white !important;
}

.last-login i {
    margin-right: 5px;
}

.last-login strong {
    color: white !important;
}

/* Estilos para las tarjetas de estadísticas */
.stats-card {
    position: relative;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 20px;
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin-right: 15px;
}

.bg-primary {
    background-color: #007bff;
}

.bg-info {
    background-color: #17a2b8;
}

.bg-warning {
    background-color: #ffc107;
}

.bg-success {
    background-color: #2ecc71;
}

.stats-info {
    flex: 1;
}

.stats-info h3 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.stats-info p {
    margin: 5px 0 0;
    color: #6c757d;
    font-size: 14px;
}

@media (max-width: 767px) {
    .feature-item {
        flex: 1 0 100%;
    }
}
</style>

<script>
// Código para inicializar los contadores
document.addEventListener('DOMContentLoaded', function() {
    // Si existe jQuery y la función counterUp
    if (typeof jQuery !== 'undefined' && jQuery.fn.counterUp) {
        jQuery('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    }
});
</script>