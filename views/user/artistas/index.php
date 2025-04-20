<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Artistas</h3>
            <p class="text-muted">Administra los artistas representados por tu empresa</p>

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
                    <a href="<?= base_url ?>artista/crear" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Artista
                    </a>
                </div>
            </div>

            <!-- Tabla de artistas -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Género Musical</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($artistas) && !empty($artistas)): ?>
                            <?php foreach ($artistas as $artista): ?>
                                <tr>
                                    <td><?= $artista->id ?></td>
                                    <td>
                                        <?php if (!empty($artista->imagen_presentacion) && file_exists($artista->imagen_presentacion)): ?>
                                            <img src="<?= base_url . $artista->imagen_presentacion ?>" alt="<?= htmlspecialchars($artista->nombre) ?>" width="50" class="img-thumbnail">
                                        <?php else: ?>
                                            <img src="<?= base_url ?>assets/images/placeholder-artist.jpg" alt="Sin imagen" width="50" class="img-thumbnail">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($artista->nombre) ?></td>
                                    <td><?= htmlspecialchars($artista->genero_musical) ?></td>
                                    <td>
                                        <?php if ($artista->estado == 'Activo'): ?>
                                            <span class="label label-success">Activo</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>artista/ver/<?= $artista->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="icon-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>artista/editar/<?= $artista->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="icon-pencil"></i>
                                        </a>
                                        <?php if ($artista->estado == 'Activo'): ?>
                                            <a href="<?= base_url ?>artista/cambiarEstado/<?= $artista->id ?>/Inactivo"
                                                class="btn btn-danger btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Suspender"
                                                onclick="return confirm('¿Está seguro que desea desactivar a este artista?')">
                                                <i class="icon-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url ?>artista/cambiarEstado/<?= $artista->id ?>/Activo"
                                                class="btn btn-success btn-circle"
                                                data-toggle="tooltip"
                                                data-original-title="Activar"
                                                onclick="return confirm('¿Está seguro que desea activar a este artista?')">
                                                <i class="icon-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $artista->id ?>)" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No hay artistas registrados</td>
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

    function confirmarEliminar(artistaId) {
        if (confirm('¿Está seguro que desea eliminar este artista? Esta acción no se puede deshacer.')) {
            window.location.href = '<?= base_url ?>artista/eliminar/' + artistaId;
        }
    }
</script>