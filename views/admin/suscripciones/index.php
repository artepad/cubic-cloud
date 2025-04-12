<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Cargar modelos necesarios
$suscripcionModel = new Suscripcion();
$empresaModel = new Empresa();
$planModel = new Plan();

// Obtener todas las suscripciones sin paginación
$suscripciones = $suscripcionModel->getAll();
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Suscripciones</h3>
            <p class="text-muted">Administre las suscripciones activas en el sistema</p>

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

            <!-- Botón para crear nueva suscripción -->
            <div class="row m-t-10 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>admin/crearSuscripcion" class="btn btn-success waves-effect waves-light">
                        <i class="fa fa-plus"></i> Nueva Suscripción
                    </a>
                </div>
            </div>

            <!-- Tabla de suscripciones -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tabla-suscripciones">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Número</th>
                            <th>Empresa</th>
                            <th>Plan</th>
                            <th class="text-center">Período</th>
                            <th class="text-center">Inicio</th>
                            <th class="text-center">Próximo Cobro</th>
                            <th class="text-center">Precio</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($suscripciones)): ?>
                            <?php foreach ($suscripciones as $suscripcion): ?>
                                <tr>
                                    <td class="text-center"><?= $suscripcion->id ?></td>
                                    <td class="text-center"><?= htmlspecialchars($suscripcion->numero_suscripcion) ?></td>
                                    <td>
                                        <a href="<?= base_url ?>empresa/ver/<?= $suscripcion->empresa_id ?>" data-toggle="tooltip" title="Ver empresa">
                                            <?= htmlspecialchars($suscripcion->empresa_nombre) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($suscripcion->plan_nombre) ?>
                                        <span class="label label-info"><?= htmlspecialchars($suscripcion->tipo_plan) ?></span>
                                    </td>
                                    <td class="text-center"><?= htmlspecialchars($suscripcion->periodo_facturacion) ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($suscripcion->fecha_inicio)) ?></td>
                                    <td class="text-center">
                                        <?php 
                                        if ($suscripcion->fecha_siguiente_factura) {
                                            $fecha_siguiente = new DateTime($suscripcion->fecha_siguiente_factura);
                                            $hoy = new DateTime();
                                            $diferencia = $hoy->diff($fecha_siguiente);
                                            $dias_restantes = $diferencia->invert ? -$diferencia->days : $diferencia->days;
                                            
                                            $clase = 'label-success';
                                            if ($dias_restantes < 0) {
                                                $clase = 'label-danger';
                                            } elseif ($dias_restantes < 7) {
                                                $clase = 'label-warning';
                                            }
                                            
                                            echo date('d/m/Y', strtotime($suscripcion->fecha_siguiente_factura));
                                            echo ' <span class="label ' . $clase . '">';
                                            
                                            if ($dias_restantes < 0) {
                                                echo abs($dias_restantes) . ' días vencida';
                                            } elseif ($dias_restantes == 0) {
                                                echo 'Hoy';
                                            } else {
                                                echo $dias_restantes . ' días';
                                            }
                                            
                                            echo '</span>';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                        $simbolo = '$';
                                        switch ($suscripcion->moneda) {
                                            case 'USD':
                                                $simbolo = 'US$';
                                                break;
                                            case 'EUR':
                                                $simbolo = '€';
                                                break;
                                        }
                                        echo $simbolo . ' ' . number_format($suscripcion->precio_total, 2);
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $estado_class = 'label-default';
                                        switch ($suscripcion->estado) {
                                            case 'Activa':
                                                $estado_class = 'label-success';
                                                break;
                                            case 'Pendiente':
                                                $estado_class = 'label-warning';
                                                break;
                                            case 'Suspendida':
                                                $estado_class = 'label-danger';
                                                break;
                                            case 'Cancelada':
                                                $estado_class = 'label-danger';
                                                break;
                                            case 'Finalizada':
                                                $estado_class = 'label-default';
                                                break;
                                        }
                                        ?>
                                        <span class="label <?= $estado_class ?>"><?= htmlspecialchars($suscripcion->estado) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="<?= base_url ?>admin/verSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            
                                            <a href="<?= base_url ?>admin/editarSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            
                                            <?php if ($suscripcion->estado == 'Activa'): ?>
                                                <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $suscripcion->id ?>, 'Suspendida')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                                    <i class="fa fa-ban"></i>
                                                </a>
                                            <?php elseif ($suscripcion->estado == 'Suspendida'): ?>
                                                <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $suscripcion->id ?>, 'Activa')" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Reactivar">
                                                    <i class="fa fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($suscripcion->estado != 'Cancelada' && $suscripcion->estado != 'Finalizada'): ?>
                                                <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $suscripcion->id ?>, 'Cancelada')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                                
                                                <a href="javascript:void(0);" onclick="confirmarRenovar(<?= $suscripcion->id ?>)" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Renovar">
                                                    <i class="fa fa-refresh"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <a href="<?= base_url ?>admin/historialSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver historial">
                                                <i class="fa fa-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No hay suscripciones registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para operaciones con suscripciones -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Inicializar datatables si está disponible
        if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            $('#tabla-suscripciones').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                },
                "pageLength": 10,
                "order": [
                    [0, "desc"]
                ]
            });
        }
    });

    // Confirmar cambio de estado
    function confirmarCambiarEstado(id, nuevoEstado) {
        let mensaje = '';
        switch(nuevoEstado) {
            case 'Activa':
                mensaje = '¿Está seguro de que desea activar esta suscripción?';
                break;
            case 'Suspendida':
                mensaje = '¿Está seguro de que desea suspender esta suscripción? Esto limitará el acceso a la empresa.';
                break;
            case 'Cancelada':
                mensaje = '¿Está seguro de que desea cancelar esta suscripción? Esto finalizará el servicio para esta empresa.';
                break;
            default:
                mensaje = '¿Está seguro de que desea cambiar el estado de esta suscripción a ' + nuevoEstado + '?';
        }

        if (confirm(mensaje)) {
            window.location.href = '<?= base_url ?>admin/cambiarEstadoSuscripcion?id=' + id + '&estado=' + nuevoEstado;
        }
    }

    // Confirmar renovación
    function confirmarRenovar(id) {
        if (confirm('¿Está seguro de que desea renovar esta suscripción? Esto extenderá el período según la configuración actual.')) {
            window.location.href = '<?= base_url ?>admin/renovarSuscripcion?id=' + id;
        }
    }
</script>

<!-- Estilos adicionales -->
<style>
    .btn-circle {
        border-radius: 100%;
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }

    .table th {
        font-weight: 600;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
    }

    .label {
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 12px;
        font-weight: 500;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
</style>