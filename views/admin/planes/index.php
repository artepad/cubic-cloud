<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Planes</h3>
            <p class="text-muted">Administra los planes de suscripción disponibles en el sistema</p>
            
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

            <!-- Acciones de gestión -->
            <div class="row m-t-10 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>plan/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Plan
                    </a>
                </div>
            </div>
            
            <!-- Tabla de planes -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Precio Mensual</th>
                            <th>Usuarios</th>
                            <th>Artistas</th>
                            <th>Eventos</th>
                            <th>Estado</th>
                            <th>Visible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($planes) && !empty($planes)): ?>
                            <?php foreach ($planes as $plan): ?>
                                <tr>
                                    <td><?= $plan->id ?></td>
                                    <td><?= htmlspecialchars($plan->nombre) ?></td>
                                    <td><?= htmlspecialchars($plan->tipo_plan) ?></td>
                                    <td>
                                        <?php
                                        $simbolo = '';
                                        switch ($plan->moneda) {
                                            case 'CLP': $simbolo = '$'; break;
                                            case 'USD': $simbolo = 'US$'; break;
                                            case 'EUR': $simbolo = '€'; break;
                                        }
                                        echo $simbolo . number_format($plan->precio_mensual, 0, ',', '.');
                                        ?>
                                    </td>
                                    <td><?= $plan->max_usuarios == 0 ? 'Ilimitados' : $plan->max_usuarios ?></td>
                                    <td><?= $plan->max_artistas == 0 ? 'Ilimitados' : $plan->max_artistas ?></td>
                                    <td><?= $plan->max_eventos == 0 ? 'Ilimitados' : $plan->max_eventos ?></td>
                                    <td>
                                        <?php
                                        $estado_class = '';
                                        switch ($plan->estado) {
                                            case 'Activo': $estado_class = 'label-success'; break;
                                            case 'Inactivo': $estado_class = 'label-warning'; break;
                                            case 'Descontinuado': $estado_class = 'label-danger'; break;
                                        }
                                        ?>
                                        <span class="label <?= $estado_class ?>"><?= $plan->estado ?></span>
                                    </td>
                                    <td>
                                        <span class="label <?= $plan->visible == 'Si' ? 'label-info' : 'label-default' ?>">
                                            <?= $plan->visible ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>plan/ver?id=<?= $plan->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        
                                        <a href="<?= base_url ?>plan/editar?id=<?= $plan->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        
                                        <?php if ($plan->estado == 'Activo'): ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambioEstado(<?= $plan->id ?>, 'Inactivo')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Desactivar">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambioEstado(<?= $plan->id ?>, 'Activo')" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Activar">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $plan->id ?>)" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No hay planes registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para las confirmaciones -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    
    // Confirmar cambio de estado
    function confirmarCambioEstado(id, nuevoEstado) {
        var mensaje = '';
        if (nuevoEstado === 'Activo') {
            mensaje = '¿Estás seguro de que deseas activar este plan?';
        } else if (nuevoEstado === 'Inactivo') {
            mensaje = '¿Estás seguro de que deseas desactivar este plan?';
        } else {
            mensaje = '¿Estás seguro de que deseas cambiar el estado del plan a ' + nuevoEstado + '?';
        }
        
        if (confirm(mensaje)) {
            window.location.href = '<?= base_url ?>plan/cambiarEstado?id=' + id + '&estado=' + nuevoEstado;
        }
    }
    
    // Confirmar eliminación
    function confirmarEliminar(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este plan? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url ?>plan/delete?id=' + id;
        }
    }
</script>