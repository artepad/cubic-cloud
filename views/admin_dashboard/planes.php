<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Planes</h3>
            <p class="text-muted">Administra los planes de suscripción disponibles en el sistema</p>
            
            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="javascript:void(0);" class="btn btn-info waves-effect waves-light m-r-10" id="btnNuevoPlan">
                        <i class="icon-plus"></i> Nuevo Plan
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success waves-effect waves-light m-r-10" id="btnActualizarPlanes">
                        <i class="icon-refresh"></i> Actualizar
                    </a>
                    <a href="javascript:void(0);" class="btn btn-warning waves-effect waves-light" id="btnExportarPlanes">
                        <i class="icon-cloud-download"></i> Exportar
                    </a>
                </div>
            </div>
            
            <!-- Tabla de planes -->
            <div class="table-responsive">
                <table class="table table-striped" id="tablaPlanes">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Precio Mensual</th>
                            <th>Usuarios</th>
                            <th>Eventos</th>
                            <th>Almacenamiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Plan Básico</td>
                            <td>Básico</td>
                            <td>$49.99 USD</td>
                            <td>1</td>
                            <td>10</td>
                            <td>100 MB</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Desactivar">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Plan Profesional</td>
                            <td>Profesional</td>
                            <td>$149.99 USD</td>
                            <td>5</td>
                            <td>50</td>
                            <td>500 MB</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Desactivar">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Plan Premium</td>
                            <td>Premium</td>
                            <td>$299.99 USD</td>
                            <td>Ilimitados</td>
                            <td>Ilimitados</td>
                            <td>2 GB</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Desactivar">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Plan Personalizado</td>
                            <td>Personalizado</td>
                            <td>$499.99 USD</td>
                            <td>Ilimitados</td>
                            <td>Ilimitados</td>
                            <td>5 GB</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Desactivar">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Plan Demo</td>
                            <td>Básico</td>
                            <td>$0.00 USD</td>
                            <td>1</td>
                            <td>5</td>
                            <td>50 MB</td>
                            <td><span class="label label-warning">Inactivo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Activar">
                                    <i class="icon-check"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="text-center m-t-20">
                <ul class="pagination">
                    <li class="disabled">
                        <a href="javascript:void(0);"><i class="icon-arrow-left-circle"></i></a>
                    </li>
                    <li class="active">
                        <a href="javascript:void(0);">1</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);">2</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="icon-arrow-right-circle"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Cards para comparativa de planes -->
