<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Usuarios</h3>
            <p class="text-muted">Administra los usuarios registrados en el sistema</p>

            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="<?= base_url ?>systemDashboard/crearUsuario" class="btn btn-info waves-effect waves-light m-r-10">
                        <i class="icon-plus"></i> Nuevo Usuario
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="icon-refresh"></i> Actualizar
                    </a>
                    <a href="javascript:void(0);" class="btn btn-warning waves-effect waves-light">
                        <i class="icon-cloud-download"></i> Exportar
                    </a>
                </div>
            </div>

            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>País</th>
                            <th>Identificación</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>María González</td>
                            <td>maria.gonzalez@example.com</td>
                            <td>ADMIN</td>
                            <td>Chile</td>
                            <td>12.345.678-9</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Carlos Ramírez</td>
                            <td>carlos.ramirez@example.com</td>
                            <td>VENDEDOR</td>
                            <td>Chile</td>
                            <td>14.789.325-6</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Ana Martínez</td>
                            <td>ana.martinez@example.com</td>
                            <td>TOUR_MANAGER</td>
                            <td>España</td>
                            <td>45678912B</td>
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
                        <tr>
                            <td>4</td>
                            <td>Sebastián López</td>
                            <td>sebastian.lopez@example.com</td>
                            <td>ADMIN</td>
                            <td>Chile</td>
                            <td>17.456.789-8</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                    <i class="icon-ban"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Valentina Torres</td>
                            <td>valentina.torres@example.com</td>
                            <td>VENDEDOR</td>
                            <td>Argentina</td>
                            <td>28456789</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Suspender">
                                    <i class="icon-ban"></i>
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
                        <a href="javascript:void(0);">3</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);"><i class="icon-arrow-right-circle"></i></a>
                    </li>
                </ul>
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
    });
</script>