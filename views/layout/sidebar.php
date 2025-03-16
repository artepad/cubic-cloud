<!-- ===== Left-Sidebar ===== -->
<aside class="sidebar">
    <div class="scroll-sidebar">
        <div class="user-profile">
            <div class="dropdown user-pro-body">
                <div class="profile-image">
                    <img src="<?=base_url?>assets/plugins/images/users/logo.png" alt="user-img" class="img-circle">
                </div>
                <p class="profile-text m-t-15 font-16">
                    <?php if(isset($_SESSION['admin'])): ?>
                        <a href="javascript:void(0);"><?= $_SESSION['admin']->nombre ?> <?= $_SESSION['admin']->apellido ?></a>
                    <?php elseif(isset($_SESSION['usuario'])): ?>
                        <a href="javascript:void(0);"><?= $_SESSION['usuario']->nombre ?> <?= $_SESSION['usuario']->apellido ?></a>
                    <?php else: ?>
                        <a href="javascript:void(0);">Cubic Cloud</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <nav class="sidebar-nav">
            <ul id="side-menu">
                <li>
                    <a class="waves-effect" href="<?=base_url?>dashboard/index" aria-expanded="false">
                        <i class="icon-screen-desktop fa-fw"></i>
                        <span class="hide-menu"> Dashboard
                            <span class="label label-rounded label-success pull-right">5</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="<?=base_url?>agenda/listar" aria-expanded="false">
                        <i class="icon-notebook fa-fw"></i>
                        <span class="hide-menu">Agenda
                            <span class="label label-rounded label-warning pull-right">12</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a class="waves-effect" href="<?=base_url?>cliente/listar" aria-expanded="false">
                        <i class="icon-user fa-fw"></i>
                        <span class="hide-menu"> Clientes
                            <span class="label label-rounded label-info pull-right">24</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a class="waves-effect" href="<?=base_url?>artista/listar" aria-expanded="false">
                        <i class="icon-microphone fa-fw"></i>
                        <span class="hide-menu">Artistas
                            <span class="label label-rounded label-primary pull-right">18</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="<?=base_url?>calendario/listar" aria-expanded="false">
                        <i class="icon-calender fa-fw"></i>
                        <span class="hide-menu">Calendario</span>
                    </a>
                </li>
                <li>
                    <a href="<?=base_url?>configuracion/index" aria-expanded="false">
                        <i class="icon-settings fa-fw"></i>
                        <span class="hide-menu">Configuración</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-30">
            <span class="hide-menu">
                <a href="<?=base_url?>cliente/crear" class="btn btn-info m-b-10 btn-block">Nuevo Cliente</a>
                <a href="<?=base_url?>evento/crear" class="btn btn-success btn-block">Nuevo Evento</a>
                <?php if(isset($_SESSION['admin'])): ?>
                    <a href="<?=base_url?>admin/logout" class="btn btn-danger m-t-15 btn-block">Cerrar Sesión</a>
                <?php elseif(isset($_SESSION['usuario'])): ?>
                    <a href="<?=base_url?>usuario/logout" class="btn btn-danger m-t-15 btn-block">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="<?=base_url?>" class="btn btn-danger m-t-15 btn-block">Volver al inicio</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</aside>
<!-- ===== Left-Sidebar-End ===== -->

<!-- Page-Content -->
<div class="page-wrapper">