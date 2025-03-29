<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Verificar que existe el plan
if (!isset($plan) || !$plan) {
    redirectTo('plan/index');
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <h3 class="box-title">Editar Plan</h3>
                <p class="text-muted m-b-30">Modifique los datos del plan de suscripción</p>
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

            <!-- Formulario de edición de plan -->
            <form id="formEditarPlan" class="form-horizontal" method="post" action="<?= base_url ?>plan/update">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="id" value="<?= $plan->id ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-info-circle m-r-5"></i> Información Básica</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Nombre <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="nombre" id="nombre" required 
                                       value="<?= htmlspecialchars($plan->nombre) ?>"
                                       placeholder="Ej: Plan Profesional">
                                <small class="help-block">Nombre comercial del plan</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Descripción</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="3" 
                                       placeholder="Descripción detallada del plan"><?= htmlspecialchars($plan->descripcion) ?></textarea>
                                <small class="help-block">Describa brevemente las características del plan</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Tipo de Plan <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="tipo_plan" id="tipo_plan" required>
                                    <option value="">Seleccione un tipo...</option>
                                    <option value="Básico" <?= $plan->tipo_plan == 'Básico' ? 'selected' : '' ?>>Básico</option>
                                    <option value="Profesional" <?= $plan->tipo_plan == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                                    <option value="Premium" <?= $plan->tipo_plan == 'Premium' ? 'selected' : '' ?>>Premium</option>
                                    <option value="Personalizado" <?= $plan->tipo_plan == 'Personalizado' ? 'selected' : '' ?>>Personalizado</option>
                                </select>
                                <small class="help-block">Categoría principal del plan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-money m-r-5"></i> Precios</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Moneda <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="moneda" id="moneda" required>
                                    <option value="CLP" <?= $plan->moneda == 'CLP' ? 'selected' : '' ?>>CLP (Peso Chileno)</option>
                                    <option value="USD" <?= $plan->moneda == 'USD' ? 'selected' : '' ?>>USD (Dólar Estadounidense)</option>
                                    <option value="EUR" <?= $plan->moneda == 'EUR' ? 'selected' : '' ?>>EUR (Euro)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Precio Mensual <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="simbolo-moneda">$</span>
                                    <input type="number" class="form-control" name="precio_mensual" id="precio_mensual" required 
                                           step="0.01" min="0" value="<?= $plan->precio_mensual ?>" placeholder="0.00">
                                </div>
                                <small class="help-block">Precio para facturación mensual</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Precio Semestral</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="simbolo-moneda-semestral">$</span>
                                    <input type="number" class="form-control" name="precio_semestral" id="precio_semestral" 
                                           step="0.01" min="0" value="<?= $plan->precio_semestral ?>" placeholder="0.00">
                                </div>
                                <small class="help-block">Precio mensual para facturación semestral (opcional)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Precio Anual</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="simbolo-moneda-anual">$</span>
                                    <input type="number" class="form-control" name="precio_anual" id="precio_anual" 
                                           step="0.01" min="0" value="<?= $plan->precio_anual ?>" placeholder="0.00">
                                </div>
                                <small class="help-block">Precio mensual para facturación anual (opcional)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-gears m-r-5"></i> Límites y Características</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Usuarios Máximos</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="max_usuarios" id="max_usuarios" 
                                       value="<?= $plan->max_usuarios ?>" min="0" step="1">
                                <small class="help-block">Cantidad máxima de usuarios permitidos (0 = Ilimitados)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Artistas Máximos</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="max_artistas" id="max_artistas" 
                                       value="<?= $plan->max_artistas ?>" min="0" step="1">
                                <small class="help-block">Cantidad máxima de artistas (0 = Ilimitados)</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Eventos Máximos</label>
                            <div class="col-md-6">
                                <input type="number" class="form-control" name="max_eventos" id="max_eventos" 
                                       value="<?= $plan->max_eventos ?>" min="0" step="1">
                                <small class="help-block">Cantidad máxima de eventos mensuales (0 = Ilimitados)</small>
                            </div>
                        </div>
                        <!-- Campo de almacenamiento eliminado -->
                        <input type="hidden" name="max_almacenamiento" value="<?= $plan->max_almacenamiento ?>">
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Características Adicionales</label>
                            <div class="col-md-6">
                                <?php 
                                // Extraer las características desde el JSON
                                $caracteristicas = $plan->caracteristicas_array ?? [];
                                ?>
                                
                                <div class="checkbox checkbox-info">
                                    <input id="soporte_prioritario" name="soporte_prioritario" type="checkbox" 
                                        <?= isset($caracteristicas['soporte_prioritario']) && $caracteristicas['soporte_prioritario'] ? 'checked' : '' ?>>
                                    <label for="soporte_prioritario">Soporte Prioritario</label>
                                </div>
                                <div class="checkbox checkbox-info">
                                    <input id="soporte_telefonico" name="soporte_telefonico" type="checkbox"
                                        <?= isset($caracteristicas['soporte_telefonico']) && $caracteristicas['soporte_telefonico'] ? 'checked' : '' ?>>
                                    <label for="soporte_telefonico">Soporte Telefónico</label>
                                </div>
                                <div class="checkbox checkbox-info">
                                    <input id="copias_seguridad" name="copias_seguridad" type="checkbox"
                                        <?= isset($caracteristicas['copias_seguridad']) && $caracteristicas['copias_seguridad'] ? 'checked' : '' ?>>
                                    <label for="copias_seguridad">Copias de Seguridad</label>
                                </div>
                                <div class="checkbox checkbox-info">
                                    <input id="importar_contactos" name="importar_contactos" type="checkbox"
                                        <?= isset($caracteristicas['importar_contactos']) && $caracteristicas['importar_contactos'] ? 'checked' : '' ?>>
                                    <label for="importar_contactos">Importar Contactos</label>
                                </div>
                                <div class="checkbox checkbox-info">
                                    <input id="exportar_pdf" name="exportar_pdf" type="checkbox"
                                        <?= isset($caracteristicas['exportar_pdf']) && $caracteristicas['exportar_pdf'] ? 'checked' : '' ?>>
                                    <label for="exportar_pdf">Exportar a PDF</label>
                                </div>
                                <div class="checkbox checkbox-info">
                                    <input id="reportes_avanzados" name="reportes_avanzados" type="checkbox"
                                        <?= isset($caracteristicas['reportes_avanzados']) && $caracteristicas['reportes_avanzados'] ? 'checked' : '' ?>>
                                    <label for="reportes_avanzados">Reportes Avanzados</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Otras Características</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="caracteristicas_adicionales" rows="3" 
                                          placeholder="Otras características (separadas por comas)"><?= isset($caracteristicas['adicionales']) ? htmlspecialchars($caracteristicas['adicionales']) : '' ?></textarea>
                                <small class="help-block">Ingrese otras características no listadas arriba</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-cog m-r-5"></i> Configuración</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Estado</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado" id="estado">
                                    <option value="Activo" <?= $plan->estado == 'Activo' ? 'selected' : '' ?>>Activo</option>
                                    <option value="Inactivo" <?= $plan->estado == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                    <option value="Descontinuado" <?= $plan->estado == 'Descontinuado' ? 'selected' : '' ?>>Descontinuado</option>
                                </select>
                                <small class="help-block">Estado del plan</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Visibilidad</label>
                            <div class="col-md-6">
                                <select class="form-control" name="visible" id="visible">
                                    <option value="Si" <?= $plan->visible == 'Si' ? 'selected' : '' ?>>Visible para clientes</option>
                                    <option value="No" <?= $plan->visible == 'No' ? 'selected' : '' ?>>Oculto (sólo administradores)</option>
                                </select>
                                <small class="help-block">Determina si el plan es visible para nuevos clientes</small>
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
                        <a href="<?= base_url ?>plan/index" class="btn btn-default waves-effect waves-light m-l-10">
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
    // Actualizar símbolos de moneda cuando cambie la selección
    const monedaSelect = document.getElementById('moneda');
    const simboloMoneda = document.getElementById('simbolo-moneda');
    const simboloMonedaSemestral = document.getElementById('simbolo-moneda-semestral');
    const simboloMonedaAnual = document.getElementById('simbolo-moneda-anual');
    
    function actualizarSimbolos() {
        let simbolo = '$'; // Por defecto
        
        switch (monedaSelect.value) {
            case 'USD':
                simbolo = 'US$';
                break;
            case 'EUR':
                simbolo = '€';
                break;
            default:
                simbolo = '$'; // CLP
        }
        
        simboloMoneda.textContent = simbolo;
        simboloMonedaSemestral.textContent = simbolo;
        simboloMonedaAnual.textContent = simbolo;
    }
    
    // Actualizar inicialmente y al cambiar
    actualizarSimbolos();
    monedaSelect.addEventListener('change', actualizarSimbolos);
    
    // Validar formulario antes de enviar
    document.getElementById('formEditarPlan').addEventListener('submit', function(e) {
        const nombre = document.getElementById('nombre').value.trim();
        const tipoPlan = document.getElementById('tipo_plan').value;
        const precioMensual = document.getElementById('precio_mensual').value;
        
        let isValid = true;
        let errorMessage = '';
        
        // Validar campos obligatorios
        if (!nombre) {
            errorMessage = 'El nombre del plan es obligatorio';
            isValid = false;
        } else if (!tipoPlan) {
            errorMessage = 'Debe seleccionar un tipo de plan';
            isValid = false;
        } else if (!precioMensual || precioMensual <= 0) {
            errorMessage = 'Debe especificar un precio mensual válido';
            isValid = false;
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