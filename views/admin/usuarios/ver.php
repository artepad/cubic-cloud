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
        <div class="white-box">
            <div class="d-flex align-items-center mb-4">
                <h3 class="box-title mb-0">Detalles del Usuario</h3>
                <div class="ms-auto">
                    <a href="<?= base_url ?>usuario/listar" class="btn btn-primary">
                        <i class="icon-arrow-left-circle"></i> Volver
                    </a>
                    <a href="<?= base_url ?>usuario/editar?id=<?= $usuario->id ?>" class="btn btn-warning">
                        <i class="icon-pencil"></i> Editar
                    </a>
                    <?php if ($usuario->estado == 'Activo'): ?>
                        <a href="javascript:void(0);" class="btn btn-danger cambiar-estado" 
                           data-id="<?= $usuario->id ?>" data-estado="Inactivo">
                            <i class="icon-ban"></i> Suspender
                        </a>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="btn btn-success cambiar-estado" 
                           data-id="<?= $usuario->id ?>" data-estado="Activo">
                            <i class="icon-check"></i> Activar
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mensajes de éxito y error -->
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                Usuario actualizado correctamente
            </div>

            <!-- Información del usuario -->
            <div class="row">
                <!-- Columna de información general -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Información General</h4>
                        </div>
                        <div class="panel-body">
                            <div class="user-detail-card">
                                <div class="text-center mb-4">
                                    <div class="profile-pic-container">
                                        <div class="profile-pic-default">
                                            <span class="initials">
                                                JP
                                            </span>
                                        </div>
                                    </div>
                                    <h4 class="mt-3 mb-0">Juan Pérez</h4>
                                    <span class="text-muted">ADMIN</span>
                                    <div class="mt-2"><span class="label label-success">Activo</span></div>
                                </div>
                                
                                <div class="info-list">
                                    <div class="info-item">
                                        <span class="info-label"><i class="icon-envelope"></i> Correo:</span>
                                        <span class="info-value">juan.perez@ejemplo.com</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><i class="icon-phone"></i> Teléfono:</span>
                                        <span class="info-value">+56 9 8765 4321</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><i class="icon-map"></i> País:</span>
                                        <span class="info-value">Chile (CL)</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label"><i class="icon-credit-card"></i> Identificación:</span>
                                        <span class="info-value">
                                            RUT: 12.345.678-9
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Columna de seguridad y acceso -->
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Seguridad y Acceso</h4>
                        </div>
                        <div class="panel-body">
                            <div class="info-list">
                                <div class="info-item">
                                    <span class="info-label"><i class="icon-clock"></i> Último acceso:</span>
                                    <span class="info-value">
                                        02/04/2025 15:30
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label"><i class="icon-screen-desktop"></i> IP último acceso:</span>
                                    <span class="info-value">192.168.1.100</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label"><i class="icon-lock"></i> Intentos de acceso fallidos:</span>
                                    <span class="info-value">0</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label"><i class="icon-calendar"></i> Fecha de creación:</span>
                                    <span class="info-value">15/12/2024 09:20</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label"><i class="icon-refresh"></i> Última actualización:</span>
                                    <span class="info-value">28/03/2025 14:15</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acciones adicionales -->
                    <div class="panel panel-default mt-4">
                        <div class="panel-heading">
                            <h4 class="panel-title">Acciones</h4>
                        </div>
                        <div class="panel-body">
                            <button class="btn btn-outline btn-info btn-block m-b-10" data-toggle="modal" data-target="#resetPasswordModal">
                                <i class="icon-lock-open"></i> Restablecer Contraseña
                            </button>
                            
                            <button class="btn btn-outline btn-warning btn-block" data-toggle="modal" data-target="#enviarEmailModal">
                                <i class="icon-envelope"></i> Enviar Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Historial de actividad (datos de ejemplo) -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">Historial de Actividad</h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th>IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>02/04/2025 15:30</td>
                                            <td><span class="label label-info">Inicio de sesión</span></td>
                                            <td>Inicio de sesión exitoso</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <tr>
                                            <td>28/03/2025 14:15</td>
                                            <td><span class="label label-warning">Actualización</span></td>
                                            <td>Actualización de datos personales</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <tr>
                                            <td>15/03/2025 10:45</td>
                                            <td><span class="label label-info">Inicio de sesión</span></td>
                                            <td>Inicio de sesión exitoso</td>
                                            <td>200.14.85.32</td>
                                        </tr>
                                        <tr>
                                            <td>02/03/2025 09:30</td>
                                            <td><span class="label label-danger">Intento fallido</span></td>
                                            <td>Contraseña incorrecta</td>
                                            <td>200.14.85.32</td>
                                        </tr>
                                        <tr>
                                            <td>15/01/2025 18:20</td>
                                            <td><span class="label label-primary">Cambio de contraseña</span></td>
                                            <td>Cambio de contraseña realizado</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <tr>
                                            <td>15/12/2024 09:20</td>
                                            <td><span class="label label-success">Creación</span></td>
                                            <td>Cuenta creada</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                    </tbody>
                                </table>
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

<style>
    /* Estilos para la tarjeta de usuario */
    .user-detail-card {
        margin-bottom: 20px;
    }
    
    .profile-pic-container {
        display: flex;
        justify-content: center;
    }
    
    .profile-pic-default {
        width: 100px;
        height: 100px;
        background-color: #3498db;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .initials {
        color: white;
        font-size: 36px;
        font-weight: bold;
    }
    
    .info-list {
        margin-top: 20px;
    }
    
    .info-item {
        padding: 10px 0;
        border-bottom: 1px solid #f1f1f1;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
        min-width: 120px;
        display: inline-block;
    }
    
    .info-value {
        color: #333;
    }
    
    /* Estilo para los botones de acción */
    .btn-outline {
        background-color: transparent;
        transition: all 0.3s;
    }
    
    .btn-outline.btn-info {
        color: #31708f;
        border-color: #31708f;
    }
    
    .btn-outline.btn-info:hover {
        background-color: #31708f;
        color: white;
    }
    
    .btn-outline.btn-warning {
        color: #8a6d3b;
        border-color: #8a6d3b;
    }
    
    .btn-outline.btn-warning:hover {
        background-color: #8a6d3b;
        color: white;
    }
    
    .mt-4 {
        margin-top: 20px;
    }
    
    .mb-4 {
        margin-bottom: 20px;
    }
    
    .mt-3 {
        margin-top: 15px;
    }
    
    .mb-0 {
        margin-bottom: 0;
    }
    
    .mt-2 {
        margin-top: 10px;
    }
    
    .m-b-10 {
        margin-bottom: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
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

