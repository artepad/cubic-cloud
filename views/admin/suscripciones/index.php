<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Obtener parámetros de paginación y filtrado
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$elementosPorPagina = 10;
$offset = ($pagina - 1) * $elementosPorPagina;

// Filtros
$filters = [];
if (isset($_GET['estado']) && !empty($_GET['estado'])) {
    $filters['estado'] = $_GET['estado'];
}
if (isset($_GET['empresa_id']) && !empty($_GET['empresa_id'])) {
    $filters['empresa_id'] = $_GET['empresa_id'];
}
if (isset($_GET['plan_id']) && !empty($_GET['plan_id'])) {
    $filters['plan_id'] = $_GET['plan_id'];
}
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $filters['busqueda'] = $_GET['busqueda'];
}
if (isset($_GET['periodo']) && !empty($_GET['periodo'])) {
    $filters['periodo'] = $_GET['periodo'];
}
if (isset($_GET['vencidas']) && $_GET['vencidas'] == '1') {
    $filters['vencidas'] = true;
}
if (isset($_GET['proximo_vencimiento']) && !empty($_GET['proximo_vencimiento'])) {
    $filters['proximo_vencimiento'] = $_GET['proximo_vencimiento'];
}

// Inicializar modelos necesarios
$suscripcionModel = new Suscripcion();
$empresaModel = new Empresa();
$planModel = new Plan();

// Obtener suscripciones aplicando filtros y paginación
$suscripciones = $suscripcionModel->getAll($filters, $elementosPorPagina, $offset);
$total_suscripciones = $suscripcionModel->countAll($filters);

// Obtener listas para los selectores de filtro
$empresas = $empresaModel->getAll();
$planes = $planModel->getAll();

