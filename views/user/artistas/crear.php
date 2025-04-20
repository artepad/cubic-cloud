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

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Nuevo Artista</h3>
                <p class="text-muted m-b-30">Complete el formulario para registrar un nuevo artista en el sistema</p>
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

            <!-- Formulario de creación de artista -->
            <form id="formCrearArtista" class="form-horizontal" method="post" action="<?= base_url ?>artistas/guardar" enctype="multipart/form-data">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-music m-r-5"></i> Información del Artista</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Nombre Artístico <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="nombre" id="nombre" required
                                    placeholder="Ingrese el nombre artístico">
                                <small class="help-block">Nombre con el que se promociona el artista</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Género Musical <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="genero_musical" id="genero_musical" required>
                                    <option value="">Seleccione un género...</option>
                                    <option value="Pop">Pop</option>
                                    <option value="Rock">Rock</option>
                                    <option value="Reggaeton">Reggaeton</option>
                                    <option value="Hip Hop">Hip Hop</option>
                                    <option value="Cumbia">Cumbia</option>
                                    <option value="Salsa">Salsa</option>
                                    <option value="Jazz">Jazz</option>
                                    <option value="Electrónica">Electrónica</option>
                                    <option value="Folk">Folk</option>
                                    <option value="Clásica">Clásica</option>
                                    <option value="Otros">Otros</option>
                                </select>
                                <small class="help-block">Estilo musical principal del artista</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Descripción</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="4"
                                    placeholder="Biografía y descripción del artista..."></textarea>
                                <small class="help-block">Biografía y características del artista</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Texto de Presentación</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="presentacion" id="presentacion" rows="4"
                                    placeholder="Texto para cotizaciones y propuestas..."></textarea>
                                <small class="help-block">Este texto se usará en cotizaciones y propuestas comerciales</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-image m-r-5"></i> Imágenes del Artista</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Imagen de Presentación</label>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Seleccionar</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="imagen_presentacion" accept="image/jpeg,image/png,image/gif">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                                </div>
                                <small class="help-block">Imagen principal del artista (JPG, PNG, GIF - máx. 5MB)</small>
                                <div class="preview-image mt-2" id="preview-imagen-presentacion"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Logo del Artista</label>
                            <div class="col-md-6">
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Seleccionar</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="logo_artista" accept="image/jpeg,image/png,image/gif">
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Quitar</a>
                                </div>
                                <small class="help-block">Logo o imagen de marca del artista (JPG, PNG, GIF - máx. 5MB)</small>
                                <div class="preview-image mt-2" id="preview-logo-artista"></div>
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
                                <small class="help-block">Los artistas inactivos no aparecerán en las búsquedas</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-group m-b-0 text-center">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="fa fa-check"></i> Guardar Artista
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

<!-- JavaScript para la validación y comportamiento del formulario -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vista previa de imágenes al cargar los archivos
        function setupImagePreview(inputName, previewId) {
            const input = document.querySelector('input[name="' + inputName + '"]');
            const preview = document.getElementById(previewId);
            
            input.addEventListener('change', function() {
                preview.innerHTML = '';
                
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    
                    // Validar tamaño (máximo 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('El archivo es demasiado grande. El tamaño máximo permitido es 5MB.');
                        this.value = '';
                        return;
                    }
                    
                    // Validar tipo
                    if (!file.type.match('image/jpeg') && !file.type.match('image/png') && !file.type.match('image/gif')) {
                        alert('Solo se permiten archivos JPG, PNG o GIF.');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail';
                        img.style.maxHeight = '150px';
                        img.style.marginTop = '10px';
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Configurar vista previa para ambas imágenes
        setupImagePreview('imagen_presentacion', 'preview-imagen-presentacion');
        setupImagePreview('logo_artista', 'preview-logo-artista');
        
        // Validación del formulario antes de enviar
        document.getElementById('formCrearArtista').addEventListener('submit', function(event) {
            const nombre = document.getElementById('nombre').value.trim();
            const generoMusical = document.getElementById('genero_musical').value;
            
            if (nombre === '') {
                event.preventDefault();
                alert('El nombre artístico es obligatorio');
                document.getElementById('nombre').focus();
                return false;
            }
            
            if (generoMusical === '') {
                event.preventDefault();
                alert('Debe seleccionar un género musical');
                document.getElementById('genero_musical').focus();
                return false;
            }
            
            // Preparar los textos para enviar
            document.getElementById('descripcion').value = document.getElementById('descripcion').value.trim();
            document.getElementById('presentacion').value = document.getElementById('presentacion').value.trim();
        });
        
        // Verificar compatibilidad con FileReader para vistas previas
        if (typeof FileReader === 'undefined') {
            document.querySelectorAll('.preview-image').forEach(function(element) {
                element.style.display = 'none';
            });
            
            // Mostrar mensaje de advertencia
            const fileInputs = document.querySelectorAll('.fileinput');
            fileInputs.forEach(function(input) {
                const helpBlock = input.parentNode.querySelector('.help-block');
                helpBlock.innerHTML += '<br><span class="text-warning">Su navegador no soporta la vista previa de imágenes.</span>';
            });
        }
    });

    // Función para confirmar cancelación
    function confirmCancel() {
        if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
            window.location.href = '<?= base_url ?>artistas/index';
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

    .mt-2 {
        margin-top: 8px;
    }

    .text-center {
        text-align: center;
    }

    .preview-image {
        min-height: 20px;
    }

    .fileinput .thumbnail {
        display: inline-block;
        margin-bottom: 10px;
        overflow: hidden;
        text-align: center;
        vertical-align: middle;
    }

    .fileinput-exists .fileinput-new, 
    .fileinput-new .fileinput-exists {
        display: none;
    }
    
    .btn-file {
        position: relative;
        overflow: hidden;
    }
    
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>