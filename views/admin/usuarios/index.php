<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Usuarios</h3>
            <p class="text-muted">Administra los usuarios registrados en el sistema</p>

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
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>usuario/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Usuario
                    </a>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>País</th>
                            <th>Identificación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($usuarios) && !empty($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= $usuario->id ?></td>
                                    <td><?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></td>
                                    <td><?= htmlspecialchars($usuario->email) ?></td>
                                    <td><?= htmlspecialchars($usuario->tipo_usuario) ?></td>
                                    <td><?= htmlspecialchars($usuario->pais) ?></td>
                                    <td><?= htmlspecialchars($usuario->numero_identificacion) ?></td>
                                    <td>
                                        <?php if ($usuario->estado == 'Activo'): ?>
                                            <span class="label label-success">Activo</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>usuario/ver/<?= $usuario->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>usuario/editar/<?= $usuario->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <?php if ($usuario->estado == 'Activo'): ?>
                                            <a href="<?= base_url ?>usuario/cambiarEstado/<?= $usuario->id ?>/Inactivo"
                                                class="btn btn-danger btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Suspender"
                                                onclick="return confirm('¿Está seguro que desea suspender a este usuario?')">
                                                <i class="icon-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url ?>usuario/cambiarEstado/<?= $usuario->id ?>/Activo"
                                                class="btn btn-success btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Activar"
                                                onclick="return confirm('¿Está seguro que desea activar a este usuario?')">
                                                <i class="icon-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $usuario->id ?>)" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay usuarios registrados</td>
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

    function confirmarEliminar(userId) {
        if (confirm('¿Está seguro que desea eliminar este usuario? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url ?>usuario/eliminar/' + userId;
        }
    }
</script>