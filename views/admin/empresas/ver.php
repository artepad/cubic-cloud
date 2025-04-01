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

// Obtener información del plan y suscripción actual
$suscripcion = null;
$plan = null;

// Si existe el modelo de Suscripcion, intentar obtener la suscripción activa
if (class_exists('Suscripcion')) {
    $suscripcionModel = new Suscripcion();
    $suscripcion = $suscripcionModel->getActivaByEmpresa($empresa->id);
    
    // Si existe suscripción, obtener detalles del plan
    if ($suscripcion && class_exists('Plan')) {
        $planModel = new Plan();
        $plan = $planModel->getById($suscripcion->plan_id);
    }
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">Detalles de la Empresa</h3>
                    <div class="pull-right">
                        <a href="<?= base_url ?>admin/editarEmpresa?id=<?= $empresa->id ?>" class="btn btn-info btn-sm waves-effect waves-light">
                            <i class="fa fa-pencil"></i> Editar Empresa
                        </a>
                        <a href="<?= base_url ?>admin/empresas" class="btn btn-default btn-sm waves-effect waves-light m-l-5">
                            <i class="fa fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Estado y tipo de empresa -->
            <div class="m-t-20 m-b-20">
                <span class="label label-<?= $empresa->estado == 'activa' ? 'success' : 'danger' ?> m-r-10">
                    <?= $empresa->estado == 'activa' ? 'Activa' : 'Suspendida' ?>
                </span>
                <?php if ($empresa->es_demo == 'Si'): ?>
                    <span class="label label-warning">Cuenta Demo</span>
                <?php endif; ?>
            </div>

            <div class="row">
                <!-- Columna de información principal -->
                <div class="col-md-8">
                    <!-- Información básica -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-building m-r-5"></i> Información Básica</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Nombre / Razón Social</th>
                                        <td><?= htmlspecialchars($empresa->nombre) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Identificación Fiscal</th>
                                        <td><?= htmlspecialchars($empresa->identificacion_fiscal ?: 'No especificada') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dirección</th>
                                        <td><?= htmlspecialchars($empresa->direccion) ?></td>
                                    </tr>
                                    <tr>
                                        <th>País</th>
                                        <td><?= htmlspecialchars($empresa->pais) ?> (<?= htmlspecialchars($empresa->codigo_pais) ?>)</td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono</th>
                                        <td><?= htmlspecialchars($empresa->telefono ?: 'No especificado') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email de Contacto</th>
                                        <td><?= htmlspecialchars($empresa->email_contacto ?: 'No especificado') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Moneda Predeterminada</th>
                                        <td><?= htmlspecialchars($empresa->tipo_moneda) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha de Creación</th>
                                        <td><?= date('d/m/Y H:i', strtotime($empresa->fecha_creacion)) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Última Actualización</th>
                                        <td><?= date('d/m/Y H:i', strtotime($empresa->fecha_actualizacion)) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Información de suscripción y plan -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-credit-card m-r-5"></i> Plan y Suscripción</h4>
                        </div>
                        <div class="panel-body">
                            <?php if ($suscripcion && $plan): ?>
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <th width="30%">Plan Actual</th>
                                            <td>
                                                <span class="label label-info"><?= htmlspecialchars($plan->nombre) ?></span>
                                                <span class="text-muted m-l-5">(<?= htmlspecialchars($plan->tipo_plan) ?>)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Número de Suscripción</th>
                                            <td><?= htmlspecialchars($suscripcion->numero_suscripcion) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Estado de Suscripción</th>
                                            <td>
                                                <span class="label label-<?= $suscripcion->estado == 'Activa' ? 'success' : 'warning' ?>">
                                                    <?= htmlspecialchars($suscripcion->estado) ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Período de Facturación</th>
                                            <td><?= htmlspecialchars($suscripcion->periodo_facturacion) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Inicio</th>
                                            <td><?= date('d/m/Y', strtotime($suscripcion->fecha_inicio)) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Próxima Facturación</th>
                                            <td><?= date('d/m/Y', strtotime($suscripcion->fecha_siguiente_factura)) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Precio Mensual</th>
                                            <td>
                                                <?php
                                                $simbolo = '';
                                                switch ($suscripcion->moneda) {
                                                    case 'CLP': $simbolo = '$'; break;
                                                    case 'USD': $simbolo = 'US$'; break;
                                                    case 'EUR': $simbolo = '€'; break;
                                                }
                                                echo $simbolo . ' ' . number_format($suscripcion->precio_final, 2);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Límites Según Plan</th>
                                            <td>
                                                <ul class="list-unstyled">
                                                    <li><strong>Usuarios:</strong> <?= $plan->max_usuarios == 0 ? 'Ilimitados' : $plan->max_usuarios ?></li>
                                                    <li><strong>Eventos:</strong> <?= $plan->max_eventos == 0 ? 'Ilimitados' : $plan->max_eventos ?></li>
                                                    <li><strong>Artistas:</strong> <?= $plan->max_artistas == 0 ? 'Ilimitados' : $plan->max_artistas ?></li>
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Esta empresa no tiene una suscripción activa. 
                                    <a href="<?= base_url ?>admin/crearSuscripcion?empresa_id=<?= $empresa->id ?>" class="alert-link">
                                        Crear suscripción
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Datos de facturación -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-file-text-o m-r-5"></i> Datos de Facturación</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th width="30%">Razón Social</th>
                                        <td><?= htmlspecialchars($empresa->razon_social_facturacion ?: $empresa->nombre) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Dirección</th>
                                        <td><?= htmlspecialchars($empresa->direccion_facturacion ?: $empresa->direccion) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Ciudad</th>
                                        <td><?= htmlspecialchars($empresa->ciudad_facturacion ?: 'No especificada') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Código Postal</th>
                                        <td><?= htmlspecialchars($empresa->codigo_postal ?: 'No especificado') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email de Facturación</th>
                                        <td><?= htmlspecialchars($empresa->email_facturacion ?: $empresa->email_contacto) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Contacto de Facturación</th>
                                        <td><?= htmlspecialchars($empresa->contacto_facturacion ?: 'No especificado') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Columna lateral con información adicional -->
                <div class="col-md-4">
                    <!-- Administrador -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-user-circle-o m-r-5"></i> Administrador</h4>
                        </div>
                        <div class="panel-body">
                            <?php if (isset($admin) && $admin): ?>
                                <div class="text-center m-b-10">
                                    <img src="<?= base_url ?>assets/plugins/images/users/user.png" alt="Admin" class="img-circle" width="80">
                                </div>
                                <div class="text-center">
                                    <h4 class="m-t-0 m-b-5"><?= htmlspecialchars($admin->nombre . ' ' . $admin->apellido) ?></h4>
                                    <p class="text-muted m-b-5"><?= htmlspecialchars($admin->email) ?></p>
                                    <p class="text-muted m-b-0"><?= htmlspecialchars($admin->telefono ?: 'Sin teléfono registrado') ?></p>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <a href="<?= base_url ?>admin/editarUsuario?id=<?= $admin->id ?>" class="btn btn-info btn-sm">
                                        <i class="fa fa-pencil"></i> Editar Administrador
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    No se encontró información del administrador.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recursos visuales -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-image m-r-5"></i> Recursos Visuales</h4>
                        </div>
                        <div class="panel-body">
                            <div class="m-b-15">
                                <h5 class="box-title m-b-10">Logo Principal</h5>
                                <?php if (!empty($empresa->imagen_empresa)): ?>
                                    <img src="<?= base_url . $empresa->imagen_empresa ?>" alt="Logo" class="img-responsive img-thumbnail">
                                <?php else: ?>
                                    <div class="alert alert-info m-b-0">
                                        No hay logo disponible
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <hr>
                            
                            <div class="m-b-15">
                                <h5 class="box-title m-b-10">Logo para Documentos</h5>
                                <?php if (!empty($empresa->imagen_documento)): ?>
                                    <img src="<?= base_url . $empresa->imagen_documento ?>" alt="Logo Documentos" class="img-responsive img-thumbnail">
                                <?php else: ?>
                                    <div class="alert alert-info m-b-0">
                                        No hay logo para documentos disponible
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <hr>
                            
                            <div>
                                <h5 class="box-title m-b-10">Firma Digital</h5>
                                <?php if (!empty($empresa->imagen_firma)): ?>
                                    <img src="<?= base_url . $empresa->imagen_firma ?>" alt="Firma" class="img-responsive img-thumbnail">
                                <?php else: ?>
                                    <div class="alert alert-info m-b-0">
                                        No hay firma digital disponible
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cuenta Demo (si aplica) -->
                    <?php if ($empresa->es_demo == 'Si'): ?>
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h4 class="panel-title"><i class="fa fa-clock-o m-r-5"></i> Información de Demo</h4>
                        </div>
                        <div class="panel-body">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <th>Inicio de Demo</th>
                                        <td>
                                            <?= $empresa->demo_inicio ? date('d/m/Y', strtotime($empresa->demo_inicio)) : 'No definido' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Fin de Demo</th>
                                        <td>
                                            <?php 
                                            if ($empresa->demo_fin) {
                                                $fecha_fin = strtotime($empresa->demo_fin);
                                                $hoy = time();
                                                $dias_restantes = round(($fecha_fin - $hoy) / 86400);
                                                
                                                echo date('d/m/Y', $fecha_fin);
                                                
                                                if ($dias_restantes > 0) {
                                                    echo ' <span class="text-success">(' . $dias_restantes . ' días restantes)</span>';
                                                } else if ($dias_restantes == 0) {
                                                    echo ' <span class="text-warning">(Vence hoy)</span>';
                                                } else {
                                                    echo ' <span class="text-danger">(Vencido)</span>';
                                                }
                                            } else {
                                                echo 'No definido';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Acciones adicionales -->
            <div class="row m-t-20">
                <div class="col-md-12">
                    <div class="well">
                        <div class="btn-group">
                            <?php if ($empresa->estado == 'activa'): ?>
                                <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $empresa->id ?>, 'suspendida')" 
                                   class="btn btn-danger waves-effect waves-light">
                                    <i class="fa fa-ban"></i> Suspender Empresa
                                </a>
                            <?php else: ?>
                                <a href="javascript:void(0);" onclick="confirmarCambiarEstado(<?= $empresa->id ?>, 'activa')" 
                                   class="btn btn-success waves-effect waves-light">
                                    <i class="fa fa-check"></i> Activar Empresa
                                </a>
                            <?php endif; ?>
                            
                            <a href="javascript:void(0);" onclick="confirmarEliminar(<?= $empresa->id ?>)" 
                               class="btn btn-danger waves-effect waves-light m-l-10">
                                <i class="fa fa-trash"></i> Eliminar Empresa
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para confirmaciones -->
<script>
// Confirmar cambio de estado
function confirmarCambiarEstado(id, nuevoEstado) {
    let mensaje = nuevoEstado === 'activa' 
        ? '¿Está seguro de que desea activar esta empresa?'
        : '¿Está seguro de que desea suspender esta empresa? Esto impedirá el acceso a todos sus usuarios.';
    
    if (confirm(mensaje)) {
        window.location.href = '<?= base_url ?>admin/cambiarEstadoEmpresa?id=' + id + '&estado=' + nuevoEstado;
    }
}

// Confirmar eliminación
function confirmarEliminar(id) {
    if (confirm('¿Está seguro de que desea eliminar esta empresa? Esta acción no se puede deshacer y eliminará todos los datos asociados.')) {
        window.location.href = '<?= base_url ?>admin/eliminarEmpresa?id=' + id;
    }
}
</script>

<!-- Estilos adicionales -->
<style>
.panel {
    border-radius: 5px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
}
.panel-warning {
    border-color: #ffb22b;
}
.panel-warning .panel-heading {
    background-color: #ffb22b;
    border-color: #ffb22b;
    color: white;
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
.table {
    margin-bottom: 0;
}
.table > tbody > tr > th {
    background-color: #f9f9f9;
}
.label {
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 12px;
    font-weight: 500;
}
.text-muted {
    color: #777;
}
.m-b-0 { margin-bottom: 0 !important; }
.m-b-5 { margin-bottom: 5px !important; }
.m-b-10 { margin-bottom: 10px !important; }
.m-b-15 { margin-bottom: 15px !important; }
.m-t-0 { margin-top: 0 !important; }
.m-t-10 { margin-top: 10px !important; }
.m-t-20 { margin-top: 20px !important; }
.m-l-5 { margin-left: 5px !important; }
.m-l-10 { margin-left: 10px !important; }
.m-r-5 { margin-right: 5px !important; }
.m-r-10 { margin-right: 10px !important; }
</style>