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

// Asegurarse de que tenemos un evento válido
if (!isset($evento) || !$evento) {
    redirectTo('agenda/index');
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="box-title"><?= htmlspecialchars($evento->nombre_evento) ?></h3>
                    <p>
                        <span class="label <?= isset($estadoClases[$evento->estado_evento]) ? $estadoClases[$evento->estado_evento] : 'label-default' ?>">
                            <?= $evento->estado_evento ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <a href="<?= base_url ?>agenda/index" class="btn btn-default waves-effect waves-light">
                        <i class="fa fa-arrow-left"></i> Volver a la agenda
                    </a>
                    
                    <?php if ($evento->estado_evento != 'Finalizado' && $evento->estado_evento != 'Cancelado'): ?>
                        <a href="<?= base_url ?>evento/editar/<?= $evento->id ?>" class="btn btn-warning waves-effect waves-light">
                            <i class="fa fa-pencil"></i> Editar evento
                        </a>
                    <?php endif; ?>
                </div>
            </div>

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

            <!-- Detalles del evento en paneles -->
            <div class="row m-t-20">
                <!-- Información básica -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-info-circle"></i> Información del Evento</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong><br>
                                    <?= date('d/m/Y', strtotime($evento->fecha_evento)) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Hora:</strong><br>
                                    <?= $evento->hora_evento ? date('H:i', strtotime($evento->hora_evento)) . ' hrs' : 'No definida' ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tipo de evento:</strong><br>
                                    <?= htmlspecialchars($evento->tipo_evento ?: 'No especificado') ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Valor:</strong><br>
                                    <?= $evento->valor_evento ? '$' . number_format($evento->valor_evento, 0, ',', '.') : 'No definido' ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Ciudad:</strong><br>
                                    <?= htmlspecialchars($evento->ciudad_evento ?: 'No especificada') ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Lugar específico:</strong><br>
                                    <?= htmlspecialchars($evento->lugar_evento ?: 'No especificado') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Participantes -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-users"></i> Participantes</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Cliente:</strong><br>
                                    <?php if (isset($evento->cliente_nombre)): ?>
                                        <?= htmlspecialchars($evento->cliente_nombre . ' ' . $evento->cliente_apellido) ?>
                                        <?php if (isset($evento->cliente_id)): ?>
                                            <a href="<?= base_url ?>cliente/ver/<?= $evento->cliente_id ?>" class="btn btn-xs btn-info pull-right">
                                                <i class="fa fa-eye"></i> Ver detalles
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No asignado</span>
                                    <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Artista:</strong><br>
                                    <?php if (isset($evento->artista_nombre)): ?>
                                        <?= htmlspecialchars($evento->artista_nombre) ?>
                                        <?php if (isset($evento->artista_id)): ?>
                                            <a href="<?= base_url ?>artista/ver/<?= $evento->artista_id ?>" class="btn btn-xs btn-info pull-right">
                                                <i class="fa fa-eye"></i> Ver detalles
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No asignado</span>
                                    <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Gira/Tour:</strong><br>
                                    <?php if (isset($evento->gira_nombre)): ?>
                                        <?= htmlspecialchars($evento->gira_nombre) ?>
                                        <a href="<?= base_url ?>gira/ver/<?= $evento->gira_id ?>" class="btn btn-xs btn-info pull-right">
                                            <i class="fa fa-eye"></i> Ver gira
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Evento independiente (no parte de gira)</span>
                                    <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Segunda fila de paneles -->
            <div class="row">
                <!-- Logística -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-truck"></i> Logística</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <div class="circle-indicator <?= $evento->hotel == 'Si' ? 'bg-success' : 'bg-danger' ?>">
                                        <i class="fa fa-hotel"></i>
                                    </div>
                                    <p><strong>Hospedaje</strong></p>
                                    <p><?= $evento->hotel == 'Si' ? 'Incluido' : 'No incluido' ?></p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="circle-indicator <?= $evento->traslados == 'Si' ? 'bg-success' : 'bg-danger' ?>">
                                        <i class="fa fa-car"></i>
                                    </div>
                                    <p><strong>Traslados</strong></p>
                                    <p><?= $evento->traslados == 'Si' ? 'Incluidos' : 'No incluidos' ?></p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="circle-indicator <?= $evento->viaticos == 'Si' ? 'bg-success' : 'bg-danger' ?>">
                                        <i class="fa fa-cutlery"></i>
                                    </div>
                                    <p><strong>Viáticos</strong></p>
                                    <p><?= $evento->viaticos == 'Si' ? 'Incluidos' : 'No incluidos' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Historial y acciones -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-clock-o"></i> Historial y Acciones</h3>
                        </div>
                        <div class="panel-body">
                            <p><strong>Fecha de creación:</strong> <?= date('d/m/Y H:i', strtotime($evento->fecha_creacion)) ?></p>
                            <p><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($evento->fecha_actualizacion)) ?></p>
                            
                            <hr>
                            
                            <!-- Acciones según el estado actual -->
                            <div class="btn-group btn-group-justified m-t-10">
                                <?php if ($evento->estado_evento == 'Propuesta' || $evento->estado_evento == 'Solicitado'): ?>
                                    <a href="<?= base_url ?>evento/confirmar/<?= $evento->id ?>" class="btn btn-success waves-effect waves-light">
                                        <i class="fa fa-check"></i> Confirmar
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($evento->estado_evento == 'Confirmado'): ?>
                                    <a href="<?= base_url ?>evento/finalizar/<?= $evento->id ?>" class="btn btn-primary waves-effect waves-light">
                                        <i class="fa fa-flag-checkered"></i> Finalizar
                                    </a>
                                    <a href="<?= base_url ?>evento/reagendar/<?= $evento->id ?>" class="btn btn-info waves-effect waves-light">
                                        <i class="fa fa-calendar-plus-o"></i> Reagendar
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($evento->estado_evento != 'Cancelado'): ?>
                                    <a href="javascript:void(0);" onclick="cambiarEstado(<?= $evento->id ?>, 'Cancelado')" class="btn btn-danger waves-effect waves-light">
                                        <i class="fa fa-ban"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Archivos asociados (para futura implementación) -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa fa-file"></i> Archivos Asociados</h3>
                        </div>
                        <div class="panel-body">
                            <p class="text-muted">No hay archivos asociados a este evento.</p>
                            <!-- Aquí se listarían los archivos cuando se implemente esa funcionalidad -->
                            <div class="text-center">
                                <button class="btn btn-info waves-effect waves-light disabled">
                                    <i class="fa fa-upload"></i> Subir Archivos
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .circle-indicator {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 24px;
    }
    .bg-success {
        background-color: #26c6da;
    }
    .bg-danger {
        background-color: #ef5350;
    }
</style>

<script>
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