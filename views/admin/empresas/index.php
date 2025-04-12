<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Obtener empresas de la base de datos
$empresa_model = new Empresa();
$empresas = $empresa_model->getAll();
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Empresas</h3>
            <p class="text-muted">Administre las empresas registradas en el sistema</p>

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

            <!-- Botón para crear nueva empresa -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>empresa/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nueva Empresa
                    </a>
                </div>
            </div>

            <!-- Tabla de empresas -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Administrador</th>
                            <th>País</th>
                            <th>Plan</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($empresas)): ?>
                            <?php foreach ($empresas as $empresa): ?>
                                <tr>
                                    <td><?= $empresa->id ?></td>
                                    <td><?= htmlspecialchars($empresa->nombre) ?></td>
                                    <td><?= htmlspecialchars($empresa->identificacion_fiscal ?: '-') ?></td>
                                    <td><?= isset($empresa->admin_nombre) ? htmlspecialchars($empresa->admin_nombre . ' ' . $empresa->admin_apellido) : 'Sin asignar' ?></td>
                                    <td><?= htmlspecialchars($empresa->pais) ?></td>
                                    <td>
                                        <?php
                                        if (isset($empresa->suscripcion)) {
                                            echo htmlspecialchars($empresa->suscripcion->plan_nombre ?: 'Sin plan');
                                        } else {
                                            echo 'Sin plan';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($empresa->es_demo == 'Si'): ?>
                                            <span class="label label-warning">Demo</span>
                                        <?php else: ?>
                                            <span class="label label-info">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($empresa->estado == 'activa'): ?>
                                            <span class="label label-success">Activa</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Suspendida</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>empresa/ver/<?= $empresa->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>empresa/editar/<?= $empresa->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="icon-pencil"></i>
                                        </a>

                                        <?php if ($empresa->estado == 'activa'): ?>
                                            <a href="<?= base_url ?>empresa/cambiarEstado/<?= $empresa->id ?>/suspendida"
                                                class="btn btn-danger btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Suspender"
                                                onclick="return confirm('¿Está seguro que desea suspender esta empresa? Esto impedirá el acceso a todos sus usuarios.')">
                                                <i class="icon-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url ?>empresa/cambiarEstado/<?= $empresa->id ?>/activa"
                                                class="btn btn-success btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Activar"
                                                onclick="return confirm('¿Está seguro que desea activar esta empresa?')">
                                                <i class="icon-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $empresa->id ?>)" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay empresas registradas</td>
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

    function confirmarEliminar(empresaId) {
        if (confirm('¿Está seguro que desea eliminar esta empresa? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url ?>empresa/eliminar/' + empresaId;
        }
    }
</script>