// Cálculos para la paginación
$total_paginas = ceil($total_suscripciones / $elementosPorPagina);
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Suscripciones</h3>
            <p class="text-muted">Administra las suscripciones de empresas en el sistema</p>
            
            <!-- Mensajes de éxito y error -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['success_message'] ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?= $_SESSION['error_message'] ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <!-- Filtros de búsqueda -->
            <div class="row m-t-10 m-b-20">
                <div class="col-md-12">
                    <form id="searchForm" class="form-inline" method="get" action="<?= base_url ?>admin/suscripciones">
                        <div class="form-group m-r-10 m-b-10">
                            <input type="text" class="form-control" name="busqueda" id="busqueda" 
                                placeholder="Buscar..." 
                                value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <select class="form-control" name="empresa_id" id="empresa_id">
                                <option value="">Todas las empresas</option>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= $empresa->id ?>" <?= (isset($_GET['empresa_id']) && $_GET['empresa_id'] == $empresa->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($empresa->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <select class="form-control" name="plan_id" id="plan_id">
                                <option value="">Todos los planes</option>
                                <?php foreach ($planes as $plan): ?>
                                    <option value="<?= $plan->id ?>" <?= (isset($_GET['plan_id']) && $_GET['plan_id'] == $plan->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($plan->nombre) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <select class="form-control" name="estado" id="estado">
                                <option value="">Todos los estados</option>
                                <option value="Activa" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Activa') ? 'selected' : '' ?>>Activa</option>
                                <option value="Pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                                <option value="Suspendida" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Suspendida') ? 'selected' : '' ?>>Suspendida</option>
                                <option value="Cancelada" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Cancelada') ? 'selected' : '' ?>>Cancelada</option>
                                <option value="Finalizada" <?= (isset($_GET['estado']) && $_GET['estado'] == 'Finalizada') ? 'selected' : '' ?>>Finalizada</option>
                            </select>
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <select class="form-control" name="periodo" id="periodo">
                                <option value="">Todos los períodos</option>
                                <option value="Mensual" <?= (isset($_GET['periodo']) && $_GET['periodo'] == 'Mensual') ? 'selected' : '' ?>>Mensual</option>
                                <option value="Semestral" <?= (isset($_GET['periodo']) && $_GET['periodo'] == 'Semestral') ? 'selected' : '' ?>>Semestral</option>
                                <option value="Anual" <?= (isset($_GET['periodo']) && $_GET['periodo'] == 'Anual') ? 'selected' : '' ?>>Anual</option>
                            </select>
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <div class="checkbox checkbox-info">
                                <input id="vencidas" name="vencidas" type="checkbox" value="1" <?= (isset($_GET['vencidas']) && $_GET['vencidas'] == '1') ? 'checked' : '' ?>>
                                <label for="vencidas">Vencidas</label>
                            </div>
                        </div>
                        <div class="form-group m-r-10 m-b-10">
                            <select class="form-control" name="proximo_vencimiento" id="proximo_vencimiento">
                                <option value="">Próximo vencimiento</option>
                                <option value="7" <?= (isset($_GET['proximo_vencimiento']) && $_GET['proximo_vencimiento'] == '7') ? 'selected' : '' ?>>Próximos 7 días</option>
                                <option value="15" <?= (isset($_GET['proximo_vencimiento']) && $_GET['proximo_vencimiento'] == '15') ? 'selected' : '' ?>>Próximos 15 días</option>
                                <option value="30" <?= (isset($_GET['proximo_vencimiento']) && $_GET['proximo_vencimiento'] == '30') ? 'selected' : '' ?>>Próximos 30 días</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info waves-effect waves-light m-r-10 m-b-10">
                            <i class="fa fa-search"></i> Filtrar
                        </button>
                        <a href="<?= base_url ?>admin/suscripciones" class="btn btn-default waves-effect waves-light m-b-10">
                            <i class="fa fa-refresh"></i> Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <!-- Botón para crear nueva suscripción -->
            <div class="row m-t-10 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>admin/crearSuscripcion" class="btn btn-success waves-effect waves-light">
                        <i class="fa fa-plus"></i> Nueva Suscripción
                    </a>
                    <a href="javascript:void(0);" id="exportarSuscripciones" class="btn btn-info waves-effect waves-light m-l-10">
                        <i class="fa fa-file-excel-o"></i> Exportar a Excel
                    </a>
                </div>
            </div>
            
            <!-- Tabla de suscripciones -->
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="tablaSuscripciones">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>Plan</th>
                            <th>Inicio</th>
                            <th>Próximo Cobro</th>
                            <th>Período</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($suscripciones)): ?>
                            <?php foreach ($suscripciones as $suscripcion): ?>
                                <tr>
                                    <td><?= $suscripcion->id ?></td>
                                    <td>
                                        <a href="<?= base_url ?>admin/verEmpresa?id=<?= $suscripcion->empresa_id ?>" title="Ver empresa">
                                            <?= htmlspecialchars($suscripcion->empresa_nombre) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($suscripcion->plan_nombre) ?></td>
                                    <td><?= date('d/m/Y', strtotime($suscripcion->fecha_inicio)) ?></td>
                                    <td>
                                        <?php 
                                        if ($suscripcion->fecha_siguiente_factura) {
                                            $fecha_factura = strtotime($suscripcion->fecha_siguiente_factura);
                                            $hoy = time();
                                            $dias_restantes = round(($fecha_factura - $hoy) / 86400);
                                            
                                            echo date('d/m/Y', $fecha_factura);
                                            
                                            if ($dias_restantes < 0) {
                                                echo ' <span class="label label-danger">Vencida</span>';
                                            } elseif ($dias_restantes <= 7) {
                                                echo ' <span class="label label-warning">En ' . $dias_restantes . ' días</span>';
                                            }
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $suscripcion->periodo_facturacion ?></td>
                                    <td>
                                        <?php
                                        $simbolo = '';
                                        switch ($suscripcion->moneda) {
                                            case 'CLP': $simbolo = '$'; break;
                                            case 'USD': $simbolo = 'US$'; break;
                                            case 'EUR': $simbolo = '€'; break;
                                        }
                                        echo $simbolo . ' ' . number_format($suscripcion->precio_total, 2);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $estado_class = '';
                                        switch ($suscripcion->estado) {
                                            case 'Activa': $estado_class = 'label-success'; break;
                                            case 'Pendiente': $estado_class = 'label-warning'; break;
                                            case 'Suspendida': $estado_class = 'label-danger'; break;
                                            case 'Cancelada': $estado_class = 'label-default'; break;
                                            case 'Finalizada': $estado_class = 'label-info'; break;
                                        }
                                        ?>
                                        <span class="label <?= $estado_class ?>"><?= $suscripcion->estado ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url ?>admin/verSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url ?>admin/editarSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        
                                        <?php if ($suscripcion->estado == 'Activa'): ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $suscripcion->id ?>, 'Suspendida')" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                                <i class="fa fa-ban"></i>
                                            </a>
                                        <?php elseif ($suscripcion->estado == 'Suspendida'): ?>
                                            <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $suscripcion->id ?>, 'Activa')" class="btn btn-success btn-circle" data-toggle="tooltip" data-original-title="Activar">
                                                <i class="fa fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?= base_url ?>admin/historialSuscripcion?id=<?= $suscripcion->id ?>" class="btn btn-primary btn-circle" data-toggle="tooltip" data-original-title="Ver historial">
                                            <i class="fa fa-history"></i>
                                        </a>
                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-circle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="javascript:void(0);" onclick="confirmarRenovar(<?= $suscripcion->id ?>)"><i class="fa fa-refresh text-success"></i> Renovar</a></li>
                                                <li><a href="<?= base_url ?>admin/crearFactura?suscripcion_id=<?= $suscripcion->id ?>"><i class="fa fa-file-text-o text-info"></i> Generar Factura</a></li>
                                                <li><a href="javascript:void(0);" onclick="confirmarCancelar(<?= $suscripcion->id ?>)"><i class="fa fa-times text-danger"></i> Cancelar Suscripción</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron suscripciones</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_paginas > 1): ?>
                <div class="text-center m-t-20">
                    <ul class="pagination">
                        <li class="<?= $pagina <= 1 ? 'disabled' : '' ?>">
                            <a href="<?= $pagina <= 1 ? 'javascript:void(0);' : base_url . 'admin/suscripciones?' . http_build_query(array_merge($_GET, ['pagina' => $pagina - 1])) ?>">
                                <i class="fa fa-angle-left"></i>
                            </a>
                        </li>
                        
                        <?php for ($i = max(1, $pagina - 2); $i <= min($pagina + 2, $total_paginas); $i++): ?>
                            <li class="<?= $i == $pagina ? 'active' : '' ?>">
                                <a href="<?= $i == $pagina ? 'javascript:void(0);' : base_url . 'admin/suscripciones?' . http_build_query(array_merge($_GET, ['pagina' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="<?= $pagina >= $total_paginas ? 'disabled' : '' ?>">
                            <a href="<?= $pagina >= $total_paginas ? 'javascript:void(0);' : base_url . 'admin/suscripciones?' . http_build_query(array_merge($_GET, ['pagina' => $pagina + 1])) ?>">
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript para las operaciones con suscripciones -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Inicializar DataTable si está disponible
    if (typeof $ !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
        $('#tablaSuscripciones').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [[ 0, "desc" ]],
            "paging": false,
            "info": false,
            "searching": false
        });
    }
    
    // Exportar a Excel
    document.getElementById('exportarSuscripciones').addEventListener('click', function() {
        window.location.href = '<?= base_url ?>admin/exportarSuscripciones?' + new URLSearchParams(new FormData(document.getElementById('searchForm'))).toString();
    });
});

