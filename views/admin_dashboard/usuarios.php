<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Usuarios</h3>
            <p class="text-muted">Administra los usuarios registrados en el sistema</p>

            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <a href="javascript:void(0);" class="btn btn-info waves-effect waves-light m-r-10">
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

<!-- Modal para crear/editar usuario -->
<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog" aria-labelledby="usuarioModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="usuarioModalLabel">Gestión de Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="usuarioForm" class="form-horizontal">
                    <div class="form-group">
                        <label for="nombre" class="col-sm-3 control-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del usuario" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apellido" class="col-sm-3 control-label">Apellido</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido del usuario" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email del usuario" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="telefono" class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Teléfono del usuario">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pais" class="col-sm-3 control-label">País</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="pais" name="pais" required>
                                <option value="">Seleccione un país...</option>
                                <option value="CL">Chile</option>
                                <option value="AR">Argentina</option>
                                <option value="CO">Colombia</option>
                                <option value="PE">Perú</option>
                                <option value="MX">México</option>
                                <option value="ES">España</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipo_identificacion" class="col-sm-3 control-label">Tipo de ID</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="tipo_identificacion" name="tipo_identificacion" required>
                                <option value="RUT">RUT (Chile)</option>
                                <option value="DNI">DNI (Argentina/España)</option>
                                <option value="CC">Cédula (Colombia)</option>
                                <option value="RFC">RFC (México)</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="numero_identificacion" class="col-sm-3 control-label">Número de ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" placeholder="Número de identificación">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tipo_usuario" class="col-sm-3 control-label">Tipo de usuario</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                                <option value="ADMIN">Administrador</option>
                                <option value="VENDEDOR">Vendedor</option>
                                <option value="TOUR_MANAGER">Tour Manager</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Contraseña</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                            <p class="help-block">Dejar en blanco para mantener la contraseña actual (en caso de edición)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm" class="col-sm-3 control-label">Confirmar Contraseña</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirmar contraseña">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="estado" class="col-sm-3 control-label">Estado</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="two_factor_status" class="col-sm-3 control-label">Autenticación 2FA</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="two_factor_status" name="two_factor_status">
                                <option value="Desactivado">Desactivado</option>
                                <option value="Activado">Activado</option>
                            </select>
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