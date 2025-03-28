<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Suscripciones</h3>
            <p class="text-muted">Administra las suscripciones de empresas en el sistema</p>
            
            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="javascript:void(0);" class="btn btn-info waves-effect waves-light m-r-10">
                        <i class="icon-plus"></i> Nueva Suscripción
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="icon-refresh"></i> Actualizar
                    </a>
                    <a href="javascript:void(0);" class="btn btn-warning waves-effect waves-light">
                        <i class="icon-cloud-download"></i> Exportar
                    </a>
                </div>
            </div>
            
            <!-- Tabla de suscripciones -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>Plan</th>
                            <th>Inicio</th>
                            <th>Próximo Cobro</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Eventos Santiago SPA</td>
                            <td>Profesional</td>
                            <td>01/01/2025</td>
                            <td>01/04/2025</td>
                            <td>$149.99</td>
                            <td><span class="label label-success">Activa</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                    <i class="icon-close"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Productor Nacional Ltda.</td>
                            <td>Premium</td>
                            <td>15/01/2025</td>
                            <td>15/04/2025</td>
                            <td>$299.99</td>
                            <td><span class="label label-success">Activa</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                    <i class="icon-close"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Música en Vivo SPA</td>
                            <td>Básico</td>
                            <td>10/02/2025</td>
                            <td>10/03/2025</td>
                            <td>$49.99</td>
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
                            <td>Profesional</td>
                            <td>05/02/2025</td>
                            <td>05/03/2025</td>
                            <td>$149.99</td>
                            <td><span class="label label-success">Activa</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                    <i class="icon-close"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Eventos Corporativos SA</td>
                            <td>Premium</td>
                            <td>15/12/2024</td>
                            <td>15/03/2025</td>
                            <td>$299.99</td>
                            <td><span class="label label-success">Activa</span></td>
                            <td>
                                <a href="javascript:void(0);" class="btn btn-info btn-circle" data-toggle="tooltip" data-original-title="Ver detalles">
                                    <i class="icon-eye"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-warning btn-circle" data-toggle="tooltip" data-original-title="Editar">
                                    <i class="icon-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" class="btn btn-danger btn-circle" data-toggle="tooltip" data-original-title="Cancelar">
                                    <i class="icon-close"></i>
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