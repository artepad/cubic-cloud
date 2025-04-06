<?php
// En lugar de usar datos de ejemplo, usaremos el objeto $usuario que viene del controlador
// $usuario se obtiene del método getById del modelo Usuario
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="panel-title" style="color: white; font-weight: bold;">Detalles del Usuario</h3>
                    </div>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <!-- Mensajes de éxito y error -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= $_SESSION['success_message'] ?>
                            <?php unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= $_SESSION['error_message'] ?>
                            <?php unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Información del usuario -->
                    <div class="form-horizontal">
                        <!-- Sección de información personal -->
                        <h3 class="box-title">Información Personal</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Nombre:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Email:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->email) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Teléfono:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->telefono) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Tipo de usuario:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->tipo_usuario) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">País:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->pais . ' (' . $usuario->codigo_pais . ')') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Identificación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->tipo_identificacion . ': ' . $usuario->numero_identificacion) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Estado:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <?php if ($usuario->estado == 'Activo'): ?>
                                                <span class="label label-success">Activo</span>
                                            <?php else: ?>
                                                <span class="label label-warning">Inactivo</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de seguridad -->
                        <h3 class="box-title m-t-30">Seguridad y Acceso</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Último acceso:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $usuario->ultimo_login ? date('d/m/Y H:i', strtotime($usuario->ultimo_login)) : 'Nunca' ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">IP último acceso:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->ip_ultimo_acceso ?? 'No disponible') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Fecha de creación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($usuario->fecha_creacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Última actualización:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($usuario->fecha_actualizacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acciones  -->
                        <div class="form-actions m-t-30">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="btn-group m-r-10">
                                        <a href="<?= base_url ?>usuario/editar?id=<?= $usuario->id ?>" class="btn btn-warning waves-effect waves-light">
                                            <i class="fa fa-pencil"></i> Editar
                                        </a>
                                    </div>
                                    <div class="btn-group m-r-10">
                                        <a href="<?= base_url ?>usuario/listar" class="btn btn-info waves-effect waves-light">
                                            <i class="fa fa-arrow-left"></i> Volver
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar componentes necesarios
        if (typeof $ !== 'undefined') {
            // Inicializar tooltips si están disponibles
            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    });
</script>