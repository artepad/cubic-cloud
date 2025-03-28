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

<!-- Gráfico de actividad -->
<div class="row">
    <div class="col-md-8">
        <div class="white-box">
            <h3 class="box-title">Actividad Reciente</h3>
            <div class="row">
                <div class="col-md-12">
                    <div id="activityChart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="white-box">
            <h3 class="box-title">Distribución de Planes</h3>
            <div id="planDistribution" style="height: 300px;"></div>
        </div>
    </div>
</div>

<!-- Últimas empresas registradas y alertas del sistema -->
<div class="row">
    <div class="col-md-6">
        <div class="white-box">
            <h3 class="box-title">Últimas Empresas Registradas</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Plan</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Eventos Santiago SPA</td>
                            <td>Profesional</td>
                            <td>12/03/2025</td>
                            <td><span class="label label-success">Activa</span></td>
                        </tr>
                        <tr>
                            <td>Productor Nacional Ltda.</td>
                            <td>Premium</td>
                            <td>05/03/2025</td>
                            <td><span class="label label-success">Activa</span></td>
                        </tr>
                        <tr>
                            <td>Música en Vivo SPA</td>
                            <td>Básico</td>
                            <td>28/02/2025</td>
                            <td><span class="label label-warning">Pendiente</span></td>
                        </tr>
                        <tr>
                            <td>Representante Sur SPA</td>
                            <td>Profesional</td>
                            <td>20/02/2025</td>
                            <td><span class="label label-success">Activa</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="white-box">
            <h3 class="box-title">Alertas del Sistema</h3>
            <div class="message-center">
                <a href="#">
                    <div class="mail-contnet">
                        <h5><span class="label label-danger">Crítico</span> Suscripción próxima a vencer</h5>
                        <span class="mail-desc">La empresa "Eventos Norte" tiene suscripción que vence en 3 días</span>
                        <span class="time">16/03/2025 09:30</span>
                    </div>
                </a>
                <a href="#">
                    <div class="mail-contnet">
                        <h5><span class="label label-warning">Advertencia</span> Almacenamiento</h5>
                        <span class="mail-desc">3 empresas están cerca del límite de almacenamiento</span>
                        <span class="time">15/03/2025 14:45</span>
                    </div>
                </a>
                <a href="#">
                    <div class="mail-contnet">
                        <h5><span class="label label-info">Información</span> Nuevos registros</h5>
                        <span class="mail-desc">5 nuevos usuarios se registraron en la última semana</span>
                        <span class="time">14/03/2025 10:15</span>
                    </div>
                </a>
                <a href="#">
                    <div class="mail-contnet">
                        <h5><span class="label label-success">Completado</span> Actualización del sistema</h5>
                        <span class="mail-desc">La actualización programada se completó con éxito</span>
                        <span class="time">13/03/2025 08:20</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Script para inicializar los gráficos cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar contadores si existe la función
    if (typeof jQuery !== 'undefined' && jQuery.fn.counterUp) {
        jQuery('.counter').counterUp({
            delay: 10,
            time: 1000
        });
    }
    
    // Si Chartist está disponible, configuramos los gráficos
    if (typeof Chartist !== 'undefined') {
        // Gráfico de actividad
        new Chartist.Line('#activityChart', {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            series: [
                [15, 18, 14, 20, 22, 15],  // Empresas
                [25, 30, 28, 35, 40, 48]   // Usuarios
            ]
        }, {
            fullWidth: true,
            chartPadding: {
                right: 40
            },
            plugins: [
                Chartist.plugins.tooltip()
            ]
        });
        
        // Gráfico de distribución de planes
        new Chartist.Pie('#planDistribution', {
            series: [30, 45, 25],
            labels: ['Básico', 'Profesional', 'Premium']
        }, {
            donut: true,
            donutWidth: 60,
            donutSolid: true,
            startAngle: 270,
            showLabel: true,
            plugins: [
                Chartist.plugins.tooltip()
            ]
        });
    }
});
</script>