<div class="row m-t-30">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Comparativa de Planes</h3>
            <p class="text-muted">Visualización de características y precios por período</p>
            
            <div class="row m-t-30">
                <!-- Plan Básico -->
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-heading bg-primary">
                            <h3 class="panel-title text-center text-white">Plan Básico</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h3 class="price-tag">$49.99 <small>/mes</small></h3>
                            <p class="price-yearly text-muted">$539.89 /año (10% descuento)</p>
                            <hr>
                            <ul class="feature-list list-unstyled">
                                <li><i class="icon-user text-primary"></i> 1 Usuario</li>
                                <li><i class="icon-calendar text-primary"></i> 10 Eventos/mes</li>
                                <li><i class="icon-notebook text-primary"></i> 5 Artistas</li>
                                <li><i class="icon-cloud text-primary"></i> 100 MB Almacenamiento</li>
                                <li><i class="icon-envelope text-primary"></i> Email Soporte</li>
                            </ul>
                            <hr>
                            <a href="javascript:void(0);" class="btn btn-primary btn-block">Editar Plan</a>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Profesional -->
                <div class="col-md-3">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center text-white">Plan Profesional</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h3 class="price-tag">$149.99 <small>/mes</small></h3>
                            <p class="price-yearly text-muted">$1,619.89 /año (10% descuento)</p>
                            <hr>
                            <ul class="feature-list list-unstyled">
                                <li><i class="icon-user text-info"></i> 5 Usuarios</li>
                                <li><i class="icon-calendar text-info"></i> 50 Eventos/mes</li>
                                <li><i class="icon-notebook text-info"></i> 25 Artistas</li>
                                <li><i class="icon-cloud text-info"></i> 500 MB Almacenamiento</li>
                                <li><i class="icon-envelope text-info"></i> Soporte Prioritario</li>
                            </ul>
                            <hr>
                            <a href="javascript:void(0);" class="btn btn-info btn-block">Editar Plan</a>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Premium -->
                <div class="col-md-3">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center text-white">Plan Premium</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h3 class="price-tag">$299.99 <small>/mes</small></h3>
                            <p class="price-yearly text-muted">$3,239.89 /año (10% descuento)</p>
                            <hr>
                            <ul class="feature-list list-unstyled">
                                <li><i class="icon-user text-success"></i> Usuarios Ilimitados</li>
                                <li><i class="icon-calendar text-success"></i> Eventos Ilimitados</li>
                                <li><i class="icon-notebook text-success"></i> Artistas Ilimitados</li>
                                <li><i class="icon-cloud text-success"></i> 2 GB Almacenamiento</li>
                                <li><i class="icon-envelope text-success"></i> Soporte 24/7</li>
                            </ul>
                            <hr>
                            <a href="javascript:void(0);" class="btn btn-success btn-block">Editar Plan</a>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Personalizado -->
                <div class="col-md-3">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <h3 class="panel-title text-center text-white">Plan Personalizado</h3>
                        </div>
                        <div class="panel-body text-center">
                            <h3 class="price-tag">$499.99 <small>/mes</small></h3>
                            <p class="price-yearly text-muted">$5,399.89 /año (10% descuento)</p>
                            <hr>
                            <ul class="feature-list list-unstyled">
                                <li><i class="icon-user text-danger"></i> Usuarios Ilimitados</li>
                                <li><i class="icon-calendar text-danger"></i> Eventos Ilimitados</li>
                                <li><i class="icon-notebook text-danger"></i> Artistas Ilimitados</li>
                                <li><i class="icon-cloud text-danger"></i> 5 GB Almacenamiento</li>
                                <li><i class="icon-plus text-danger"></i> Funciones Personalizadas</li>
                            </ul>
                            <hr>
                            <a href="javascript:void(0);" class="btn btn-danger btn-block">Editar Plan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Plan -->
