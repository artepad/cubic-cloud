<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Gestión de Usuarios</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb justify-content-end">
                    <li class="breadcrumb-item"><a href="<?=base_url?>">Inicio</a></li>
                    <li class="breadcrumb-item active">Usuarios</li>
                </ol>
                <a href="<?=base_url?>systemDashboard/crearUsuario" class="btn btn-info d-none d-lg-block m-l-15 waves-effect waves-light">
                    <i class="fa fa-plus-circle"></i> Crear Usuario
                </a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <!-- Mostrar mensajes de éxito o error -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success_message'] ?>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error_message'] ?>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Listado de Usuarios</h4>
                    <h6 class="card-subtitle">Administra los usuarios del sistema</h6>
                    
                    <div class="table-responsive m-t-40">
                        <table id="usuariosTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Obtener lista de usuarios
                                $usuarioModel = new Usuario();
                                $usuarios = $usuarioModel->getAll();
                                
                                if ($usuarios && count($usuarios) > 0):
                                    foreach ($usuarios as $usuario): 
                                ?>
                                <tr>
                                    <td><?= $usuario->id ?></td>
                                    <td><?= htmlspecialchars($usuario->nombre) . ' ' . htmlspecialchars($usuario->apellido) ?></td>
                                    <td><?= htmlspecialchars($usuario->email) ?></td>
                                    <td><?= htmlspecialchars($usuario->tipo_usuario) ?></td>
                                    <td>
                                        <?php if ($usuario->estado == 'Activo'): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($usuario->fecha_creacion)) ?></td>
                                    <td>
                                        <a href="<?= base_url ?>systemDashboard/editarUsuario/<?= $usuario->id ?>" class="btn btn-info btn-sm">
                                            <i class="fa fa-pencil"></i> Editar
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-usuario" data-id="<?= $usuario->id ?>">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach; 
                                else: 
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay usuarios registrados</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para inicializar DataTable y manejar eliminación -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable
    $('#usuariosTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
        },
        order: [[5, 'desc']]
    });
    
    // Manejar eliminación de usuarios
    $('.btn-delete-usuario').click(function() {
        const usuarioId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= base_url ?>systemDashboard/eliminarUsuario/' + usuarioId;
            }
        });
    });
});
</script>