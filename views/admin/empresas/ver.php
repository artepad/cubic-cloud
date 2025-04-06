<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Verificar que existe la empresa
if (!isset($empresa) || !$empresa) {
    $_SESSION['error_message'] = "Empresa no encontrada";
    redirectTo('empresa/index');
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
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="panel-title" style="color: white; font-weight: bold;">Detalles de la Empresa</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <span class="label label-<?= $empresa->estado == 'activa' ? 'success' : 'danger' ?> m-r-5" style="font-size: 12px; padding: 5px 10px;">
                            <?= $empresa->estado == 'activa' ? 'Activa' : 'Suspendida' ?>
                        </span>
                        <?php if ($empresa->es_demo == 'Si'): ?>
                            <span class="label label-warning" style="font-size: 12px; padding: 5px 10px;">Cuenta Demo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <!-- Mensajes de éxito y error -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= $_SESSION['success_message'] ?>
                            <?php unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <?= $_SESSION['error_message'] ?>
                            <?php unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Información de la empresa -->
                    <div class="form-horizontal">
                        <!-- Sección de información básica -->
                        <h3 class="box-title">Información Básica</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Nombre / Razón Social:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->nombre) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Identificación Fiscal:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->identificacion_fiscal ?: 'No especificada') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Dirección:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->direccion) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">País:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->pais . ' (' . $empresa->codigo_pais . ')') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Teléfono:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->telefono ?: 'No especificado') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Email de Contacto:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->email_contacto ?: 'No especificado') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Moneda Predeterminada:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->tipo_moneda) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Administrador:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong>
                                                <?php if (isset($admin) && $admin): ?>
                                                    <?= htmlspecialchars($admin->nombre . ' ' . $admin->apellido) ?>
                                                <?php else: ?>
                                                    Sin asignar
                                                <?php endif; ?>
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de datos de facturación -->
                        <h3 class="box-title m-t-30">Datos de Facturación</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Razón Social:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->razon_social_facturacion ?: $empresa->nombre) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Dirección:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->direccion_facturacion ?: $empresa->direccion) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Ciudad:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->ciudad_facturacion ?: 'No especificada') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Código Postal:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->codigo_postal ?: 'No especificado') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Email de Facturación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->email_facturacion ?: $empresa->email_contacto) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Contacto de Facturación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($empresa->contacto_facturacion ?: 'No especificado') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de plan y suscripción -->
                        <h3 class="box-title m-t-30">Plan y Suscripción</h3>
                        <hr class="m-t-0 m-b-20">

                        <?php if ($suscripcion && $plan): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Plan Actual:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <span class="label label-info"><?= htmlspecialchars($plan->nombre) ?></span>
                                                <span class="text-muted m-l-5">(<?= htmlspecialchars($plan->tipo_plan) ?>)</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Estado de Suscripción:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <span class="label label-<?= $suscripcion->estado == 'Activa' ? 'success' : 'warning' ?>">
                                                    <?= htmlspecialchars($suscripcion->estado) ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Período de Facturación:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong><?= htmlspecialchars($suscripcion->periodo_facturacion) ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Precio Mensual:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong>
                                                    <?php
                                                    $simbolo = '';
                                                    switch ($suscripcion->moneda) {
                                                        case 'CLP': $simbolo = '$'; break;
                                                        case 'USD': $simbolo = 'US$'; break;
                                                        case 'EUR': $simbolo = '€'; break;
                                                    }
                                                    echo $simbolo . ' ' . number_format($suscripcion->precio_total ?? 0, 2);
                                                    ?>
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Fecha de Inicio:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong><?= date('d/m/Y', strtotime($suscripcion->fecha_inicio)) ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Próxima Facturación:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong><?= date('d/m/Y', strtotime($suscripcion->fecha_siguiente_factura)) ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Esta empresa no tiene una suscripción activa.
                                <a href="<?= base_url ?>admin/crearSuscripcion?empresa_id=<?= $empresa->id ?>" class="alert-link">
                                    Crear suscripción
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Información adicional para cuentas demo -->
                        <?php if ($empresa->es_demo == 'Si'): ?>
                            <h3 class="box-title m-t-30">Información de Demo</h3>
                            <hr class="m-t-0 m-b-20">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Inicio de Demo:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong>
                                                    <?= $empresa->demo_inicio ? date('d/m/Y', strtotime($empresa->demo_inicio)) : 'No definido' ?>
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">Fin de Demo:</label>
                                        <div class="col-md-8">
                                            <p class="form-control-static">
                                                <strong>
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
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Sección de auditoría -->
                        <h3 class="box-title m-t-30">Información del Sistema</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Fecha de Creación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($empresa->fecha_creacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Última Actualización:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($empresa->fecha_actualizacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acciones -->
                        <div class="form-actions m-t-30">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div class="btn-group dropup m-r-10">
                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-warning dropdown-toggle waves-effect waves-light" type="button" style="color: #fff;">
                                            Opciones <span class="caret"></span>
                                        </button>
                                        <ul role="menu" class="dropdown-menu">
                                            <li><a href="<?= base_url ?>empresa/editar/<?= $empresa->id ?>" style="color: #333;"><i class="fa fa-pencil"></i> Editar</a></li>
                                            <?php if ($empresa->estado == 'activa'): ?>
                                                <li><a href="<?= base_url ?>empresa/cambiarEstado/<?= $empresa->id ?>/suspendida" class="text-danger" onclick="return confirm('¿Está seguro que desea suspender esta empresa? Esto impedirá el acceso a todos sus usuarios.')">
                                                    <i class="fa fa-ban"></i> Suspender
                                                </a></li>
                                            <?php else: ?>
                                                <li><a href="<?= base_url ?>empresa/cambiarEstado/<?= $empresa->id ?>/activa" class="text-success" onclick="return confirm('¿Está seguro que desea activar esta empresa?')">
                                                    <i class="fa fa-check"></i> Activar
                                                </a></li>
                                            <?php endif; ?>
                                            <li><a href="javascript:void(0);" onclick="confirmarEliminar(<?= $empresa->id ?>)" class="text-danger">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </a></li>
                                        </ul>
                                    </div>
                                    <a href="<?= base_url ?>empresa/index" class="btn btn-info waves-effect waves-light m-r-10" style="color: #fff;">
                                        <i class="fa fa-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para confirmaciones -->
<script>
// Confirmar eliminación
function confirmarEliminar(id) {
    if (confirm('¿Está seguro de que desea eliminar esta empresa? Esta acción no se puede deshacer y eliminará todos los datos asociados.')) {
        window.location.href = '<?= base_url ?>empresa/eliminar/' + id;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar componentes necesarios
    if (typeof $ !== 'undefined') {
        // Inicializar tooltips si están disponibles
        if (typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }
});
</script>