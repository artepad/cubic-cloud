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





</div>
</div>
</div>

<script>
    // Asegurar que los tooltips se inicialicen correctamente
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
            $('[data-toggle="tooltip"]').tooltip();
        }

        // Inicializar el modal para usuarios
        $('#usuarioModal').on('shown.bs.modal', function() {
            $('#nombre').focus();
        });

        // Enlazar el botón "Nuevo Usuario" con el modal
        $('.btn-info[href="javascript:void(0);"]').first().on('click', function() {
            $('#usuarioModalLabel').text('Nuevo Usuario');
            $('#usuarioForm')[0].reset();
            $('#usuarioModal').modal('show');
        });
    });
</script>

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