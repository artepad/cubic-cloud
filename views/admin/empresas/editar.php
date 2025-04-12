<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Verificar que existe la empresa
if (!isset($empresa) || !$empresa) {
    $_SESSION['error_message'] = "Empresa no encontrada";
    redirectTo('admin/empresas');
}

// Cargar modelo de Usuario para obtener los administradores disponibles
$usuario_model = new Usuario();
$administradores = $usuario_model->getAll(['tipo_usuario' => 'ADMIN', 'estado' => 'Activo']);
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Empresa</h3>
                <p class="text-muted m-b-30">Modifique los datos de la empresa</p>
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

            <!-- Formulario de edición de empresa -->
            <form id="formEditarEmpresa" class="form-horizontal" method="post" action="<?= base_url ?>empresa/actualizar" enctype="multipart/form-data">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="id" value="<?= $empresa->id ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-user-circle m-r-5"></i> Administrador Asociado</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Administrador <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="usuario_id" id="usuario_id" required>
                                    <option value="">Seleccione un administrador...</option>
                                    <?php if (!empty($administradores)): ?>
                                        <?php foreach ($administradores as $admin): ?>
                                            <option value="<?= $admin->id ?>" <?= $admin->id == $empresa->usuario_id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($admin->nombre . ' ' . $admin->apellido . ' (' . $admin->email . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <small class="help-block">Seleccione el usuario que administrará esta empresa</small>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url ?>admin/crearUsuario" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Crear Nuevo Administrador
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-building m-r-5"></i> Información Básica</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Nombre <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="nombre" id="nombre" required 
                                       value="<?= htmlspecialchars($empresa->nombre) ?>"
                                       placeholder="Nombre o razón social de la empresa">
                                <small class="help-block">Nombre oficial o razón social de la empresa</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Identificación Fiscal</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="identificacion_fiscal" id="identificacion_fiscal" 
                                       value="<?= htmlspecialchars($empresa->identificacion_fiscal) ?>"
                                       placeholder="RUT, NIF, EIN, etc.">
                                <small class="help-block">Identificación fiscal según el país (RUT, NIF, EIN, etc.)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Dirección <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="direccion" id="direccion" required 
                                       value="<?= htmlspecialchars($empresa->direccion) ?>"
                                       placeholder="Dirección física de la empresa">
                                <small class="help-block">Dirección completa, incluyendo ciudad y código postal</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Teléfono</label>
                            <div class="col-md-6">
                                <input type="tel" class="form-control" name="telefono" id="telefono" 
                                       value="<?= htmlspecialchars($empresa->telefono) ?>"
                                       placeholder="+56 9 1234 5678">
                                <small class="help-block">Número de contacto principal de la empresa</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Email de Contacto</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email_contacto" id="email_contacto" 
                                       value="<?= htmlspecialchars($empresa->email_contacto) ?>"
                                       placeholder="contacto@empresa.com">
                                <small class="help-block">Email de contacto principal</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-globe m-r-5"></i> Localización</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">País <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="pais" id="pais" required>
                                    <option value="">Seleccione un país...</option>
                                    <option value="Chile" data-codigo="CL" <?= $empresa->pais == 'Chile' ? 'selected' : '' ?>>Chile</option>
                                    <option value="Argentina" data-codigo="AR" <?= $empresa->pais == 'Argentina' ? 'selected' : '' ?>>Argentina</option>
                                    <option value="Colombia" data-codigo="CO" <?= $empresa->pais == 'Colombia' ? 'selected' : '' ?>>Colombia</option>
                                    <option value="México" data-codigo="MX" <?= $empresa->pais == 'México' ? 'selected' : '' ?>>México</option>
                                    <option value="Perú" data-codigo="PE" <?= $empresa->pais == 'Perú' ? 'selected' : '' ?>>Perú</option>
                                    <option value="España" data-codigo="ES" <?= $empresa->pais == 'España' ? 'selected' : '' ?>>España</option>
                                    <option value="Estados Unidos" data-codigo="US" <?= $empresa->pais == 'Estados Unidos' ? 'selected' : '' ?>>Estados Unidos</option>
                                    <option value="Otro" data-codigo="OT" <?= $empresa->pais == 'Otro' ? 'selected' : '' ?>>Otro</option>
                                </select>
                                <input type="hidden" name="codigo_pais" id="codigo_pais" value="<?= htmlspecialchars($empresa->codigo_pais) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-credit-card m-r-5"></i> Datos de Facturación</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Razón Social</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="razon_social_facturacion" id="razon_social_facturacion" 
                                       value="<?= htmlspecialchars($empresa->razon_social_facturacion ?? '') ?>"
                                       placeholder="Razón social para facturación">
                                <small class="help-block">Nombre para facturación (si es diferente al nombre de la empresa)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Dirección de Facturación</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="direccion_facturacion" id="direccion_facturacion" 
                                       value="<?= htmlspecialchars($empresa->direccion_facturacion ?? '') ?>"
                                       placeholder="Dirección para facturación">
                                <small class="help-block">Dirección a la que se enviarán las facturas</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Ciudad</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="ciudad_facturacion" id="ciudad_facturacion" 
                                       value="<?= htmlspecialchars($empresa->ciudad_facturacion ?? '') ?>"
                                       placeholder="Ciudad para facturación">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Código Postal</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="codigo_postal" id="codigo_postal" 
                                       value="<?= htmlspecialchars($empresa->codigo_postal ?? '') ?>"
                                       placeholder="Código postal para facturación">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Email de Facturación</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email_facturacion" id="email_facturacion" 
                                       value="<?= htmlspecialchars($empresa->email_facturacion ?? '') ?>"
                                       placeholder="facturacion@empresa.com">
                                <small class="help-block">Email al que se enviarán los comprobantes de pago</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Contacto de Facturación</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="contacto_facturacion" id="contacto_facturacion" 
                                       value="<?= htmlspecialchars($empresa->contacto_facturacion ?? '') ?>"
                                       placeholder="Nombre del contacto para facturación">
                                <small class="help-block">Persona de contacto para temas de facturación</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-image m-r-5"></i> Recursos Visuales</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Logo Principal</label>
                            <div class="col-md-6">
                                <?php if (!empty($empresa->imagen_empresa)): ?>
                                    <div class="m-b-10">
                                        <img src="<?= base_url . $empresa->imagen_empresa ?>" alt="Logo" class="img-thumbnail" style="max-height:100px">
                                        <div class="checkbox checkbox-danger m-t-10">
                                            <input id="eliminar_imagen_empresa" name="eliminar_imagen_empresa" type="checkbox" value="1">
                                            <label for="eliminar_imagen_empresa">Eliminar imagen actual</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="imagen_empresa" id="imagen_empresa" accept="image/*">
                                <small class="help-block">Logo principal de la empresa (formato recomendado: PNG, tamaño máximo: 2MB)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Logo para Documentos</label>
                            <div class="col-md-6">
                                <?php if (!empty($empresa->imagen_documento)): ?>
                                    <div class="m-b-10">
                                        <img src="<?= base_url . $empresa->imagen_documento ?>" alt="Logo Documentos" class="img-thumbnail" style="max-height:100px">
                                        <div class="checkbox checkbox-danger m-t-10">
                                            <input id="eliminar_imagen_documento" name="eliminar_imagen_documento" type="checkbox" value="1">
                                            <label for="eliminar_imagen_documento">Eliminar imagen actual</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="imagen_documento" id="imagen_documento" accept="image/*">
                                <small class="help-block">Imagen para membrete de documentos (formato recomendado: PNG, tamaño máximo: 2MB)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Firma Digital</label>
                            <div class="col-md-6">
                                <?php if (!empty($empresa->imagen_firma)): ?>
                                    <div class="m-b-10">
                                        <img src="<?= base_url . $empresa->imagen_firma ?>" alt="Firma" class="img-thumbnail" style="max-height:100px">
                                        <div class="checkbox checkbox-danger m-t-10">
                                            <input id="eliminar_imagen_firma" name="eliminar_imagen_firma" type="checkbox" value="1">
                                            <label for="eliminar_imagen_firma">Eliminar imagen actual</label>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <input type="file" class="form-control" name="imagen_firma" id="imagen_firma" accept="image/*">
                                <small class="help-block">Firma digital para documentos (formato recomendado: PNG, tamaño máximo: 2MB)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-cogs m-r-5"></i> Configuración</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Moneda <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="tipo_moneda" id="tipo_moneda" required>
                                    <option value="CLP" <?= $empresa->tipo_moneda == 'CLP' ? 'selected' : '' ?>>CLP (Peso Chileno)</option>
                                    <option value="USD" <?= $empresa->tipo_moneda == 'USD' ? 'selected' : '' ?>>USD (Dólar Estadounidense)</option>
                                    <option value="EUR" <?= $empresa->tipo_moneda == 'EUR' ? 'selected' : '' ?>>EUR (Euro)</option>
                                </select>
                                <small class="help-block">Moneda principal para operaciones de la empresa</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-sliders m-r-5"></i> Configuración Adicional</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Estado</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado" id="estado">
                                    <option value="activa" <?= $empresa->estado == 'activa' ? 'selected' : '' ?>>Activa</option>
                                    <option value="suspendida" <?= $empresa->estado == 'suspendida' ? 'selected' : '' ?>>Suspendida</option>
                                </select>
                                <small class="help-block">Estado de la empresa</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Cuenta Demo</label>
                            <div class="col-md-6">
                                <div class="checkbox checkbox-info">
                                    <input id="es_demo" name="es_demo" type="checkbox" value="Si" <?= $empresa->es_demo == 'Si' ? 'checked' : '' ?>>
                                    <label for="es_demo">Es una cuenta de demostración</label>
                                </div>
                                <small class="help-block">Marque si esta es una cuenta de prueba con tiempo limitado</small>
                            </div>
                        </div>
                        <div id="demo_fechas" style="display:<?= $empresa->es_demo == 'Si' ? 'block' : 'none' ?>;">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Inicio de Demo</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="demo_inicio" id="demo_inicio" 
                                           value="<?= $empresa->demo_inicio ? date('Y-m-d', strtotime($empresa->demo_inicio)) : date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label">Fin de Demo</label>
                                <div class="col-md-6">
                                    <input type="date" class="form-control" name="demo_fin" id="demo_fin" 
                                           value="<?= $empresa->demo_fin ? date('Y-m-d', strtotime($empresa->demo_fin)) : date('Y-m-d', strtotime('+30 days')) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-group m-b-0">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="fa fa-check"></i> Guardar Cambios
                        </button>
                        <a href="<?= base_url ?>admin/empresas" class="btn btn-default waves-effect waves-light m-l-10">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript para la validación y comportamiento del formulario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar el código de país automáticamente al seleccionar un país
    const paisSelect = document.getElementById('pais');
    const codigoPaisInput = document.getElementById('codigo_pais');
    
    paisSelect.addEventListener('change', function() {
        const selectedOption = paisSelect.options[paisSelect.selectedIndex];
        const codigoPais = selectedOption.getAttribute('data-codigo');
        codigoPaisInput.value = codigoPais || '';
    });
    
    // Mostrar/ocultar fechas de demo
    const esDemo = document.getElementById('es_demo');
    const demoFechas = document.getElementById('demo_fechas');
    
    esDemo.addEventListener('change', function() {
        demoFechas.style.display = esDemo.checked ? 'block' : 'none';
    });
    
    // Validar formulario antes de enviar
    document.getElementById('formEditarEmpresa').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const usuarioId = document.getElementById('usuario_id').value;
        const direccion = document.getElementById('direccion').value.trim();
        const pais = document.getElementById('pais').value;
        
        let isValid = true;
        let errorMessage = '';
        
        // Validar campos obligatorios
        if (!nombre) {
            errorMessage = 'El nombre de la empresa es obligatorio';
            isValid = false;
        } else if (!usuarioId) {
            errorMessage = 'Debe seleccionar un administrador';
            isValid = false;
        } else if (!direccion) {
            errorMessage = 'La dirección es obligatoria';
            isValid = false;
        } else if (!pais) {
            errorMessage = 'Debe seleccionar un país';
            isValid = false;
        }
        
        // Validar fechas de demo si es cuenta demo
        if (esDemo.checked) {
            const demoInicio = new Date(document.getElementById('demo_inicio').value);
            const demoFin = new Date(document.getElementById('demo_fin').value);
            
            if (demoFin <= demoInicio) {
                errorMessage = 'La fecha de fin de demo debe ser posterior a la fecha de inicio';
                isValid = false;
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
    });
});
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
</style>