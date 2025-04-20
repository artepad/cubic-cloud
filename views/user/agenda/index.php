<?php
// Verificar que el usuario esté autenticado
if (!isUserLoggedIn()) {
    redirectTo('user/login');
}

// Formatear estados para los badges
$estadoClases = [
    'Propuesta' => 'label-warning',
    'Confirmado' => 'label-success',
    'Finalizado' => 'label-primary',
    'Reagendado' => 'label-info',
    'Solicitado' => 'label-default',
    'Cancelado' => 'label-danger'
];

// Inicializar contadores para el resumen
$totalEventos = 0;
$totalPropuestas = isset($contadores['Propuesta']) ? $contadores['Propuesta'] : 0;
$totalConfirmados = isset($contadores['Confirmado']) ? $contadores['Confirmado'] : 0;
$totalFinalizados = isset($contadores['Finalizado']) ? $contadores['Finalizado'] : 0;
$totalCancelados = isset($contadores['Cancelado']) ? $contadores['Cancelado'] : 0;

// Calcular total general
foreach ($contadores as $total) {
    $totalEventos += $total;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Agenda de Eventos</h3>
            <p class="text-muted">Consulta y administra todos los eventos de tu empresa</p>

            <!-- Mensajes de éxito y error -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['success_message'] ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['error_message'] ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Resumen de eventos -->
            <div class="row m-b-20">
                <div class="col-md-3 col-sm-6">
                    <div class="white-box bg-primary">
                        <div class="r-icon-stats">
                            <i class="fa fa-calendar bg-primary"></i>
                            <div class="bodystate">
                                <h4><?= $totalEventos ?></h4>
                                <span class="text-muted">Total Eventos</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="white-box bg-success">
                        <div class="r-icon-stats">
                            <i class="fa fa-check-circle-o bg-success"></i>
                            <div class="bodystate">
                                <h4><?= $totalConfirmados ?></h4>
                                <span class="text-muted">Confirmados</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="white-box bg-warning">
                        <div class="r-icon-stats">
                            <i class="fa fa-clock-o bg-warning"></i>
                            <div class="bodystate">
                                <h4><?= $totalPropuestas ?></h4>
                                <span class="text-muted">Propuestas</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="white-box bg-danger">
                        <div class="r-icon-stats">
                            <i class="fa fa-ban bg-danger"></i>
                            <div class="bodystate">
                                <h4><?= $totalCancelados ?></h4>
                                <span class="text-muted">Cancelados</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros y botones de acción -->
            <div class="row m-b-20">
                <div class="col-md-8">
                    <form action="<?= base_url ?>agenda/index" method="GET" class="form-inline">
                        <div class="form-group m-r-10">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="Propuesta" <?= isset($_GET['estado']) && $_GET['estado'] == 'Propuesta' ? 'selected' : '' ?>>Propuesta</option>
                                <option value="Confirmado" <?= isset($_GET['estado']) && $_GET['estado'] == 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                <option value="Finalizado" <?= isset($_GET['estado']) && $_GET['estado'] == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                <option value="Reagendado" <?= isset($_GET['estado']) && $_GET['estado'] == 'Reagendado' ? 'selected' : '' ?>>Reagendado</option>
                                <option value="Solicitado" <?= isset($_GET['estado']) && $_GET['estado'] == 'Solicitado' ? 'selected' : '' ?>>Solicitado</option>
                                <option value="Cancelado" <?= isset($_GET['estado']) && $_GET['estado'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group m-r-10">
                            <input type="date" name="fecha_inicio" class="form-control" placeholder="Fecha inicio" 
                                   value="<?= isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '' ?>">
                        </div>
                        <div class="form-group m-r-10">
                            <input type="date" name="fecha_fin" class="form-control" placeholder="Fecha fin" 
                                   value="<?= isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '' ?>">
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light">
                            <i class="fa fa-filter"></i> Filtrar
                        </button>
                        <a href="<?= base_url ?>agenda/index" class="btn btn-default waves-effect waves-light m-l-5">
                            <i class="fa fa-refresh"></i> Limpiar
                        </a>
                    </form>
                </div>
                <div class="col-md-4 text-right">
                    <a href="<?= base_url ?>agenda/calendario" class="btn btn-primary waves-effect waves-light m-r-5">
                        <i class="fa fa-calendar"></i> Ver Calendario
                    </a>
                    <a href="<?= base_url ?>agenda/exportar<?= isset($_GET['estado']) || isset($_GET['fecha_inicio']) || isset($_GET['fecha_fin']) ? '?' . http_build_query($_GET) : '' ?>" 
                       class="btn btn-success waves-effect waves-light">
                        <i class="fa fa-file-excel-o"></i> Exportar
                    </a>
                </div>
            </div>

            <!-- Tabla de eventos -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre evento</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Artista</th>
                            <th>Ciudad</th>
                            <th>Valor</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($eventos)): ?>
                            <?php foreach ($eventos as $evento): ?>
                                <tr>
                                    <td><?= $evento->id ?></td>
                                    <td><?= htmlspecialchars($evento->nombre_evento) ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($evento->fecha_evento)) ?>
                                        <?= $evento->hora_evento ? '<br><small>' . date('H:i', strtotime($evento->hora_evento)) . 'hrs</small>' : '' ?>
                                    </td>
                                    <td>
                                        <?php if (isset($evento->cliente_nombre)): ?>
                                            <?= htmlspecialchars($evento->cliente_nombre . ' ' . $evento->cliente_apellido) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin cliente</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($evento->artista_nombre)): ?>
                                            <?= htmlspecialchars($evento->artista_nombre) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Sin artista</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($evento->ciudad_evento ?: 'N/A') ?></td>
                                    <td>
                                        <?php if ($evento->valor_evento): ?>
                                            <strong>$<?= number_format($evento->valor_evento, 0, ',', '.') ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">No definido</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="label <?= isset($estadoClases[$evento->estado_evento]) ? $estadoClases[$evento->estado_evento] : 'label-default' ?>">
                                            <?= $evento->estado_evento ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>agenda/ver/<?= $evento->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="icon-eye"></i>
                                        </a>
                                        
                                        <?php if ($evento->estado_evento == 'Propuesta' || $evento->estado_evento == 'Solicitado'): ?>
                                            <a href="<?= base_url ?>evento/confirmar/<?= $evento->id ?>" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Confirmar">
                                                <i class="icon-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($evento->estado_evento != 'Finalizado' && $evento->estado_evento != 'Cancelado'): ?>
                                            <a href="<?= base_url ?>evento/editar/<?= $evento->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                                <i class="icon-pencil"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($evento->estado_evento != 'Cancelado'): ?>
                                            <a href="javascript:void(0);" onclick="cambiarEstado(<?= $evento->id ?>, 'Cancelado')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                                <i class="icon-ban"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay eventos para mostrar</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación (para futura implementación) -->
            <!--
            <div class="text-center m-t-20">
                <ul class="pagination">
                    <li class="disabled"><a href="#"><i class="fa fa-angle-left"></i></a></li>
                    <li class="active"><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                </ul>
            </div>
            -->
        </div>
    </div>
</div>

<script>
    // Asegurar que los tooltips se inicialicen correctamente
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    // Función para cambiar el estado de un evento
    function cambiarEstado(eventoId, nuevoEstado) {
        let confirmMessage = '¿Está seguro que desea cambiar el estado del evento a ' + nuevoEstado + '?';
        
        if (nuevoEstado === 'Cancelado') {
            confirmMessage = '¿Está seguro que desea cancelar este evento? Esta acción puede afectar a los participantes.';
        }
        
        if (confirm(confirmMessage)) {
            window.location.href = '<?= base_url ?>evento/cambiarEstado/' + eventoId + '/' + nuevoEstado;
        }
    }
</script>