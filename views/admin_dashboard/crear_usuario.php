<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Crear Nuevo Usuario</h3>
            <p class="text-muted">Ingresa los datos del nuevo usuario para el sistema</p>

            <!-- Mensajes de error o éxito -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error_message'] ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Formulario de creación de usuario - Notar que ahora apunta a redirectAfterSave -->
            <form id="formCrearUsuario" class="form-horizontal m-t-30" method="post" action="<?= base_url ?>systemDashboard/redirectAfterSave">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <!-- Información personal -->
                <div class="form-group">
                    <label class="col-md-3 control-label">Nombre *</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Apellido *</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="apellido" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Email *</label>
                    <div class="col-md-6">
                        <input type="email" class="form-control" name="email" required>
                        <small class="text-muted">Este email será utilizado para iniciar sesión</small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Teléfono</label>
                    <div class="col-md-6">
                        <input type="tel" class="form-control" name="telefono">
                    </div>
                </div>

                <!-- Localización -->
                <div class="form-group">
                    <label class="col-md-3 control-label">País *</label>
                    <div class="col-md-6">
                        <select class="form-control" name="pais" id="pais" required>
                            <option value="Chile">Chile</option>
                            <option value="Argentina">Argentina</option>
                            <option value="México">México</option>
                            <option value="Colombia">Colombia</option>
                            <option value="Perú">Perú</option>
                            <option value="España">España</option>
                            <option value="Estados Unidos">Estados Unidos</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Identificación</label>
                    <div class="col-md-3">
                        <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                            <option value="RUT">RUT</option>
                            <option value="DNI">DNI</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="SSN">SSN</option>
                            <option value="NIF">NIF</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="numero_identificacion" placeholder="Número de identificación">
                    </div>
                </div>

                <!-- Acceso y permisos -->
                <div class="form-group">
                    <label class="col-md-3 control-label">Tipo de Usuario *</label>
                    <div class="col-md-6">
                        <select class="form-control" name="tipo_usuario" required>
                            <option value="ADMIN">Administrador</option>
                            <!-- Opciones eliminadas para simplificar el proceso -->
                            <!-- <option value="VENDEDOR">Vendedor</option> -->
                            <!-- <option value="TOUR_MANAGER">Tour Manager</option> -->
                        </select>
                        <small class="text-muted">El superadmin solo puede crear cuentas de administrador</small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Estado *</label>
                    <div class="col-md-6">
                        <select class="form-control" name="estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <label class="col-md-3 control-label">Contraseña *</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" name="password" id="password" required>
                        <small class="text-muted">Mínimo 8 caracteres, incluyendo mayúsculas, minúsculas y números</small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Confirmar Contraseña *</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>

                <!-- Notificaciones -->
                <div class="form-group">
                    <label class="col-md-3 control-label">Notificaciones</label>
                    <div class="col-md-6">
                        <div class="checkbox checkbox-success">
                            <input id="notif_email" name="notif_email" type="checkbox" checked>
                            <label for="notif_email">Recibir notificaciones por email</label>
                        </div>
                        <div class="checkbox checkbox-success">
                            <input id="notif_sistema" name="notif_sistema" type="checkbox" checked>
                            <label for="notif_sistema">Recibir notificaciones en el sistema</label>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-group m-b-0">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-info waves-effect waves-light m-r-10">
                            <i class="fa fa-check"></i> Guardar Usuario
                        </button>
                        <a href="<?= base_url ?>systemDashboard/usuarios" class="btn btn-default waves-effect waves-light">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts para validación y comportamiento dinámico -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para actualizar los tipos de identificación según el país
    function actualizarTiposIdentificacion() {
        const pais = document.getElementById('pais').value;
        const tipoIdentificacion = document.getElementById('tipo_identificacion');
        
        // Resetear opciones
        tipoIdentificacion.innerHTML = '';
        
        // Definir opciones por país
        const opciones = {
            'Chile': [
                {valor: 'RUT', texto: 'RUT'}
            ],
            'Argentina': [
                {valor: 'DNI', texto: 'DNI'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'México': [
                {valor: 'CURP', texto: 'CURP'},
                {valor: 'RFC', texto: 'RFC'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Colombia': [
                {valor: 'CC', texto: 'Cédula de Ciudadanía'},
                {valor: 'CE', texto: 'Cédula de Extranjería'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Perú': [
                {valor: 'DNI', texto: 'DNI'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'España': [
                {valor: 'NIE', texto: 'NIE'},
                {valor: 'NIF', texto: 'NIF'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ],
            'Estados Unidos': [
                {valor: 'SSN', texto: 'SSN'},
                {valor: 'Pasaporte', texto: 'Pasaporte'}
            ]
        };
        
        // Opción por defecto si no hay opciones específicas
        let opcionesPais = opciones[pais] || [
            {valor: 'Pasaporte', texto: 'Pasaporte'},
            {valor: 'ID', texto: 'Documento de Identidad'}
        ];
        
        // Agregar opciones al select
        opcionesPais.forEach(function(opcion) {
            const option = document.createElement('option');
            option.value = opcion.valor;
            option.textContent = opcion.texto;
            tipoIdentificacion.appendChild(option);
        });
    }
    
    // Validación de contraseñas
    function validarContrasena() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Verificar que coincidan
        if (password !== confirmPassword) {
            document.getElementById('confirm_password').setCustomValidity('Las contraseñas no coinciden');
        } else {
            document.getElementById('confirm_password').setCustomValidity('');
        }
        
        // Verificar fortaleza
        const regexMayuscula = /[A-Z]/;
        const regexMinuscula = /[a-z]/;
        const regexNumero = /[0-9]/;
        
        if (password.length < 8 || 
            !regexMayuscula.test(password) ||
            !regexMinuscula.test(password) ||
            !regexNumero.test(password)) {
            document.getElementById('password').setCustomValidity('La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número');
        } else {
            document.getElementById('password').setCustomValidity('');
        }
    }
    
    // Configurar eventos
    document.getElementById('pais').addEventListener('change', actualizarTiposIdentificacion);
    document.getElementById('password').addEventListener('input', validarContrasena);
    document.getElementById('confirm_password').addEventListener('input', validarContrasena);
    
    // Cargar valores iniciales
    actualizarTiposIdentificacion();
    
    // Validación del formulario antes de enviar
    document.getElementById('formCrearUsuario').addEventListener('submit', function(event) {
        // Validar contraseñas
        validarContrasena();
        
        // Si hay errores de validación, evitar envío del formulario
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            
            // Mostrar mensaje de error
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Por favor, revisa los campos marcados y completa la información requerida',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                alert('Por favor, revisa los campos marcados y completa la información requerida');
            }
        }
    });
});
</script>