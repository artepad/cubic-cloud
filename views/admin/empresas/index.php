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
            <div class="row m-t-10 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>empresa/crear" class="btn btn-success waves-effect waves-light">
                        <i class="fa fa-plus"></i> Nueva Empresa
                    </a>
                </div>
            </div>

            <!-- Tabla de empresas -->
            <div class="table-responsive">
                <table class="table table-striped" id="tabla-empresas">
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Identificación</th>
                            <th class="text-center">Administrador</th>
                            <th class="text-center">País</th>
                            <th class="text-center">Plan</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($empresas)): ?>
                            <?php foreach ($empresas as $empresa): ?>
                                <tr>
                                    <td class="text-center"><?= $empresa->id ?></td>
                                    <td><?= htmlspecialchars($empresa->nombre) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($empresa->identificacion_fiscal ?: '-') ?></td>
                                    <td class="text-center"><?= isset($empresa->admin_nombre) ? htmlspecialchars($empresa->admin_nombre . ' ' . $empresa->admin_apellido) : 'Sin asignar' ?></td>
                                    <td class="text-center"><?= htmlspecialchars($empresa->pais) ?></td>
                                    <td class="text-center">
                                        <?php
                                        // Obtenemos el plan asociado a través de la suscripción activa
                                        $suscripcion = isset($empresa->suscripcion) ? $empresa->suscripcion : null;
                                        $plan_nombre = $suscripcion ? htmlspecialchars($suscripcion->plan_nombre) : 'Sin plan';
                                        echo $plan_nombre;
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($empresa->es_demo == 'Si'): ?>
                                            <span class="label label-warning">Demo</span>
                                        <?php else: ?>
                                            <span class="label label-info">Normal</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($empresa->estado == 'activa'): ?>
                                            <span class="label label-success">Activa</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Suspendida</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url ?>empresa/ver/<?= $empresa->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>admin/editarEmpresa?id=<?= $empresa->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <?php if ($empresa->estado == 'activa'): ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $empresa->id ?>, 'suspendida')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $empresa->id ?>, 'activa')" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Activar">
                                                <i class="fa fa-check"></i>
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

<!-- JavaScript para operaciones con empresas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips
        if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Inicializar datatables si está disponible
        if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
            $('#tabla-empresas').DataTable({
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
        let mensaje = nuevoEstado === 'activa' ?
            '¿Está seguro de que desea activar esta empresa?' :
            '¿Está seguro de que desea suspender esta empresa? Esto impedirá el acceso a todos sus usuarios.';

        if (confirm(mensaje)) {
            window.location.href = '<?= base_url ?>admin/cambiarEstadoEmpresa?id=' + id + '&estado=' + nuevoEstado;
        }
    }

    // Confirmar eliminación
    function confirmarEliminar(id) {
        if (confirm('¿Está seguro de que desea eliminar esta empresa? Esta acción no se puede deshacer y eliminará todos los datos asociados.')) {
            window.location.href = '<?= base_url ?>admin/eliminarEmpresa?id=' + id;
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
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .label {
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 12px;
        font-weight: 500;
    }
</style>