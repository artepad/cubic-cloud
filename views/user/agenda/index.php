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

            <!-- Botón para crear nuevo evento -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>evento/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Evento
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