<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Clientes</h3>
            <p class="text-muted">Administra los clientes registrados en tu empresa</p>

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
                    <a href="<?= base_url ?>clientes/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Cliente
                    </a>
                </div>
            </div>

            <!-- Tabla de clientes -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Identificación</th>
                            <th>Género</th>
                            <th>Correo</th>
                            <th>Celular</th>
                            <th>País</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($clientes) && !empty($clientes)): ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?= $cliente->id ?></td>
                                    <td><?= htmlspecialchars($cliente->nombres . ' ' . $cliente->apellidos) ?></td>
                                    <td><?= htmlspecialchars($cliente->numero_identificacion) ?></td>
                                    <td><?= htmlspecialchars($cliente->genero) ?></td>
                                    <td><?= htmlspecialchars($cliente->correo) ?></td>
                                    <td><?= htmlspecialchars($cliente->celular) ?></td>
                                    <td><?= htmlspecialchars($cliente->pais) ?></td>
                                    <td>
                                        <?php if ($cliente->estado == 'Activo'): ?>
                                            <span class="label label-success">Activo</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>cliente/ver/<?= $cliente->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>cliente/editar/<?= $cliente->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <?php if ($cliente->estado == 'Activo'): ?>
                                            <a href="<?= base_url ?>cliente/cambiarEstado/<?= $cliente->id ?>/Inactivo"
                                                class="btn btn-danger btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Suspender"
                                                onclick="return confirm('¿Está seguro que desea suspender a este cliente?')">
                                                <i class="icon-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url ?>cliente/cambiarEstado/<?= $cliente->id ?>/Activo"
                                                class="btn btn-success btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Activar"
                                                onclick="return confirm('¿Está seguro que desea activar a este cliente?')">
                                                <i class="icon-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $cliente->id ?>)" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No hay clientes registrados</td>
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

    function confirmarEliminar(clienteId) {
        if (confirm('¿Está seguro que desea eliminar este cliente? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url ?>clientes/eliminar/' + clienteId;
        }
    }
</script>