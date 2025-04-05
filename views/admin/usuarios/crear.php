<?php
// Aseguramos que se ha cargado el helper de autenticación
if (!function_exists('isAdminLoggedIn')) {
    require_once 'helpers/auth_helper.php';
}

// Verificar que el usuario sea admin
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Definir título de la página
$pageTitle = "Crear Usuario";
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Nuevo Usuario</h3>
                <p class="text-muted m-b-30">Complete el formulario para registrar un nuevo usuario en el sistema</p>
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

            <!-- Formulario de creación de usuario -->
            <form id="formCrearUsuario" class="form-horizontal" method="post" action="<?= base_url ?>admin/saveUsuario">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-user m-r-5"></i> Información Personal</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Nombre <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="nombre" id="nombre" required 
                                       placeholder="Ingrese el nombre">
                                <small class="help-block">Nombre del usuario</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Apellido <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="apellido" id="apellido" required 
                                       placeholder="Ingrese el apellido">
                                <small class="help-block">Apellido del usuario</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Email <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" id="email" required 
                                       placeholder="ejemplo@dominio.com">
                                <small class="help-block">Esta dirección se usará para iniciar sesión y recuperar contraseña</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Teléfono</label>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" name="telefono" id="telefono" 
                                       placeholder="+56 9 1234 5678">
                                <small class="help-block">Opcional: Número de contacto</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-map-marker m-r-5"></i> Localización e Identificación</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">País <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="pais" id="pais" required>
                                    <option value="">Seleccione un país...</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="Colombia">Colombia</option>
                                    <option value="México">México</option>
                                    <option value="Perú">Perú</option>
                                    <option value="España">España</option>
                                    <option value="Estados Unidos">Estados Unidos</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Documento de Identidad</label>
                            <div class="col-md-3">
                                <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                                    <option value="RUT">RUT (Chile)</option>
                                    <option value="DNI">DNI</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="SSN">SSN</option>
                                    <option value="NIF">NIF</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="numero_identificacion" id="numero_identificacion" 
                                       placeholder="Número de identificación">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-key m-r-5"></i> Acceso al Sistema</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Tipo de Usuario <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="hidden" name="tipo_usuario" value="ADMIN">
                                <input type="text" class="form-control" value="Administrador" readonly>
                                <small class="help-block">Este usuario tendrá permisos de administrador</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Estado <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado" id="estado" required>
                                    <option value="Activo" selected>Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                                <small class="help-block">El usuario inactivo no podrá iniciar sesión</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" required minlength="8">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="togglePassword">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class="password-strength mt-2" id="password-strength">
                                    <div class="progress" style="height: 6px; margin-bottom: 2px;">
                                        <div class="progress-bar" role="progressbar" style="width: 0%;" id="password-strength-bar"></div>
                                    </div>
                                    <small id="password-strength-text">Seguridad de la contraseña</small>
                                </div>
                                <small class="help-block">Mínimo 8 caracteres, debe incluir mayúsculas, minúsculas y números</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="8">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="toggleConfirmPassword">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </span>
                                </div>
                                <small id="password-match" class="help-block"></small>
                            </div>
                        </div>


                    </div>
                </div>



                <!-- Botones de acción -->
                <div class="form-group m-b-0">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="fa fa-check"></i> Guardar Usuario
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
    // Variables para los campos de contraseña
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const passwordStrengthBar = document.getElementById('password-strength-bar');
    const passwordStrengthText = document.getElementById('password-strength-text');
    const passwordMatch = document.getElementById('password-match');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    
    // Mostrar/ocultar contraseña
    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Función para actualizar los tipos de identificación según el país
    function actualizarTiposIdentificacion() {
        const pais = document.getElementById('pais').value;
        const tipoIdentificacion = document.getElementById('tipo_identificacion');
        
        // Limpiar el select
        tipoIdentificacion.innerHTML = '';
        
        // Definir opciones por país
        const opciones = {
            'Chile': [
                {valor: 'RUT', texto: 'RUT (Chile)'}
            ],
            'Argentina': [
                {valor: 'DNI', texto: 'DNI (Argentina)'},
                {valor: 'CUIT', texto: 'CUIT'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Colombia': [
                {valor: 'CC', texto: 'Cédula de Ciudadanía'},
                {valor: 'CE', texto: 'Cédula de Extranjería'},
                {valor: 'NIT', texto: 'NIT'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'México': [
                {valor: 'CURP', texto: 'CURP'},
                {valor: 'RFC', texto: 'RFC'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Perú': [
                {valor: 'DNI', texto: 'DNI (Perú)'},
                {valor: 'RUC', texto: 'RUC'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'España': [
                {valor: 'DNI', texto: 'DNI (España)'},
                {valor: 'NIE', texto: 'NIE'},
                {valor: 'CIF', texto: 'CIF'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Estados Unidos': [
                {valor: 'SSN', texto: 'Social Security Number'},
                {valor: 'EIN', texto: 'Employer ID Number'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ]
        };
        
        // Opciones por defecto si no hay opciones específicas para el país
        let opcionesPais = opciones[pais] || [
            {valor: 'ID', texto: 'Documento de Identidad'},
            {valor: 'Pasaporte', texto: 'Pasaporte'}
        ];
        
        // Añadir opciones al select
        opcionesPais.forEach(function(opcion) {
            const option = document.createElement('option');
            option.value = opcion.valor;
            option.textContent = opcion.texto;
            tipoIdentificacion.appendChild(option);
        });
    }
    
    // Función para evaluar la fortaleza de la contraseña
    function evaluarFortalezaPassword(pass) {
        let score = 0;
        
        // Longitud mínima
        if (pass.length >= 8) score += 20;
        
        // Letras mayúsculas y minúsculas
        if (/[A-Z]/.test(pass)) score += 20;
        if (/[a-z]/.test(pass)) score += 20;
        
        // Números
        if (/\d/.test(pass)) score += 20;
        
        // Caracteres especiales
        if (/[^A-Za-z0-9]/.test(pass)) score += 20;
        
        return score;
    }
    
    // Función para actualizar la visualización de la fortaleza de la contraseña
    function actualizarFortalezaPassword() {
        const value = password.value;
        const score = evaluarFortalezaPassword(value);
        
        passwordStrengthBar.style.width = score + '%';
        
        // Cambiar color según fortaleza
        if (score >= 80) {
            passwordStrengthBar.className = 'progress-bar progress-bar-success';
            passwordStrengthText.textContent = 'Contraseña muy fuerte';
            passwordStrengthText.className = 'text-success';
        } else if (score >= 60) {
            passwordStrengthBar.className = 'progress-bar progress-bar-info';
            passwordStrengthText.textContent = 'Contraseña fuerte';
            passwordStrengthText.className = 'text-info';
        } else if (score >= 40) {
            passwordStrengthBar.className = 'progress-bar progress-bar-warning';
            passwordStrengthText.textContent = 'Contraseña moderada';
            passwordStrengthText.className = 'text-warning';
        } else {
            passwordStrengthBar.className = 'progress-bar progress-bar-danger';
            passwordStrengthText.textContent = 'Contraseña débil';
            passwordStrengthText.className = 'text-danger';
        }
    }
    
    // Función para verificar si las contraseñas coinciden
    function verificarPasswordsCoinciden() {
        if (confirmPassword.value === '') {
            passwordMatch.textContent = '';
            passwordMatch.className = 'help-block';
            return;
        }
        
        if (password.value === confirmPassword.value) {
            passwordMatch.textContent = 'Las contraseñas coinciden';
            passwordMatch.className = 'help-block text-success';
            confirmPassword.setCustomValidity('');
        } else {
            passwordMatch.textContent = 'Las contraseñas no coinciden';
            passwordMatch.className = 'help-block text-danger';
            confirmPassword.setCustomValidity('Las contraseñas no coinciden');
        }
    }
    
    // Registrar eventos
    document.getElementById('pais').addEventListener('change', actualizarTiposIdentificacion);
    password.addEventListener('input', function() {
        actualizarFortalezaPassword();
        verificarPasswordsCoinciden();
    });
    confirmPassword.addEventListener('input', verificarPasswordsCoinciden);
    
    // Validación del formulario antes de enviar
    document.getElementById('formCrearUsuario').addEventListener('submit', function(event) {
        // Verificar si las contraseñas coinciden
        if (password.value !== confirmPassword.value) {
            event.preventDefault();
            passwordMatch.textContent = 'Las contraseñas no coinciden';
            passwordMatch.className = 'help-block text-danger';
            confirmPassword.focus();
            return false;
        }
        
        // Verificar fortaleza de la contraseña
        const score = evaluarFortalezaPassword(password.value);
        if (score < 60) {
            if (!confirm('La contraseña no es muy segura. ¿Desea continuar de todos modos?')) {
                event.preventDefault();
                password.focus();
                return false;
            }
        }
    });
    
    // Inicializar el formulario
    actualizarTiposIdentificacion();
});

// Función para confirmar cancelación
function confirmCancel() {
    if (confirm('¿Está seguro que desea cancelar? Los cambios no guardados se perderán.')) {
        window.location.href = '<?= base_url ?>admin/usuarios';
    }
}
</script>

<!-- Estilos personalizados -->
<style>
.panel {
    border-radius: 5px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
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
.checkbox {
    margin-top: 0;
}
.checkbox label {
    padding-left: 25px;
}
.checkbox input[type="checkbox"] {
    margin-left: -25px;
}
.password-strength {
    margin-top: 5px;
}
.mt-2 {
    margin-top: 8px;
}
</style>