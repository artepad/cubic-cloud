<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Verificar que existe el plan
if (!isset($plan) || !$plan) {
    $_SESSION['error_message'] = "Plan no encontrado";
    redirectTo('plan/index');
}

// Convertir características de JSON a array si no está ya convertido
if (!isset($plan->caracteristicas_array) && isset($plan->caracteristicas)) {
    $plan->caracteristicas_array = json_decode($plan->caracteristicas, true);
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="panel-title" style="color: white; font-weight: bold;">Detalles del Plan</h3>
                    </div>
                    <div class="col-md-4 text-right">
                        <?php
                        $estado_class = '';
                        switch ($plan->estado) {
                            case 'Activo':
                                $estado_class = 'success';
                                break;
                            case 'Inactivo':
                                $estado_class = 'warning';
                                break;
                            case 'Descontinuado':
                                $estado_class = 'danger';
                                break;
                        }
                        ?>
                        <span class="label label-<?= $estado_class ?> m-r-5" style="font-size: 12px; padding: 5px 10px;">
                            <?= $plan->estado ?>
                        </span>
                        <?php if ($plan->visible == 'Si'): ?>
                            <span class="label label-info" style="font-size: 12px; padding: 5px 10px;">Visible</span>
                        <?php else: ?>
                            <span class="label label-default" style="font-size: 12px; padding: 5px 10px;">Oculto</span>
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

                    <!-- Información del plan -->
                    <div class="form-horizontal">
                        <!-- Sección de información básica -->
                        <h3 class="box-title">Información Básica</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Nombre:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($plan->nombre) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Tipo:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($plan->tipo_plan) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Descripción:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= nl2br(htmlspecialchars($plan->descripcion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Moneda:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= htmlspecialchars($plan->moneda) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de precios -->
                        <h3 class="box-title m-t-30">Precios</h3>
                        <hr class="m-t-0 m-b-20">

                        <?php
                        // Determinar símbolo de moneda
                        $simbolo = '';
                        switch ($plan->moneda) {
                            case 'CLP':
                                $simbolo = '$';
                                break;
                            case 'USD':
                                $simbolo = 'US$';
                                break;
                            case 'EUR':
                                $simbolo = '€';
                                break;
                        }
                        ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Precio Mensual:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_mensual, 0, '', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Precio Semestral:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_semestral, 0, '', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Precio Anual:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_anual, 0, '', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Visibilidad:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong>
                                                <?= $plan->visible == 'Si' ? 'Visible para clientes' : 'Oculto (solo administradores)' ?>
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de límites -->
                        <h3 class="box-title m-t-30">Límites</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Usuarios Máximos:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_usuarios == 0 ? 'Ilimitados' : $plan->max_usuarios ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Artistas Máximos:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_artistas == 0 ? 'Ilimitados' : $plan->max_artistas ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Eventos Máximos:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_eventos == 0 ? 'Ilimitados' : $plan->max_eventos ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Espacio reservado para mantener la estructura de dos columnas -->
                            </div>
                        </div>

                        <!-- Sección de características -->
                        <h3 class="box-title m-t-30">Características</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <?php if (isset($plan->caracteristicas_array)): ?>
                                                <?php
                                                // Definir características en pares para vista de 2 columnas
                                                $caracteristicas = [
                                                    'soporte_prioritario' => 'Soporte Prioritario',
                                                    'soporte_telefonico' => 'Soporte Telefónico',
                                                    'copias_seguridad' => 'Copias de Seguridad',
                                                    'importar_contactos' => 'Importar Contactos',
                                                    'exportar_pdf' => 'Exportar a PDF',
                                                    'reportes_avanzados' => 'Reportes Avanzados'
                                                ];

                                                $count = 0;
                                                $items_per_row = 2;
                                                
                                                foreach ($caracteristicas as $key => $label):
                                                    if ($count % $items_per_row == 0):
                                                        echo '<tr>';
                                                    endif;
                                                    
                                                    $icono = isset($plan->caracteristicas_array[$key]) && $plan->caracteristicas_array[$key] 
                                                        ? '<i class="fa fa-check text-success"></i>' 
                                                        : '<i class="fa fa-times text-danger"></i>';
                                                ?>
                                                <td width="50%">
                                                    <?= $icono ?> <?= $label ?>
                                                </td>
                                                <?php
                                                    $count++;
                                                    if ($count % $items_per_row == 0):
                                                        echo '</tr>';
                                                    endif;
                                                endforeach;
                                                
                                                // Si quedó una fila incompleta
                                                if ($count % $items_per_row != 0):
                                                    echo '<td></td></tr>';
                                                endif;
                                                
                                                // Mostrar características adicionales en pares
                                                if (isset($plan->caracteristicas_array['adicionales']) && !empty($plan->caracteristicas_array['adicionales'])):
                                                    $adicionales = explode(',', $plan->caracteristicas_array['adicionales']);
                                                    $count = 0;
                                                    
                                                    foreach ($adicionales as $adicional):
                                                        if (trim($adicional) !== ''):
                                                            if ($count % $items_per_row == 0):
                                                                echo '<tr>';
                                                            endif;
                                                ?>
                                                <td width="50%">
                                                    <i class="fa fa-check text-success"></i> <?= htmlspecialchars(trim($adicional)) ?>
                                                </td>
                                                <?php
                                                            $count++;
                                                            if ($count % $items_per_row == 0):
                                                                echo '</tr>';
                                                            endif;
                                                        endif;
                                                    endforeach;
                                                    
                                                    // Si quedó una fila incompleta
                                                    if ($count % $items_per_row != 0):
                                                        echo '<td></td></tr>';
                                                    endif;
                                                endif;
                                                ?>
                                            <?php else: ?>
                                                <tr><td>No hay características disponibles</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de información del sistema -->
                        <h3 class="box-title m-t-30">Información del Sistema</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Fecha de Creación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($plan->fecha_creacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Última Actualización:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($plan->fecha_actualizacion)) ?></strong>
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
                                            <li><a href="<?= base_url ?>plan/editar/<?= $plan->id ?>" style="color: #333;"><i class="fa fa-pencil"></i> Editar</a></li>
                                            <?php if ($plan->estado == 'Activo'): ?>
                                                <li><a href="<?= base_url ?>plan/cambiarEstado/<?= $plan->id ?>/Inactivo" class="text-danger" onclick="return confirm('¿Está seguro que desea desactivar este plan?')">
                                                        <i class="fa fa-ban"></i> Desactivar
                                                    </a></li>
                                            <?php else: ?>
                                                <li><a href="<?= base_url ?>plan/cambiarEstado/<?= $plan->id ?>/Activo" class="text-success" onclick="return confirm('¿Está seguro que desea activar este plan?')">
                                                        <i class="fa fa-check"></i> Activar
                                                    </a></li>
                                            <?php endif; ?>
                                            <?php if ($plan->visible == 'Si'): ?>
                                                <li><a href="<?= base_url ?>plan/cambiarVisibilidad/<?= $plan->id ?>/No" class="text-danger" onclick="return confirm('¿Está seguro que desea ocultar este plan?')">
                                                        <i class="fa fa-eye-slash"></i> Ocultar
                                                    </a></li>
                                            <?php else: ?>
                                                <li><a href="<?= base_url ?>plan/cambiarVisibilidad/<?= $plan->id ?>/Si" class="text-success" onclick="return confirm('¿Está seguro que desea hacer visible este plan?')">
                                                        <i class="fa fa-eye"></i> Hacer visible
                                                    </a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                    <a href="<?= base_url ?>plan/index" class="btn btn-info waves-effect waves-light m-r-10" style="color: #fff;">
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

<script>
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