<!-- Mensaje de bienvenida personalizado -->
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="alert alert-success">
                <i class="icon-check"></i> ¡Inicio de sesión exitoso!
                <p class="m-t-10">Bienvenido/a, <?= $admin->nombre ?> <?= $admin->apellido ?>.</p>
                <p>Has accedido al panel de administración de CUBIC Cloud. Desde aquí podrás gestionar:</p>
                <ul class="list-icons">
                    <li><i class="icon-arrow-right-circle text-success"></i> Empresas y sus recursos</li>
                    <li><i class="icon-arrow-right-circle text-success"></i> Planes y suscripciones</li>
                    <li><i class="icon-arrow-right-circle text-success"></i> Usuarios y permisos</li>
                    <li><i class="icon-arrow-right-circle text-success"></i> Configuración del sistema</li>
                </ul>
                <p>Tu último acceso fue: <?= $ultimo_login ?></p>
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
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Empresas Activas</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash"><canvas width="67" height="30" style="display: inline-block; width: 67px; height: 30px; vertical-align: top;"></canvas></div>
                            </li>
                            <li class="text-right"><i class="icon-arrow-up-circle text-success"></i> <span class="counter text-success"><?= $empresas_count ?></span></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Usuarios Totales -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Usuarios Totales</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash2"><canvas width="67" height="30" style="display: inline-block; width: 67px; height: 30px; vertical-align: top;"></canvas></div>
                            </li>
                            <li class="text-right"><i class="icon-arrow-up-circle text-info"></i> <span class="counter text-info"><?= $usuarios_count ?></span></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Eventos Activos -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Eventos Activos</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash3"><canvas width="67" height="30" style="display: inline-block; width: 67px; height: 30px; vertical-align: top;"></canvas></div>
                            </li>
                            <li class="text-right"><i class="icon-arrow-up-circle text-purple"></i> <span class="counter text-purple"><?= $eventos_count ?></span></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Ingresos Mensuales -->
                <div class="col-lg-3 col-sm-6 col-xs-12">
                    <div class="white-box analytics-info">
                        <h3 class="box-title">Ingresos Mensuales</h3>
                        <ul class="list-inline two-part">
                            <li>
                                <div id="sparklinedash4"><canvas width="67" height="30" style="display: inline-block; width: 67px; height: 30px; vertical-align: top;"></canvas></div>
                            </li>
                            <li class="text-right"><i class="icon-arrow-up-circle text-danger"></i> <span class="counter text-danger"><?= $ingresos ?></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Acciones rápidas -->
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Acciones Rápidas</h3>
            <div class="row m-t-20">
                <div class="col-md-3 col-sm-6 text-center">
                    <a href="<?= base_url ?>systemDashboard/empresas" class="btn btn-info btn-block waves-effect waves-light m-b-10">
                        <i class="icon-building font-20 m-r-5"></i> Gestionar Empresas
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <a href="<?= base_url ?>systemDashboard/usuarios" class="btn btn-primary btn-block waves-effect waves-light m-b-10">
                        <i class="icon-user font-20 m-r-5"></i> Gestionar Usuarios
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <a href="<?= base_url ?>systemDashboard/planes" class="btn btn-success btn-block waves-effect waves-light m-b-10">
                        <i class="icon-list font-20 m-r-5"></i> Gestionar Planes
                    </a>
                </div>
                <div class="col-md-3 col-sm-6 text-center">
                    <a href="<?= base_url ?>systemDashboard/configuracion" class="btn btn-default btn-block waves-effect waves-light m-b-10">
                        <i class="icon-settings font-20 m-r-5"></i> Configuración
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recordatorios o información importante -->
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="alert alert-warning">
                <i class="icon-info"></i> <strong>Recordatorio:</strong> 
                Verifica regularmente las cuentas próximas a vencer y los reportes de actividad para mantener el sistema optimizado.
            </div>
        </div>
    </div>
</div>

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