// Confirmar cambio de estado
function confirmarCambiarEstado(id, nuevoEstado) {
    let mensaje = '';
    if (nuevoEstado === 'Activa') {
        mensaje = '¿Está seguro de que desea activar esta suscripción?';
    } else if (nuevoEstado === 'Suspendida') {
        mensaje = '¿Está seguro de que desea suspender esta suscripción? Esto impedirá el acceso a la empresa.';
    } else {
        mensaje = '¿Está seguro de que desea cambiar el estado de la suscripción a ' + nuevoEstado + '?';
    }
    
    if (confirm(mensaje)) {
        window.location.href = '<?= base_url ?>admin/cambiarEstadoSuscripcion?id=' + id + '&estado=' + nuevoEstado;
    }
}

// Confirmar renovación
function confirmarRenovar(id) {
    if (confirm('¿Está seguro de que desea renovar esta suscripción? Esto actualizará la fecha de próximo cobro según el período de facturación.')) {
        window.location.href = '<?= base_url ?>admin/renovarSuscripcion?id=' + id;
    }
}

// Confirmar cancelación
function confirmarCancelar(id) {
    if (confirm('¿Está seguro de que desea cancelar esta suscripción? Esto impedirá el acceso a la empresa hasta que se reactive.')) {
        window.location.href = '<?= base_url ?>admin/cambiarEstadoSuscripcion?id=' + id + '&estado=Cancelada';
    }
}
</script>

<!-- Estilos adicionales -->
<style>
.btn-circle {
    border-radius: 100%;
    width: 30px;
    height: 30px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 5px;
}
.table th {
    font-weight: 600;
}
.label {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 12px;
    font-weight: 500;
}
.dropdown-menu {
    min-width: 200px;
}
.dropdown-menu > li > a {
    padding: 8px 20px;
}
.dropdown-menu > li > a i {
    margin-right: 8px;
}
.checkbox {
    margin-top: 10px;
    margin-bottom: 10px;
}
.checkbox label {
    padding-left: 25px;
}
.checkbox input[type="checkbox"] {
    margin-left: -25px;
}