<div id="modalPlan" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalPlanLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalPlanLabel">Nuevo Plan</h4>
            </div>
            <div class="modal-body">
                <form id="formPlan" class="form-horizontal">
                    <input type="hidden" id="plan_id" name="plan_id" value="">
                    
                    <!-- Información básica del plan -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre comercial del plan" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Tipo *</label>
                        <div class="col-md-9">
                            <select class="form-control" id="tipo_plan" name="tipo_plan" required>
                                <option value="Básico">Básico</option>
                                <option value="Profesional">Profesional</option>
                                <option value="Premium">Premium</option>
                                <option value="Personalizado">Personalizado</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Descripción *</label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada de las características del plan" required></textarea>
                        </div>
                    </div>
                    
                    <!-- Precios -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Moneda *</label>
                        <div class="col-md-9">
                            <select class="form-control" id="moneda" name="moneda" required>
                                <option value="CLP">CLP (Peso Chileno)</option>
                                <option value="USD">USD (Dólar Estadounidense)</option>
                                <option value="EUR">EUR (Euro)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Precio Mensual *</label>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="precio_mensual" name="precio_mensual" placeholder="0.00" required>
                            </div>
                        </div>
                        <label class="col-md-3 control-label">Precio Semestral *</label>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="precio_semestral" name="precio_semestral" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Precio Anual *</label>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="precio_anual" name="precio_anual" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Límites -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Usuarios Máximos *</label>
                        <div class="col-md-3">
                            <input type="number" min="1" class="form-control" id="max_usuarios" name="max_usuarios" placeholder="1" required>
                            <small class="text-muted">Usar -1 para ilimitados</small>
                        </div>
                        <label class="col-md-3 control-label">Eventos Máximos *</label>
                        <div class="col-md-3">
                            <input type="number" min="1" class="form-control" id="max_eventos" name="max_eventos" placeholder="10" required>
                            <small class="text-muted">Usar -1 para ilimitados</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Artistas Máximos *</label>
                        <div class="col-md-3">
                            <input type="number" min="1" class="form-control" id="max_artistas" name="max_artistas" placeholder="5" required>
                            <small class="text-muted">Usar -1 para ilimitados</small>
                        </div>
                        <label class="col-md-3 control-label">Almacenamiento (MB) *</label>
                        <div class="col-md-3">
                            <input type="number" min="1" class="form-control" id="max_almacenamiento" name="max_almacenamiento" placeholder="100" required>
                        </div>
                    </div>
                    
                    <!-- Características adicionales -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Características Adicionales</label>
                        <div class="col-md-9">
                            <div class="checkbox checkbox-success">
                                <input id="api_access" name="api_access" type="checkbox">
                                <label for="api_access">Acceso a API</label>
                            </div>
                            <div class="checkbox checkbox-success">
                                <input id="reportes_avanzados" name="reportes_avanzados" type="checkbox">
                                <label for="reportes_avanzados">Reportes Avanzados</label>
                            </div>
                            <div class="checkbox checkbox-success">
                                <input id="integraciones" name="integraciones" type="checkbox">
                                <label for="integraciones">Integraciones con Terceros</label>
                            </div>
                            <div class="checkbox checkbox-success">
                                <input id="soporte_prioritario" name="soporte_prioritario" type="checkbox">
                                <label for="soporte_prioritario">Soporte Prioritario</label>
                            </div>
                            <div class="checkbox checkbox-success">
                                <input id="personalizacion" name="personalizacion" type="checkbox">
                                <label for="personalizacion">Personalización de Funcionalidades</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Estado *</label>
                        <div class="col-md-3">
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Descontinuado">Descontinuado</option>
                            </select>
                        </div>
                        <label class="col-md-3 control-label">Visible</label>
                        <div class="col-md-3">
                            <select class="form-control" id="visible" name="visible">
                                <option value="Si">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPlan">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos CSS adicionales para esta vista -->
