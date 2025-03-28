<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Configuración del Sistema</h3>
            <p class="text-muted">Administra los parámetros generales y configuraciones del sistema</p>
            
            <!-- Menú de navegación para secciones -->
            <div class="row m-t-20">
                <div class="col-md-12">
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#tab_general" data-toggle="tab">
                                <span class="visible-xs"><i class="fa fa-cog"></i></span>
                                <span class="hidden-xs">General</span>
                            </a>
                        </li>
                        <li class="tab">
                            <a href="#tab_seguridad" data-toggle="tab">
                                <span class="visible-xs"><i class="fa fa-lock"></i></span>
                                <span class="hidden-xs">Seguridad</span>
                            </a>
                        </li>
                        <li class="tab">
                            <a href="#tab_email" data-toggle="tab">
                                <span class="visible-xs"><i class="fa fa-envelope"></i></span>
                                <span class="hidden-xs">Correo Electrónico</span>
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Contenido de las pestañas -->
                    <div class="tab-content">
                        <!-- Configuración General -->
                        <div class="tab-pane active" id="tab_general">
                            <form id="formGeneral" class="form-horizontal m-t-30">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Nombre de la Aplicación</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="app_name" value="Cubic Cloud">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">URL de la Aplicación</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="app_url" value="http://localhost/cubic-cloud/">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Zona Horaria</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="app_timezone">
                                            <option value="America/Santiago" selected>America/Santiago</option>
                                            <option value="America/Buenos_Aires">America/Buenos_Aires</option>
                                            <option value="America/Lima">America/Lima</option>
                                            <option value="America/Bogota">America/Bogota</option>
                                            <option value="America/Mexico_City">America/Mexico_City</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Idioma por Defecto</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="app_language">
                                            <option value="es" selected>Español</option>
                                            <option value="en">English</option>
                                            <option value="pt">Português</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Moneda por Defecto</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="app_currency">
                                            <option value="CLP" selected>CLP (Peso Chileno)</option>
                                            <option value="USD">USD (Dólar Estadounidense)</option>
                                            <option value="EUR">EUR (Euro)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <button type="button" class="btn btn-info" id="btnGuardarGeneral">
                                            <i class="fa fa-check"></i> Guardar Configuración
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Configuración de Seguridad -->
                        <div class="tab-pane" id="tab_seguridad">
                            <form id="formSeguridad" class="form-horizontal m-t-30">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Tiempo de Sesión (segundos)</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="session_timeout" value="3600">
                                        <small class="text-muted">Tiempo hasta que una sesión inactiva expire (3600 = 1 hora)</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Duración Cookie "Recuérdame" (días)</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="cookie_lifetime" value="30">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Intentos Máximos de Login</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="max_login_attempts" value="5">
                                        <small class="text-muted">Intentos fallidos antes de bloquear la cuenta</small>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Política de Contraseñas</label>
                                    <div class="col-md-6">
                                        <div class="checkbox checkbox-info">
                                            <input id="req_uppercase" name="req_uppercase" type="checkbox" checked>
                                            <label for="req_uppercase">Requerir al menos una letra mayúscula</label>
                                        </div>
                                        <div class="checkbox checkbox-info">
                                            <input id="req_lowercase" name="req_lowercase" type="checkbox" checked>
                                            <label for="req_lowercase">Requerir al menos una letra minúscula</label>
                                        </div>
                                        <div class="checkbox checkbox-info">
                                            <input id="req_number" name="req_number" type="checkbox" checked>
                                            <label for="req_number">Requerir al menos un número</label>
                                        </div>
                                        <div class="form-group" style="margin-top: 10px; margin-bottom: 0;">
                                            <label>Longitud mínima:</label>
                                            <input type="number" class="form-control" name="min_password_length" value="8">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Protección CSRF</label>
                                    <div class="col-md-6">
                                        <input type="checkbox" name="csrf_protection" checked class="js-switch" data-color="#26c6da" data-size="small">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <button type="button" class="btn btn-info" id="btnGuardarSeguridad">
                                            <i class="fa fa-check"></i> Guardar Configuración
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Configuración de Correo Electrónico -->
                        <div class="tab-pane" id="tab_email">
                            <form id="formEmail" class="form-horizontal m-t-30">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Servidor SMTP</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="smtp_server" value="smtp.example.com">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Puerto SMTP</label>
                                    <div class="col-md-6">
                                        <input type="number" class="form-control" name="smtp_port" value="587">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Seguridad</label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="smtp_security">
                                            <option value="">Ninguna</option>
                                            <option value="ssl">SSL</option>
                                            <option value="tls" selected>TLS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Usuario SMTP</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="smtp_username" value="usuario@example.com">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Contraseña SMTP</label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="smtp_password" value="********">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Email Remitente</label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email_from" value="no-reply@example.com">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Nombre Remitente</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="email_from_name" value="Cubic Cloud">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <button type="button" class="btn btn-info m-r-10" id="btnGuardarEmail">
                                            <i class="fa fa-check"></i> Guardar Configuración
                                        </button>
                                        <button type="button" class="btn btn-default" id="btnTestEmail">
                                            <i class="fa fa-envelope"></i> Enviar Email de Prueba
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para la funcionalidad de la página -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar switchery si está disponible
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    elems.forEach(function(html) {
        if (typeof Switchery !== 'undefined') {
            new Switchery(html, { color: html.dataset.color || '#26c6da', size: html.dataset.size || 'default' });
        }
    });
    
    // Configurar eventos para botones de guardar
    document.getElementById('btnGuardarGeneral').addEventListener('click', function() {
        guardarConfiguracion('general');
    });
    
    document.getElementById('btnGuardarSeguridad').addEventListener('click', function() {
        guardarConfiguracion('seguridad');
    });
    
    document.getElementById('btnGuardarEmail').addEventListener('click', function() {
        guardarConfiguracion('email');
    });
    
    // Configurar evento para envío de email de prueba
    document.getElementById('btnTestEmail').addEventListener('click', function() {
        enviarEmailPrueba();
    });
});

// Función para guardar configuración (simulada)
function guardarConfiguracion(seccion) {
    // Mostrar indicador de carga
    Swal.fire({
        title: 'Guardando...',
        text: 'Espere mientras se guarda la configuración',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Simular proceso de guardado
    setTimeout(function() {
        Swal.fire({
            icon: 'success',
            title: 'Configuración guardada',
            text: 'Los cambios han sido guardados correctamente',
            timer: 2000,
            showConfirmButton: false
        });
    }, 1000);
}

// Función para enviar email de prueba (simulada)
function enviarEmailPrueba() {
    // Solicitar email de destino
    Swal.fire({
        title: 'Enviar email de prueba',
        input: 'email',
        inputLabel: 'Dirección de email para la prueba',
        inputPlaceholder: 'Ingrese una dirección de email',
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (email) => {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve();
                }, 1500);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Email enviado',
                text: 'Se ha enviado un email de prueba correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}
</script>

<!-- Estilos adicionales -->
<style>
.tab-content {
    padding: 20px;
    border: 1px solid #eaeaea;
    border-top: none;
}
.nav-tabs.customtab > li.active > a, 
.nav-tabs.customtab > li.active > a:hover, 
.nav-tabs.customtab > li.active > a:focus {
    border-bottom: 2px solid #26c6da;
    color: #26c6da;
}
.checkbox label::before {
    border: 1px solid #cccccc;
}
</style>