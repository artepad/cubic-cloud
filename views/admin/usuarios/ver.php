<?php
// Datos de ejemplo
$usuario = (object)[
    'id' => 1,
    'nombre' => 'Juan',
    'apellido' => 'Pérez',
    'email' => 'juan.perez@ejemplo.com',
    'telefono' => '+56 9 8765 4321',
    'pais' => 'Chile',
    'codigo_pais' => 'CL',
    'tipo_identificacion' => 'RUT',
    'numero_identificacion' => '12.345.678-9',
    'tipo_usuario' => 'ADMIN',
    'estado' => 'Activo',
    'ultimo_login' => '2025-04-02 15:30:45',
    'ip_ultimo_acceso' => '192.168.1.100',
    'intentos_fallidos' => 0,
    'fecha_creacion' => '2024-12-15 09:20:30',
    'fecha_actualizacion' => '2025-03-28 14:15:20'
];
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
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        Usuario actualizado correctamente
                    </div>

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
                                            <strong><?= date('d/m/Y H:i', strtotime($usuario->ultimo_login)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">IP último acceso:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($usuario->ip_ultimo_acceso) ?></strong>
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
                                    <div class="btn-group dropup m-r-10">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">
                                            Acciones <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li><a href="#" data-toggle="modal" data-target="#resetPasswordModal">Cambiar Contraseña</a></li>
                                            <?php if ($usuario->estado == 'Activo'): ?>
                                                <li><a href="javascript:void(0);" class="cambiar-estado" data-id="<?= $usuario->id ?>" data-estado="Inactivo">Suspender</a></li>
                                            <?php else: ?>
                                                <li><a href="javascript:void(0);" class="cambiar-estado" data-id="<?= $usuario->id ?>" data-estado="Activo">Activar</a></li>
                                            <?php endif; ?>
                                            <li><a href="#" data-toggle="modal" data-target="#enviarEmailModal">Enviar Email</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group dropup m-r-10">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-warning dropdown-toggle waves-effect waves-light" type="button">
                                            Opciones <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li><a href="<?= base_url ?>usuario/editar?id=<?= $usuario->id ?>">Editar</a></li>
                                            <li><a href="<?= base_url ?>usuario/listar">Volver</a></li>
                                        </ul>
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

<!-- Modal para Restablecer Contraseña -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="resetPasswordModalLabel">Restablecer Contraseña</h4>
            </div>
            <form action="<?= base_url ?>usuario/resetearPassword" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $usuario->id ?>">
                    <div class="form-group">
                        <label for="nueva_password">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="nueva_password" name="nueva_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_password">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="notificar_usuario"> Notificar al usuario por email
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Enviar Email -->
<div class="modal fade" id="enviarEmailModal" tabindex="-1" role="dialog" aria-labelledby="enviarEmailModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="enviarEmailModalLabel">Enviar Email</h4>
            </div>
            <form action="<?= base_url ?>usuario/enviarEmail" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $usuario->id ?>">
                    <div class="form-group">
                        <label for="asunto">Asunto</label>
                        <input type="text" class="form-control" id="asunto" name="asunto" required>
                    </div>
                    <div class="form-group">
                        <label for="mensaje">Mensaje</label>
                        <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar los dropdowns de Bootstrap
        $('.dropdown-toggle').dropdown();
        
        // Gestionar cambio de estado
        const botonesEstado = document.querySelectorAll('.cambiar-estado');
        
        botonesEstado.forEach(function(boton) {
            boton.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const estado = this.getAttribute('data-estado');
                
                if (confirm(`¿Estás seguro de que deseas cambiar el estado del usuario a ${estado}?`)) {
                    window.location.href = `<?= base_url ?>usuario/cambiarEstado?id=${id}&estado=${estado}`;
                }
            });
        });
        
        // Validación de contraseñas coincidentes
        const formResetPassword = document.querySelector('#resetPasswordModal form');
        
        if (formResetPassword) {
            formResetPassword.addEventListener('submit', function(event) {
                const password = document.getElementById('nueva_password').value;
                const confirmPassword = document.getElementById('confirmar_password').value;
                
                if (password !== confirmPassword) {
                    event.preventDefault();
                    alert('Las contraseñas no coinciden');
                }
            });
        }
    });
</script>

<style>
    .m-t-0 { margin-top: 0; }
    .m-b-20 { margin-bottom: 20px; }
    .m-t-30 { margin-top: 30px; }
    .m-r-10 { margin-right: 10px; }
    
    .form-horizontal .form-group {
        margin-bottom: 15px;
    }
    
    .form-control-static {
        min-height: 34px;
        padding-top: 7px;
        padding-bottom: 7px;
    }
    
    .table>tbody>tr>td {
        vertical-align: middle;
    }
    
    .panel-heading {
        padding: 15px;
    }
    
    .panel-title {
        margin-top: 5px;
    }
    
    .box-title {
        font-size: 18px;
        margin-bottom: 0;
    }
    
    hr {
        border-top: 1px solid #eee;
    }
    
    .label {
        display: inline-block;
        padding: 5px 10px;
        font-size: 12px;
    }
    
    .form-actions {
        padding: 15px 0;
        border-top: 1px solid #eee;
    }
    
    /* Estilos para los botones desplegables */
    .btn-group.dropup {
        margin-bottom: 10px;
    }
    
    .dropdown-menu {
        min-width: 160px;
    }
    
    .dropdown-menu > li > a {
        padding: 8px 20px;
    }
    
    .dropdown-menu > li > a:hover {
        background-color: #f5f5f5;
    }
</style>

