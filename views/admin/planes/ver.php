<?php
// Verificar que el usuario esté autenticado
if (!isAdminLoggedIn()) {
    redirectTo('admin/login');
}

// Verificar que existe el plan
if (!isset($plan) || !$plan) {
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
                    <div class="col-md-12">
                        <h3 class="panel-title" style="color: white; font-weight: bold;">Detalles del Plan</h3>
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2">Descripción:</label>
                                    <div class="col-md-10">
                                        <p class="form-control-static">
                                            <?= nl2br(htmlspecialchars($plan->descripcion)) ?>
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Precio Mensual:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_mensual, 2, ',', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Precio Semestral:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_semestral, 2, ',', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Precio Anual:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $simbolo . ' ' . number_format($plan->precio_anual, 2, ',', '.') ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de límites -->
                        <h3 class="box-title m-t-30">Límites</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Usuarios Máximos:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_usuarios == 0 ? 'Ilimitados' : $plan->max_usuarios ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Artistas Máximos:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_artistas == 0 ? 'Ilimitados' : $plan->max_artistas ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-md-6">Eventos Máximos:</label>
                                    <div class="col-md-6">
                                        <p class="form-control-static">
                                            <strong><?= $plan->max_eventos == 0 ? 'Ilimitados' : $plan->max_eventos ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de características -->
                        <h3 class="box-title m-t-30">Características</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-12">
                                <ul class="list-group">
                                    <?php if (isset($plan->caracteristicas_array)): ?>
                                        <?php
                                        // Mostrar características principales
                                        $caracteristicas = [
                                            'soporte_prioritario' => 'Soporte Prioritario',
                                            'soporte_telefonico' => 'Soporte Telefónico',
                                            'copias_seguridad' => 'Copias de Seguridad',
                                            'importar_contactos' => 'Importar Contactos',
                                            'exportar_pdf' => 'Exportar a PDF',
                                            'reportes_avanzados' => 'Reportes Avanzados'
                                        ];

                                        foreach ($caracteristicas as $key => $label):
                                            if (isset($plan->caracteristicas_array[$key]) && $plan->caracteristicas_array[$key]):
                                        ?>
                                                <li class="list-group-item">
                                                    <i class="fa fa-check text-success"></i> <?= $label ?>
                                                </li>
                                            <?php
                                            else:
                                            ?>
                                                <li class="list-group-item">
                                                    <i class="fa fa-times text-danger"></i> <?= $label ?>
                                                </li>
                                                <?php
                                            endif;
                                        endforeach;

                                        // Mostrar características adicionales si existen
                                        if (isset($plan->caracteristicas_array['adicionales']) && !empty($plan->caracteristicas_array['adicionales'])):
                                            $adicionales = explode(',', $plan->caracteristicas_array['adicionales']);
                                            foreach ($adicionales as $adicional):
                                                if (trim($adicional) !== ''):
                                                ?>
                                                    <li class="list-group-item">
                                                        <i class="fa fa-check text-success"></i> <?= htmlspecialchars(trim($adicional)) ?>
                                                    </li>
                                        <?php
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    <?php else: ?>
                                        <li class="list-group-item">No hay características disponibles</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Sección de estado -->
                        <h3 class="box-title m-t-30">Estado y Configuración</h3>
                        <hr class="m-t-0 m-b-20">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Estado:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <?php
                                            $estado_class = '';
                                            switch ($plan->estado) {
                                                case 'Activo':
                                                    $estado_class = 'label-success';
                                                    break;
                                                case 'Inactivo':
                                                    $estado_class = 'label-warning';
                                                    break;
                                                case 'Descontinuado':
                                                    $estado_class = 'label-danger';
                                                    break;
                                            }
                                            ?>
                                            <span class="label <?= $estado_class ?>"><?= $plan->estado ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Visibilidad:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <span class="label <?= $plan->visible == 'Si' ? 'label-info' : 'label-default' ?>">
                                                <?= $plan->visible == 'Si' ? 'Visible para clientes' : 'Oculto (solo administradores)' ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Fecha de creación:</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static">
                                            <strong><?= date('d/m/Y H:i', strtotime($plan->fecha_creacion)) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Última actualización:</label>
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

<!-- Estilos personalizados -->
<style>
    .panel-info>.panel-heading {
        background-color: #41b3f9;
        border-color: #41b3f9;
        color: #fff;
    }

    .box-title {
        font-weight: 600;
        font-size: 18px;
        margin-bottom: 5px;
    }

    .form-control-static {
        min-height: auto;
        padding-top: 0;
        font-size: 14px;
    }

    .list-group-item {
        border-radius: 0;
        padding: 10px 15px;
    }

    .list-group-item i {
        margin-right: 10px;
    }
</style>