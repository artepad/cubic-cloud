<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title">Gestión de Usuarios</h3>
            <p class="text-muted">Administra los usuarios registrados en el sistema</p>
            
            <!-- Acciones de gestión -->
            <div class="row m-t-20 m-b-20">
                <div class="col-md-12">
                    <button id="btnNuevoUsuario" class="btn btn-info waves-effect waves-light m-r-10">
                        <i class="fa fa-plus"></i> Nuevo Usuario
                    </button>
                    <button id="btnActualizarLista" class="btn btn-success waves-effect waves-light m-r-10">
                        <i class="fa fa-refresh"></i> Actualizar
                    </button>
                    <button id="btnExportarUsuarios" class="btn btn-warning waves-effect waves-light">
                        <i class="fa fa-download"></i> Exportar
                    </button>
                </div>
            </div>
            
            <!-- Panel de filtros -->
            <div class="panel panel-default m-b-20">
                <div class="panel-heading" data-toggle="collapse" data-target="#collapseFiltros">
                    Filtros de búsqueda <span class="pull-right"><i class="fa fa-chevron-down"></i></span>
                </div>
                <div class="panel-body collapse" id="collapseFiltros">
                    <form id="formFiltros" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Buscar</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="filtroGeneral" placeholder="Nombre, apellido o email">
                            </div>
                            <label class="col-md-1 control-label">Rol</label>
                            <div class="col-md-2">
                                <select class="form-control" id="filtroRol">
                                    <option value="">Todos</option>
                                    <option value="ADMIN">Administrador</option>
                                    <option value="VENDEDOR">Vendedor</option>
                                    <option value="TOUR_MANAGER">Tour Manager</option>
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Estado</label>
                            <div class="col-md-2">
                                <select class="form-control" id="filtroEstado">
                                    <option value="">Todos</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">País</label>
                            <div class="col-md-3">
                                <select class="form-control" id="filtroPais">
                                    <option value="">Todos</option>
                                    <option value="Chile">Chile</option>
                                    <option value="Argentina">Argentina</option>
                                    <option value="México">México</option>
                                </select>
                            </div>
                            <label class="col-md-2 control-label">Fecha registro</label>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="filtroFechaDesde" placeholder="Desde">
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control" id="filtroFechaHasta" placeholder="Hasta">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-info" id="btnFiltrar">
                                    <i class="fa fa-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped" id="tablaUsuarios">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>País</th>
                            <th>Rol</th>
                            <th>Último Acceso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Juan Perez</td>
                            <td>juan.perez@ejemplo.com</td>
                            <td>Chile</td>
                            <td>ADMIN</td>
                            <td>15/03/2025 08:45</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <button class="btn btn-info btn-circle btn-ver" data-id="1" data-toggle="tooltip" title="Ver detalles">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-circle btn-editar" data-id="1" data-toggle="tooltip" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-circle btn-desactivar" data-id="1" data-toggle="tooltip" title="Desactivar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>María González</td>
                            <td>maria.gonzalez@ejemplo.com</td>
                            <td>Chile</td>
                            <td>VENDEDOR</td>
                            <td>14/03/2025 14:20</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <button class="btn btn-info btn-circle btn-ver" data-id="2" data-toggle="tooltip" title="Ver detalles">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-circle btn-editar" data-id="2" data-toggle="tooltip" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-circle btn-desactivar" data-id="2" data-toggle="tooltip" title="Desactivar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Carlos Rodríguez</td>
                            <td>carlos.rodriguez@ejemplo.com</td>
                            <td>Argentina</td>
                            <td>TOUR_MANAGER</td>
                            <td>13/03/2025 10:15</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <button class="btn btn-info btn-circle btn-ver" data-id="3" data-toggle="tooltip" title="Ver detalles">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-circle btn-editar" data-id="3" data-toggle="tooltip" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-circle btn-desactivar" data-id="3" data-toggle="tooltip" title="Desactivar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Ana Martínez</td>
                            <td>ana.martinez@ejemplo.com</td>
                            <td>México</td>
                            <td>VENDEDOR</td>
                            <td>12/03/2025 09:30</td>
                            <td><span class="label label-warning">Inactivo</span></td>
                            <td>
                                <button class="btn btn-info btn-circle btn-ver" data-id="4" data-toggle="tooltip" title="Ver detalles">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-circle btn-editar" data-id="4" data-toggle="tooltip" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-success btn-circle btn-activar" data-id="4" data-toggle="tooltip" title="Activar">
                                    <i class="fa fa-check"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Roberto Sánchez</td>
                            <td>roberto.sanchez@ejemplo.com</td>
                            <td>Chile</td>
                            <td>ADMIN</td>
                            <td>10/03/2025 16:45</td>
                            <td><span class="label label-success">Activo</span></td>
                            <td>
                                <button class="btn btn-info btn-circle btn-ver" data-id="5" data-toggle="tooltip" title="Ver detalles">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-circle btn-editar" data-id="5" data-toggle="tooltip" title="Editar">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-circle btn-desactivar" data-id="5" data-toggle="tooltip" title="Desactivar">
                                    <i class="fa fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="text-center m-t-20">
                <ul class="pagination">
                    <li class="disabled">
                        <a href="javascript:void(0);"><i class="fa fa-chevron-left"></i></a>
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
                        <a href="javascript:void(0);"><i class="fa fa-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div id="modalUsuario" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Nuevo Usuario</h4>
            </div>
            <div class="modal-body">
                <form id="formUsuario" class="form-horizontal">
                    <input type="hidden" id="usuario_id" name="usuario_id" value="">
                    
                    <!-- Información personal -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Apellido *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="apellido" name="apellido" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Email *</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Teléfono</label>
                        <div class="col-md-9">
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                    </div>
                    
                    <!-- Identificación y localización -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">País *</label>
                        <div class="col-md-9">
                            <select class="form-control" id="pais" name="pais" required>
                                <option value="Chile">Chile</option>
                                <option value="Argentina">Argentina</option>
                                <option value="México">México</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Identificación</label>
                        <div class="col-md-4">
                            <select class="form-control" id="tipo_identificacion" name="tipo_identificacion">
                                <option value="RUT">RUT</option>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" id="numero_identificacion" name="numero_identificacion" placeholder="Número">
                        </div>
                    </div>
                    
                    <!-- Configuración del usuario -->
                    <div class="form-group">
                        <label class="col-md-3 control-label">Rol *</label>
                        <div class="col-md-4">
                            <select class="form-control" id="tipo_usuario" name="tipo_usuario" required>
                                <option value="ADMIN">Administrador</option>
                                <option value="VENDEDOR">Vendedor</option>
                                <option value="TOUR_MANAGER">Tour Manager</option>
                            </select>
                        </div>
                        <label class="col-md-2 control-label">Estado *</label>
                        <div class="col-md-3">
                            <select class="form-control" id="estado" name="estado" required>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Seguridad - solo visible al crear usuario -->
                    <div id="seccionSeguridad">
                        <hr>
                        <h4 class="m-t-0">Credenciales de Acceso</h4>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Contraseña *</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="text-muted">Mínimo 8 caracteres, incluir mayúscula, minúscula y número</small>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Confirmar *</label>
                            <div class="col-md-9">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para acciones -->
