<?php
// Aseguramos que se ha cargado el helper de autenticación
if (!function_exists('isUserLoggedIn')) {
    require_once 'helpers/auth_helper.php';
}

// Verificar que el usuario esté autenticado
if (!isUserLoggedIn() && !isAdminLoggedIn()) {
    redirectTo('user/login');
}
?>
<!DOCTYPE html>
<html>
<head>
    <!-- El head se incluye desde el header general -->
    <title><?= $pageTitle ?></title>
</head>
<body>
    <!-- Contenido principal -->
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Crear Nuevo Cliente</h3>
                            <p class="text-muted m-b-30">Complete el formulario para registrar un nuevo cliente en el sistema</p>
                        </div>

                        <!-- Mensajes de error o éxito -->
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?= $_SESSION['error_message'] ?>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <?= $_SESSION['success_message'] ?>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>

                        <!-- Formulario de creación de cliente -->
                        <form id="formCrearCliente" class="form-horizontal" method="post" action="<?= base_url ?>clientes/guardar">
                            <!-- Token CSRF para seguridad -->
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-user m-r-5"></i> Información Personal</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Nombres <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="nombres" id="nombres" required
                                                placeholder="Ingrese los nombres">
                                            <small class="help-block">Nombres del cliente</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Apellidos <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="apellidos" id="apellidos" required
                                                placeholder="Ingrese los apellidos">
                                            <small class="help-block">Apellidos del cliente</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Género <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="genero" id="genero" required>
                                                <option value="">Seleccione un género...</option>
                                                <option value="Masculino">Masculino</option>
                                                <option value="Femenino">Femenino</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                            <small class="help-block">Género del cliente</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Tipo de ID</label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                                                <option value="RUT">RUT</option>
                                                <option value="DNI">DNI</option>
                                                <option value="Pasaporte">Pasaporte</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                            <small class="help-block">Tipo de documento de identidad</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Número de ID</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="numero_identificacion" id="numero_identificacion"
                                                placeholder="Ingrese el número de identificación">
                                            <small class="help-block">Número de documento de identidad</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-envelope m-r-5"></i> Información de Contacto</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">País</label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="pais" id="pais" 
                                                value="Chile" placeholder="Ingrese el país">
                                            <input type="hidden" name="codigo_pais" value="CL">
                                            <small class="help-block">País de residencia del cliente</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Correo Electrónico</label>
                                        <div class="col-md-6">
                                            <input type="email" class="form-control" name="correo" id="correo"
                                                placeholder="ejemplo@dominio.com">
                                            <small class="help-block">Dirección de correo electrónico</small>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Teléfono Celular</label>
                                        <div class="col-md-6">
                                            <input type="tel" class="form-control" name="celular" id="celular"
                                                placeholder="+56 9 1234 5678">
                                            <small class="help-block">Número de teléfono móvil</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title"><i class="fa fa-check-circle m-r-5"></i> Estado</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Estado <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="estado" id="estado" required>
                                                <option value="Activo" selected>Activo</option>
                                                <option value="Inactivo">Inactivo</option>
                                            </select>
                                            <small class="help-block">Los clientes inactivos no aparecerán en las búsquedas</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="form-group m-b-0 text-center">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success waves-effect waves-light">
                                        <i class="fa fa-check"></i> Guardar Cliente
                                    </button>
                                    <button type="button" class="btn btn-default waves-effect waves-light m-l-10" onclick="confirmCancel()">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <!-- JavaScript para la validación y comportamiento del formulario -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validación del formulario antes de enviar
            document.getElementById('formCrearCliente').addEventListener('submit', function(event) {
                const nombres = document.getElementById('nombres').value.trim();
                const apellidos = document.getElementById('apellidos').value.trim();
                const genero = document.getElementById('genero').value;
                
                if (nombres === '') {
                    event.preventDefault();
                    alert('El nombre del cliente es obligatorio');
                    document.getElementById('nombres').focus();
                    return false;
                }
                
                if (apellidos === '') {
                    event.preventDefault();
                    alert('Los apellidos del cliente son obligatorios');
                    document.getElementById('apellidos').focus();
                    return false;
                }
                
                if (genero === '') {
                    event.preventDefault();
                    alert('Debe seleccionar un género');
                    document.getElementById('genero').focus();
                    return false;
                }
            });
        });

        // Función para confirmar cancelación
        function confirmCancel() {
            if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
                window.location.href = '<?= base_url ?>clientes/index';
            }
        }
    </script>

    <!-- Estilos personalizados -->
    <style>
        .panel {
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .panel-heading {
            background-color: #f5f5f5;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }

        .panel-title {
            margin: 0;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }

        .panel-body {
            padding: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .control-label {
            text-align: right;
            padding-top: 7px;
        }

        .help-block {
            font-size: 12px;
            color: #737373;
            margin-top: 5px;
        }

        .text-danger {
            color: #f33155;
        }

        .m-t-20 {
            margin-top: 20px;
        }
    </style>
</body>
</html>