<style>
    .panel-heading {
        color: #fff;
    }
    .price-tag {
        font-size: 30px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .price-tag small {
        font-size: 14px;
        font-weight: 400;
    }
    .price-yearly {
        font-size: 14px;
        margin-bottom: 20px;
    }
    .feature-list li {
        margin-bottom: 10px;
        font-size: 14px;
    }
    .feature-list li i {
        margin-right: 5px;
    }
    .panel-default .panel-heading {
        background-color: #4c5667;
    }
    .panel-primary .panel-heading {
        background-color: #7460ee;
    }
    .panel-info .panel-heading {
        background-color: #41b3f9;
    }
    .panel-success .panel-heading {
        background-color: #26c6da;
    }
    .panel-danger .panel-heading {
        background-color: #f33155;
    }
    .text-white {
        color: white;
    }
</style>

<!-- JavaScript para la funcionalidad de la página -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Inicializar eventos de botones
    document.getElementById('btnNuevoPlan').addEventListener('click', function() {
        abrirModalPlan();
    });
    
    document.getElementById('btnActualizarPlanes').addEventListener('click', function() {
        Swal.fire({
            title: 'Lista actualizada',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    });
    
    document.getElementById('btnExportarPlanes').addEventListener('click', function() {
        Swal.fire({
            title: 'Exportando datos',
            text: 'El archivo se está generando...',
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    });
    
    // Configurar eventos para acciones en tabla
    configurarEventosTabla();
    
    // Configurar cálculo automático de precios con descuento
    document.getElementById('precio_mensual').addEventListener('input', calcularPreciosConDescuento);
    
    // Configurar validación del formulario
    configurarValidacionFormulario();
    
    // Configurar cambio de color según tipo de plan
    document.getElementById('tipo_plan').addEventListener('change', function() {
        actualizarEstiloSegunTipo(this.value);
    });
});

// Función para abrir modal de nuevo plan
function abrirModalPlan(id = null) {
    // Resetear formulario
    document.getElementById('formPlan').reset();
    document.getElementById('plan_id').value = '';
    
    // Cambiar título según sea crear o editar
    document.getElementById('modalPlanLabel').textContent = id ? 'Editar Plan' : 'Nuevo Plan';
    
    // Si es edición, cargar datos del plan
    if (id) {
        cargarDatosPlan(id);
    } else {
        // Valores por defecto para nuevo plan
        document.getElementById('moneda').value = 'USD';
        document.getElementById('max_usuarios').value = '1';
        document.getElementById('max_eventos').value = '10';
        document.getElementById('max_artistas').value = '5';
        document.getElementById('max_almacenamiento').value = '100';
        actualizarEstiloSegunTipo('Básico');
    }
    
    // Mostrar el modal
    $('#modalPlan').modal('show');
}

// Función para cargar datos de un plan (simulada)
function cargarDatosPlan(id) {
    // En producción, esto haría una llamada AJAX para obtener los datos
    // Aquí simulamos la carga con datos de ejemplo
    let datos;
    
    switch(id) {
        case '1':
            datos = {
                id: 1,
                nombre: 'Plan Básico',
                tipo_plan: 'Básico',
                descripcion: 'Plan ideal para pequeñas empresas que organizan pocos eventos al mes.',
                moneda: 'USD',
                precio_mensual: 49.99,
                precio_semestral: 269.94, // 10% descuento sobre mensual
                precio_anual: 539.89, // 10% descuento sobre mensual
                max_usuarios: 1,
                max_eventos: 10,
                max_artistas: 5,
                max_almacenamiento: 100,
                estado: 'Activo',
                visible: 'Si',
                caracteristicas: {
                    api_access: false,
                    reportes_avanzados: false,
                    integraciones: false,
                    soporte_prioritario: false,
                    personalizacion: false
                }
            };
            break;
        case '2':
            datos = {
                id: 2,
                nombre: 'Plan Profesional',
                tipo_plan: 'Profesional',
                descripcion: 'Plan ideal para empresas medianas con múltiples eventos y artistas.',
                moneda: 'USD',
                precio_mensual: 149.99,
                precio_semestral: 809.95, // 10% descuento sobre mensual
                precio_anual: 1619.89, // 10% descuento sobre mensual
                max_usuarios: 5,
                max_eventos: 50,
                max_artistas: 25,
                max_almacenamiento: 500,
                estado: 'Activo',
                visible: 'Si',
                caracteristicas: {
                    api_access: true,
                    reportes_avanzados: true,
                    integraciones: false,
                    soporte_prioritario: true,
                    personalizacion: false
                }
            };
            break;
        case '3':
            datos = {
                id: 3,
                nombre: 'Plan Premium',
                tipo_plan: 'Premium',
                descripcion: 'Plan ideal para empresas grandes con alto volumen de eventos.',
                moneda: 'USD',
                precio_mensual: 299.99,
                precio_semestral: 1619.95, // 10% descuento sobre mensual
                precio_anual: 3239.89, // 10% descuento sobre mensual
                max_usuarios: -1, // Ilimitados
                max_eventos: -1, // Ilimitados
                max_artistas: -1, // Ilimitados
                max_almacenamiento: 2048, // 2 GB
                estado: 'Activo',
                visible: 'Si',
                caracteristicas: {
                    api_access: true,
                    reportes_avanzados: true,
                    integraciones: true,
                    soporte_prioritario: true,
                    personalizacion: false
                }
            };
            break;
        default:
            datos = {
                id: 4,
                nombre: 'Plan Personalizado',
                tipo_plan: 'Personalizado',
                descripcion: 'Plan personalizable para necesidades específicas de grandes empresas.',
                moneda: 'USD',
                precio_mensual: 499.99,
                precio_semestral: 2699.95, // 10% descuento sobre mensual
                precio_anual: 5399.89, // 10% descuento sobre mensual
                max_usuarios: -1, // Ilimitados
                max_eventos: -1, // Ilimitados
                max_artistas: -1, // Ilimitados
                max_almacenamiento: 5120, // 5 GB
                estado: 'Activo',
                visible: 'Si',
                caracteristicas: {
                    api_access: true,
                    reportes_avanzados: true,
                    integraciones: true,
                    soporte_prioritario: true,
                    personalizacion: true
                }
            };
    }
    
    // Rellenar el formulario con los datos
    document.getElementById('plan_id').value = datos.id;
    document.getElementById('nombre').value = datos.nombre;
    document.getElementById('tipo_plan').value = datos.tipo_plan;
    document.getElementById('descripcion').value = datos.descripcion;
    document.getElementById('moneda').value = datos.moneda;
    document.getElementById('precio_mensual').value = datos.precio_mensual;
    document.getElementById('precio_semestral').value = datos.precio_semestral;
    document.getElementById('precio_anual').value = datos.precio_anual;
    document.getElementById('max_usuarios').value = datos.max_usuarios;
    document.getElementById('max_eventos').value = datos.max_eventos;
    document.getElementById('max_artistas').value = datos.max_artistas;
    document.getElementById('max_almacenamiento').value = datos.max_almacenamiento;
    document.getElementById('estado').value = datos.estado;
    document.getElementById('visible').value = datos.visible;
    
    // Rellenar checkboxes de características
    document.getElementById('api_access').checked = datos.caracteristicas.api_access;
    document.getElementById('reportes_avanzados').checked = datos.caracteristicas.reportes_avanzados;
    document.getElementById('integraciones').checked = datos.caracteristicas.integraciones;
    document.getElementById('soporte_prioritario').checked = datos.caracteristicas.soporte_prioritario;
    document.getElementById('personalizacion').checked = datos.caracteristicas.personalizacion;
    
    // Actualizar estilos según el tipo de plan
    actualizarEstiloSegunTipo(datos.tipo_plan);
}

// Función para calcular precios con descuento
function calcularPreciosConDescuento() {
    const precioMensual = parseFloat(document.getElementById('precio_mensual').value) || 0;
    
    // Aplicar 10% de descuento para semestral (6 meses)
    const precioSemestral = (precioMensual * 6 * 0.9).toFixed(2);
    
    // Aplicar 10% de descuento para anual (12 meses)
    const precioAnual = (precioMensual * 12 * 0.9).toFixed(2);
    
    // Actualizar campos si no tienen el foco (para no interrumpir al usuario)
    const semestraleInput = document.getElementById('precio_semestral');
    const anualInput = document.getElementById('precio_anual');
    
    if (document.activeElement !== semestraleInput) {
        semestraleInput.value = precioSemestral;
    }
    
    if (document.activeElement !== anualInput) {
        anualInput.value = precioAnual;
    }
}

// Función para actualizar el estilo según el tipo de plan
function actualizarEstiloSegunTipo(tipo) {
    const btnGuardar = document.getElementById('btnGuardarPlan');
    
    // Resetear clases
    btnGuardar.className = 'btn';
    
    // Aplicar clase según tipo
    switch(tipo) {
        case 'Básico':
            btnGuardar.classList.add('btn-primary');
            break;
        case 'Profesional':
            btnGuardar.classList.add('btn-info');
            break;
        case 'Premium':
            btnGuardar.classList.add('btn-success');
            break;
        case 'Personalizado':
            btnGuardar.classList.add('btn-danger');
            break;
        default:
            btnGuardar.classList.add('btn-primary');
    }
}

// Configurar eventos para acciones en la tabla
function configurarEventosTabla() {
    // Obtener todos los botones de acción en la tabla
    const btnsVer = document.querySelectorAll('.icon-eye');
    const btnsEditar = document.querySelectorAll('.icon-pencil');
    const btnsDesactivar = document.querySelectorAll('.icon-ban');
    const btnsActivar = document.querySelectorAll('.icon-check');
    
    // Configurar eventos para ver detalles
    btnsVer.forEach(function(btn) {
        btn.closest('a').addEventListener('click', function() {
            const id = this.closest('tr').cells[0].textContent;
            verDetallesPlan(id);
        });
    });
    
    // Configurar eventos para editar
    btnsEditar.forEach(function(btn) {
        btn.closest('a').addEventListener('click', function() {
            const id = this.closest('tr').cells[0].textContent;
            abrirModalPlan(id);
        });
    });
    
    // Configurar eventos para desactivar
    btnsDesactivar.forEach(function(btn) {
        btn.closest('a').addEventListener('click', function() {
            const id = this.closest('tr').cells[0].textContent;
            const nombre = this.closest('tr').cells[1].textContent;
            confirmarAccion('desactivar', id, nombre);
        });
    });
    
    // Configurar eventos para activar
    btnsActivar.forEach(function(btn) {
        btn.closest('a').addEventListener('click', function() {
            const id = this.closest('tr').cells[0].textContent;
            const nombre = this.closest('tr').cells[1].textContent;
            confirmarAccion('activar', id, nombre);
        });
    });
}

// Función para ver detalles de un plan (simulada)
function verDetallesPlan(id) {
    // En producción, esto haría una llamada AJAX para obtener los datos completos
    // Aquí simulamos con datos de ejemplo
    let nombrePlan, tipoPlan, descripcion, precios, limites, caracteristicas;
    
    switch(id) {
        case '1':
            nombrePlan = 'Plan Básico';
            tipoPlan = 'Básico';
            descripcion = 'Plan ideal para pequeñas empresas que organizan pocos eventos al mes.';
            precios = 'Mensual: $49.99 USD<br>Semestral: $269.94 USD<br>Anual: $539.89 USD';
            limites = 'Usuarios: 1<br>Eventos: 10<br>Artistas: 5<br>Almacenamiento: 100 MB';
            caracteristicas = 'Soporte por Email';
            break;
        case '2':
            nombrePlan = 'Plan Profesional';
            tipoPlan = 'Profesional';
            descripcion = 'Plan ideal para empresas medianas con múltiples eventos y artistas.';
            precios = 'Mensual: $149.99 USD<br>Semestral: $809.95 USD<br>Anual: $1,619.89 USD';
            limites = 'Usuarios: 5<br>Eventos: 50<br>Artistas: 25<br>Almacenamiento: 500 MB';
            caracteristicas = 'Acceso a API<br>Reportes Avanzados<br>Soporte Prioritario';
            break;
        case '3':
            nombrePlan = 'Plan Premium';
            tipoPlan = 'Premium';
            descripcion = 'Plan ideal para empresas grandes con alto volumen de eventos.';
            precios = 'Mensual: $299.99 USD<br>Semestral: $1,619.95 USD<br>Anual: $3,239.89 USD';
            limites = 'Usuarios: Ilimitados<br>Eventos: Ilimitados<br>Artistas: Ilimitados<br>Almacenamiento: 2 GB';
            caracteristicas = 'Acceso a API<br>Reportes Avanzados<br>Integraciones<br>Soporte 24/7';
            break;
        default:
            nombrePlan = 'Plan Personalizado';
            tipoPlan = 'Personalizado';
            descripcion = 'Plan personalizable para necesidades específicas de grandes empresas.';
            precios = 'Mensual: $499.99 USD<br>Semestral: $2,699.95 USD<br>Anual: $5,399.89 USD';
            limites = 'Usuarios: Ilimitados<br>Eventos: Ilimitados<br>Artistas: Ilimitados<br>Almacenamiento: 5 GB';
            caracteristicas = 'Acceso a API<br>Reportes Avanzados<br>Integraciones<br>Soporte 24/7<br>Funciones Personalizadas';
    }
    
    Swal.fire({
        title: nombrePlan,
        html: `
            <div class="text-left">
                <p><strong>Tipo:</strong> ${tipoPlan}</p>
                <p><strong>Descripción:</strong> ${descripcion}</p>
                <hr>
                <p><strong>Precios:</strong></p>
                <p>${precios}</p>
                <hr>
                <p><strong>Límites:</strong></p>
                <p>${limites}</p>
                <hr>
                <p><strong>Características:</strong></p>
                <p>${caracteristicas}</p>
            </div>
        `,
        width: '600px'
    });
}

// Función para confirmar acciones de activar/desactivar
function confirmarAccion(accion, id, nombre) {
    const mensaje = accion === 'activar' 
        ? `¿Está seguro que desea activar el plan <strong>${nombre}</strong>?` 
        : `¿Está seguro que desea desactivar el plan <strong>${nombre}</strong>?`;
    
    Swal.fire({
        title: '¿Confirmar acción?',
        html: mensaje,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: accion === 'activar' ? '#26c6da' : '#f33155',
        cancelButtonColor: '#4c5667',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            realizarAccion(accion, id);
        }
    });
}

// Función para realizar la acción de activar/desactivar (simulada)
function realizarAccion(accion, id) {
    // En producción, esto haría una llamada AJAX para realizar la acción
    const mensaje = accion === 'activar' 
        ? 'Plan activado correctamente' 
        : 'Plan desactivado correctamente';
    
    Swal.fire({
        title: 'Éxito',
        text: mensaje,
        icon: 'success',
        timer: 2000,
        showConfirmButton: false
    });
}

// Configurar validación del formulario
function configurarValidacionFormulario() {
    // Evento para guardar plan
    document.getElementById('btnGuardarPlan').addEventListener('click', function() {
        const form = document.getElementById('formPlan');
        
        // Validar campos requeridos
        if (!validarFormulario(form)) {
            return;
        }
        
        // Recopilar datos de características adicionales para JSON
        const caracteristicas = {
            api_access: document.getElementById('api_access').checked,
            reportes_avanzados: document.getElementById('reportes_avanzados').checked,
            integraciones: document.getElementById('integraciones').checked,
            soporte_prioritario: document.getElementById('soporte_prioritario').checked,
            personalizacion: document.getElementById('personalizacion').checked
        };
        
        // En producción, esto haría una llamada AJAX para guardar los datos
        // Simulamos éxito
        Swal.fire({
            title: 'Plan guardado',
            text: 'Los datos se han guardado correctamente',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        }).then(function() {
            $('#modalPlan').modal('hide');
        });
    });
}

// Validar formulario básico
function validarFormulario(form) {
    const campos = form.querySelectorAll('[required]');
    let valido = true;
    
    campos.forEach(function(campo) {
        if (!campo.value) {
            campo.classList.add('error');
            valido = false;
        } else {
            campo.classList.remove('error');
        }
    });
    
    // Validar que los precios sean mayores o iguales a cero
    const precio_mensual = parseFloat(document.getElementById('precio_mensual').value);
    const precio_semestral = parseFloat(document.getElementById('precio_semestral').value);
    const precio_anual = parseFloat(document.getElementById('precio_anual').value);
    
    if (isNaN(precio_mensual) || precio_mensual < 0) {
        document.getElementById('precio_mensual').classList.add('error');
        valido = false;
    }
    
    if (isNaN(precio_semestral) || precio_semestral < 0) {
        document.getElementById('precio_semestral').classList.add('error');
        valido = false;
    }
    
    if (isNaN(precio_anual) || precio_anual < 0) {
        document.getElementById('precio_anual').classList.add('error');
        valido = false;
    }
    
    if (!valido) {
        Swal.fire({
            title: 'Error',
            text: 'Por favor, complete todos los campos obligatorios correctamente',
            icon: 'error',
            confirmButtonText: 'Aceptar'
        });
    }
    
    return valido;
}