<div id="modalConfirmacion" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirmar acción</h4>
            </div>
            <div class="modal-body">
                <p id="textoConfirmacion">¿Está seguro de realizar esta acción?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAccion">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript específico para esta página -->
<?php 
// Definir script específico para esta página
$pageSpecificScripts = '
<script src="'.base_url.'assets/js/usuarios.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Inicializar tooltips
    if (typeof $ !== "undefined" && typeof $.fn.tooltip !== "undefined") {
        $("[data-toggle=\'tooltip\']").tooltip();
    }
    
    // Inicializar eventos
    document.getElementById("btnNuevoUsuario").addEventListener("click", function() {
        abrirModalUsuario();
    });
    
    // Configurar otros eventos
    configurarEventosUsuarios();
});

// Función para abrir modal de usuario
function abrirModalUsuario(id = null) {
    // Resetear formulario
    document.getElementById("formUsuario").reset();
    document.getElementById("usuario_id").value = "";
    
    // Mostrar u ocultar sección de seguridad
    document.getElementById("seccionSeguridad").style.display = id ? "none" : "block";
    
    // Cambiar título según sea crear o editar
    document.querySelector(".modal-title").textContent = id ? "Editar Usuario" : "Nuevo Usuario";
    
    // Si es edición, cargar datos
    if (id) {
        // Simulación - en producción haría una llamada AJAX
        cargarDatosUsuario(id);
    }
    
    // Mostrar modal
    $("#modalUsuario").modal("show");
}

