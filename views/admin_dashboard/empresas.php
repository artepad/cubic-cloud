<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Empresas</h3>
            <p class="text-muted">Administra las empresas registradas en el sistema</p>
            
            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="javascript:void(0);" class="btn btn-info waves-effect waves-light m-r-10">
                        <i class="icon-plus"></i> Nueva Empresa
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="icon-refresh"></i> Actualizar
                    </a>
                    <a href="javascript:void(0);" class="btn btn-warning waves-effect waves-light">
                        <i class="icon-cloud-download"></i> Exportar
                    </a>
                </div>
            </div>
            
            <!-- Tabla de empresas -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Identificación Fiscal</th>
                            <th>País</th>
                            <th>Plan</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Eventos Santiago SPA</td>
                            <td>76.123.456-7</td>
                            <td>Chile</td>
                            <td>Profesional</td>
                            <td><span class="label label-success">Activa</span></td>
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
                            <td>Productor Nacional Ltda.</td>
                            <td>77.654.321-8</td>
                            <td>Chile</td>
                            <td>Premium</td>
                            <td><span class="label label-success">Activa</span></td>
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
                            <td>Música en Vivo SPA</td>
                            <td>76.987.654-3</td>
                            <td>Chile</td>
                            <td>Básico</td>
                            <td><span class="label label-warning">Pendiente</span></td>
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
                            <td>Representante Sur SPA</td>
                            <td>76.789.123-5</td>
                            <td>Chile</td>
                            <td>Profesional</td>
                            <td><span class="label label-success">Activa</span></td>
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
                            <td>Eventos Corporativos SA</td>
                            <td>96.456.789-1</td>
                            <td>Chile</td>
                            <td>Premium</td>
                            <td><span class="label label-success">Activa</span></td>
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

<!-- Activar tooltips -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && typeof $.fn.tooltip !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});
</script>