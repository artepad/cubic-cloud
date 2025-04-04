<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Cargar los modelos necesarios
$empresaModel = new Empresa();
$planModel = new Plan();

// Obtener listas para los selectores
$empresas = $empresaModel->getAll(['estado' => 'activa']);
$planes = $planModel->getAll(['estado' => 'Activo']);

// Verificar si hay un ID de empresa preseleccionado (para crear suscripción desde la vista de empresas)
$empresa_id_preseleccionado = isset($_GET['empresa_id']) ? intval($_GET['empresa_id']) : null;
$empresa_preseleccionada = null;

if ($empresa_id_preseleccionado) {
    $empresa_preseleccionada = $empresaModel->getById($empresa_id_preseleccionado);
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <h3 class="box-title">Crear Nueva Suscripción</h3>
                <p class="text-muted m-b-30">Complete el formulario para registrar una nueva suscripción</p>
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

            <!-- Formulario de creación de suscripción -->
            <form id="formCrearSuscripcion" class="form-horizontal" method="post" action="<?= base_url ?>admin/saveSuscripcion">
                <!-- Token CSRF para seguridad -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-building m-r-5"></i> Empresa</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Empresa <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="empresa_id" id="empresa_id" required>
                                    <option value="">Seleccione una empresa...</option>
                                    <?php foreach ($empresas as $empresa): ?>
                                        <option value="<?= $empresa->id ?>" <?= ($empresa_id_preseleccionado == $empresa->id) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($empresa->nombre) ?> 
                                            <?= !empty($empresa->identificacion_fiscal) ? '(' . htmlspecialchars($empresa->identificacion_fiscal) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-block">Seleccione la empresa para esta suscripción</small>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url ?>empresa/crear" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Crear Nueva Empresa
                                </a>
                            </div>
                        </div>
                        
                        <!-- Información de la empresa seleccionada (se mostrará mediante JavaScript) -->
                        <div id="infoEmpresa" style="display: none;" class="m-t-20">
                            <div class="col-md-offset-3 col-md-6">
                                <div class="well well-sm">
                                    <h4 id="nombreEmpresa">Nombre de la Empresa</h4>
                                    <p><strong>Identificación:</strong> <span id="identificacionEmpresa">-</span></p>
                                    <p><strong>País:</strong> <span id="paisEmpresa">-</span></p>
                                    <p><strong>Tipo de Moneda:</strong> <span id="monedaEmpresa">-</span></p>
                                    <p><strong>Estado:</strong> <span id="estadoEmpresa" class="label label-success">Activa</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-list m-r-5"></i> Plan y Período</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Plan <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="plan_id" id="plan_id" required>
                                    <option value="">Seleccione un plan...</option>
                                    <?php foreach ($planes as $plan): ?>
                                        <option value="<?= $plan->id ?>" 
                                                data-tipo="<?= htmlspecialchars($plan->tipo_plan) ?>"
                                                data-precio-mensual="<?= $plan->precio_mensual ?>"
                                                data-precio-semestral="<?= $plan->precio_semestral ?>"
                                                data-precio-anual="<?= $plan->precio_anual ?>"
                                                data-moneda="<?= $plan->moneda ?>">
                                            <?= htmlspecialchars($plan->nombre) ?> (<?= htmlspecialchars($plan->tipo_plan) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="help-block">Seleccione el plan para esta suscripción</small>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url ?>plan/crear" class="btn btn-info btn-sm">
                                    <i class="fa fa-plus"></i> Crear Nuevo Plan
                                </a>
                            </div>
                        </div>
                        
                        <!-- Información del plan seleccionado (se mostrará mediante JavaScript) -->
                        <div id="infoPlan" style="display: none;" class="m-t-20">
                            <div class="col-md-offset-3 col-md-6">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Detalles del Plan</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-4 text-center">
                                                <h5>Mensual</h5>
                                                <h4 id="precioMensual">$00.00</h4>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <h5>Semestral</h5>
                                                <h4 id="precioSemestral">$00.00</h4>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <h5>Anual</h5>
                                                <h4 id="precioAnual">$00.00</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Período de Facturación <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="periodo_facturacion" id="periodo_facturacion" required>
                                    <option value="Mensual">Mensual</option>
                                    <option value="Semestral">Semestral</option>
                                    <option value="Anual">Anual</option>
                                </select>
                                <small class="help-block">Seleccione el período de facturación de la suscripción</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-calendar m-r-5"></i> Fechas y Montos</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Fecha de Inicio <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" 
                                       value="<?= date('Y-m-d') ?>" required>
                                <small class="help-block">Fecha desde la cual estará activa la suscripción</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Primer Cobro <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="primer_cobro" id="primer_cobro" required>
                                    <option value="inmediato">Cobrar inmediatamente</option>
                                    <option value="fin_periodo">Cobrar al finalizar el primer período</option>
                                </select>
                                <small class="help-block">Cuándo se realizará el primer cobro de la suscripción</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Precio Total <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon" id="moneda-addon">$</span>
                                    <input type="number" class="form-control" name="precio_total" id="precio_total" 
                                           step="0.01" min="0" required>
                                </div>
                                <small class="help-block">Monto a cobrar por el período seleccionado</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Moneda <span class="text-danger">*</span></label>
                            <div class="col-md-6">
                                <select class="form-control" name="moneda" id="moneda" required>
                                    <option value="CLP">CLP (Peso Chileno)</option>
                                    <option value="USD">USD (Dólar Estadounidense)</option>
                                    <option value="EUR">EUR (Euro)</option>
                                </select>
                                <small class="help-block">Moneda utilizada para los cobros</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa fa-cog m-r-5"></i> Configuración Adicional</h4>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Estado</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado" id="estado">
                                    <option value="Activa" selected>Activa</option>
                                    <option value="Pendiente">Pendiente</option>
                                </select>
                                <small class="help-block">Estado inicial de la suscripción</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Número de Suscripción</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="numero_suscripcion" id="numero_suscripcion" 
                                       value="SUB-<?= date('Ymd') ?>-" readonly>
                                <small class="help-block">Se generará automáticamente un número único de suscripción</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">Notas</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="notas" id="notas" rows="3" 
                                         placeholder="Notas adicionales sobre esta suscripción"></textarea>
                                <small class="help-block">Información adicional sobre esta suscripción (opcional)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-group m-b-0">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success waves-effect waves-light">
                            <i class="fa fa-check"></i> Guardar Suscripción
                        </button>
                        <a href="<?= base_url ?>admin/suscripciones" class="btn btn-default waves-effect waves-light m-l-10">
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
    // Referencias a elementos del DOM
    const empresaSelect = document.getElementById('empresa_id');
    const planSelect = document.getElementById('plan_id');
    const periodoSelect = document.getElementById('periodo_facturacion');
    const precioTotalInput = document.getElementById('precio_total');
    const monedaSelect = document.getElementById('moneda');
    const monedaAddon = document.getElementById('moneda-addon');
    const numeroSuscripcion = document.getElementById('numero_suscripcion');
    
    // Manejar cambio de empresa
    empresaSelect.addEventListener('change', function() {
        const empresaId = this.value;
        if (empresaId) {
            // Cargar información de la empresa mediante AJAX
            fetch('<?= base_url ?>admin/getEmpresaInfo?id=' + empresaId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar información visible
                        document.getElementById('nombreEmpresa').textContent = data.empresa.nombre;
                        document.getElementById('identificacionEmpresa').textContent = data.empresa.identificacion_fiscal || '-';
                        document.getElementById('paisEmpresa').textContent = data.empresa.pais;
                        document.getElementById('monedaEmpresa').textContent = data.empresa.tipo_moneda;
                        
                        // Actualizar estado visual
                        const estadoEmpresa = document.getElementById('estadoEmpresa');
                        estadoEmpresa.textContent = data.empresa.estado;
                        estadoEmpresa.className = 'label ' + (data.empresa.estado === 'activa' ? 'label-success' : 'label-warning');
                        
                        // Mostrar el panel de información
                        document.getElementById('infoEmpresa').style.display = 'block';
                        
                        // Actualizar número de suscripción con ID de empresa
                        numeroSuscripcion.value = 'SUB-' + formatDate(new Date()) + '-' + empresaId;
                        
                        // Actualizar moneda predeterminada según la empresa
                        if (data.empresa.tipo_moneda) {
                            monedaSelect.value = data.empresa.tipo_moneda;
                            actualizarSimboloMoneda();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al cargar información de la empresa:', error);
                });
        } else {
            // Ocultar panel si no hay empresa seleccionada
            document.getElementById('infoEmpresa').style.display = 'none';
        }
    });
    
    // Manejar cambio de plan
    planSelect.addEventListener('change', function() {
        const planOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Obtener datos del plan desde los atributos data-*
            const tipoPlan = planOption.getAttribute('data-tipo');
            const precioMensual = parseFloat(planOption.getAttribute('data-precio-mensual'));
            const precioSemestral = parseFloat(planOption.getAttribute('data-precio-semestral'));
            const precioAnual = parseFloat(planOption.getAttribute('data-precio-anual'));
            const monedaPlan = planOption.getAttribute('data-moneda');
            
            // Actualizar información visible
            document.getElementById('precioMensual').textContent = formatearPrecio(precioMensual, monedaPlan);
            document.getElementById('precioSemestral').textContent = formatearPrecio(precioSemestral, monedaPlan);
            document.getElementById('precioAnual').textContent = formatearPrecio(precioAnual, monedaPlan);
            
            // Mostrar el panel de información
            document.getElementById('infoPlan').style.display = 'block';
            
            // Actualizar moneda según el plan
            if (monedaPlan) {
                monedaSelect.value = monedaPlan;
                actualizarSimboloMoneda();
                actualizarPrecioTotal();
            }
        } else {
            // Ocultar panel si no hay plan seleccionado
            document.getElementById('infoPlan').style.display = 'none';
        }
    });
    
    // Actualizar precio total cuando cambia el período o el plan
    periodoSelect.addEventListener('change', actualizarPrecioTotal);
    planSelect.addEventListener('change', actualizarPrecioTotal);
    
    // Actualizar símbolo de moneda cuando cambia la selección
    monedaSelect.addEventListener('change', actualizarSimboloMoneda);
    
    // Función para actualizar el precio total según el plan y período seleccionados
    function actualizarPrecioTotal() {
        const planOption = planSelect.options[planSelect.selectedIndex];
        
        if (planSelect.value && periodoSelect.value) {
            let precio = 0;
            
            // Obtener precio según el período seleccionado
            switch (periodoSelect.value) {
                case 'Mensual':
                    precio = parseFloat(planOption.getAttribute('data-precio-mensual'));
                    break;
                case 'Semestral':
                    precio = parseFloat(planOption.getAttribute('data-precio-semestral'));
                    break;
                case 'Anual':
                    precio = parseFloat(planOption.getAttribute('data-precio-anual'));
                    break;
            }
            
            // Actualizar campo de precio total
            precioTotalInput.value = precio.toFixed(2);
        }
    }
    
    // Función para actualizar el símbolo de moneda
    function actualizarSimboloMoneda() {
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
        
        monedaAddon.textContent = simbolo;
    }
    
    // Función para formatear precios con el símbolo de moneda
    function formatearPrecio(precio, moneda) {
        let simbolo = '';
        
        switch (moneda) {
            case 'USD':
                simbolo = 'US$';
                break;
            case 'EUR':
                simbolo = '€';
                break;
            default:
                simbolo = '$'; // CLP
        }
        
        return simbolo + ' ' + precio.toFixed(2);
    }
    
    // Función para formatear fecha como YYYYMMDD
    function formatDate(date) {
        return date.getFullYear() + 
               ('0' + (date.getMonth() + 1)).slice(-2) + 
               ('0' + date.getDate()).slice(-2);
    }
    
    // Validar formulario antes de enviar
    document.getElementById('formCrearSuscripcion').addEventListener('submit', function(e) {
        const empresa = empresaSelect.value;
        const plan = planSelect.value;
        const fechaInicio = document.getElementById('fecha_inicio').value;
        const precioTotal = precioTotalInput.value;
        
        let isValid = true;
        let errorMessage = '';
        
        // Validar campos obligatorios
        if (!empresa) {
            errorMessage = 'Debe seleccionar una empresa';
            isValid = false;
        } else if (!plan) {
            errorMessage = 'Debe seleccionar un plan';
            isValid = false;
        } else if (!fechaInicio) {
            errorMessage = 'Debe especificar la fecha de inicio';
            isValid = false;
        } else if (!precioTotal || precioTotal <= 0) {
            errorMessage = 'El precio total debe ser mayor que cero';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
    });
    
    // Inicializar elementos con valores preseleccionados si existen
    if (empresaSelect.value) {
        empresaSelect.dispatchEvent(new Event('change'));
    }
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
.well-sm {
    border-radius: 3px;
    padding: 10px 15px;
}
.m-t-20 {
    margin-top: 20px;
}
.m-r-5 {
    margin-right: 5px;
}
.m-l-10 {
    margin-left: 10px;
}