// Función para configurar eventos
function configurarEventosUsuarios() {
    // Eventos para botones de acción en la tabla
    document.querySelectorAll(".btn-ver").forEach(function(btn) {
        btn.addEventListener("click", function() {
            verDetallesUsuario(this.getAttribute("data-id"));
        });
    });
    
    document.querySelectorAll(".btn-editar").forEach(function(btn) {
        btn.addEventListener("click", function() {
            abrirModalUsuario(this.getAttribute("data-id"));
        });
    });
    
    document.querySelectorAll(".btn-desactivar, .btn-activar").forEach(function(btn) {
        btn.addEventListener("click", function() {
            const accion = this.classList.contains("btn-desactivar") ? "desactivar" : "activar";
            confirmarAccion(accion, this.getAttribute("data-id"));
        });
    });
    
    // Evento para guardar usuario
    document.getElementById("btnGuardarUsuario").addEventListener("click", function() {
        guardarUsuario();
    });
    
    // Evento para confirmar acción
    document.getElementById("btnConfirmarAccion").addEventListener("click", function() {
        ejecutarAccionConfirmada();
        $("#modalConfirmacion").modal("hide");
    });
}

// Otras funciones auxiliares (simuladas)
function cargarDatosUsuario(id) {
    // Simulación - en producción haría una llamada AJAX
    setTimeout(function() {
        const datos = {
            nombre: "Juan",
            apellido: "Pérez",
            email: "juan.perez@ejemplo.com",
            telefono: "+56912345678",
            pais: "Chile",
            tipo_identificacion: "RUT",
            numero_identificacion: "12.345.678-9",
            tipo_usuario: "ADMIN",
            estado: "Activo"
        };
        
        // Llenar formulario
        document.getElementById("nombre").value = datos.nombre;
        document.getElementById("apellido").value = datos.apellido;
        document.getElementById("email").value = datos.email;
        document.getElementById("telefono").value = datos.telefono;
        document.getElementById("pais").value = datos.pais;
        document.getElementById("tipo_identificacion").value = datos.tipo_identificacion;
        document.getElementById("numero_identificacion").value = datos.numero_identificacion;
        document.getElementById("tipo_usuario").value = datos.tipo_usuario;
        document.getElementById("estado").value = datos.estado;
    }, 300);
}

function verDetallesUsuario(id) {
    // Simulación - en producción haría una llamada AJAX
    alert("Ver detalles del usuario ID: " + id);
}

function confirmarAccion(accion, id) {
    const mensaje = accion === "activar" ? 
        "¿Está seguro que desea activar este usuario?" : 
        "¿Está seguro que desea desactivar este usuario?";
    
    document.getElementById("textoConfirmacion").textContent = mensaje;
    document.getElementById("btnConfirmarAccion").setAttribute("data-accion", accion);
    document.getElementById("btnConfirmarAccion").setAttribute("data-id", id);
    
    $("#modalConfirmacion").modal("show");
}

function ejecutarAccionConfirmada() {
    const btn = document.getElementById("btnConfirmarAccion");
    const accion = btn.getAttribute("data-accion");
    const id = btn.getAttribute("data-id");
    
    // Simulación - en producción haría una llamada AJAX
    alert("Ejecutando acción: " + accion + " para usuario ID: " + id);
}

function guardarUsuario() {
    // Validar formulario
    const form = document.getElementById("formUsuario");
    if (!form.checkValidity()) {
        alert("Por favor, complete todos los campos obligatorios");
        return;
    }
    
    // Simulación - en producción haría una llamada AJAX
    alert("Guardando usuario...");
    $("#modalUsuario").modal("hide");
}
</script>
';
?>