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
                <?php if(isset($_SESSION['admin'])): ?>
                    <!-- Menú específico para superadmin -->
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/index" aria-expanded="false">
                            <i class="icon-screen-desktop fa-fw"></i>
                            <span class="hide-menu"> Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/usuarios" aria-expanded="false">
                            <i class="icon-people fa-fw"></i>
                            <span class="hide-menu"> Usuarios
                                <span class="label label-rounded label-info pull-right">54</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/empresas" aria-expanded="false">
                            <i class="icon-briefcase fa-fw"></i>
                            <span class="hide-menu"> Empresas
                                <span class="label label-rounded label-success pull-right">15</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/planes" aria-expanded="false">
                            <i class="icon-layers fa-fw"></i>
                            <span class="hide-menu"> Planes</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/suscripciones" aria-expanded="false">
                            <i class="icon-credit-card fa-fw"></i>
                            <span class="hide-menu"> Suscripciones</span>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect" href="<?=base_url?>systemDashboard/configuracion" aria-expanded="false">
                            <i class="icon-settings fa-fw"></i>
                            <span class="hide-menu"> Configuración</span>
                        </a>
                    </li>
                <?php elseif(isset($_SESSION['usuario'])): ?>
                    <!-- Menú para usuarios normales -->
                    <?php if($_SESSION['usuario']->tipo_usuario == 'ADMIN'): ?>
                        <!-- Menú específico para administradores de empresa -->
                        <li>
                            <a class="waves-effect" href="<?=base_url?>empresas/dashboard" aria-expanded="false">
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
                    <?php elseif($_SESSION['usuario']->tipo_usuario == 'VENDEDOR'): ?>
                        <!-- Menú específico para vendedores -->
                        <li>
                            <a class="waves-effect" href="<?=base_url?>ventas/dashboard" aria-expanded="false">
                                <i class="icon-screen-desktop fa-fw"></i>
                                <span class="hide-menu"> Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>ventas/clientes" aria-expanded="false">
                                <i class="icon-user fa-fw"></i>
                                <span class="hide-menu"> Clientes</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>ventas/cotizaciones" aria-expanded="false">
                                <i class="icon-doc fa-fw"></i>
                                <span class="hide-menu"> Cotizaciones</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>ventas/reportes" aria-expanded="false">
                                <i class="icon-chart fa-fw"></i>
                                <span class="hide-menu"> Reportes</span>
                            </a>
                        </li>
                    <?php elseif($_SESSION['usuario']->tipo_usuario == 'TOUR_MANAGER'): ?>
                        <!-- Menú específico para gestores de eventos -->
                        <li>
                            <a class="waves-effect" href="<?=base_url?>eventos/dashboard" aria-expanded="false">
                                <i class="icon-screen-desktop fa-fw"></i>
                                <span class="hide-menu"> Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>eventos/calendario" aria-expanded="false">
                                <i class="icon-calender fa-fw"></i>
                                <span class="hide-menu"> Calendario</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>eventos/giras" aria-expanded="false">
                                <i class="icon-map fa-fw"></i>
                                <span class="hide-menu"> Giras</span>
                            </a>
                        </li>
                        <li>
                            <a class="waves-effect" href="<?=base_url?>eventos/recursos" aria-expanded="false">
                                <i class="icon-wrench fa-fw"></i>
                                <span class="hide-menu"> Recursos</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- Menú para usuarios no autenticados -->
                    <li>
                        <a class="waves-effect" href="<?=base_url?>" aria-expanded="false">
                            <i class="icon-home fa-fw"></i>
                            <span class="hide-menu"> Inicio</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="p-30">
            <span class="hide-menu">
                <?php if(isset($_SESSION['admin'])): ?>
                    <a href="<?=base_url?>systemDashboard/empresas" class="btn btn-info m-b-10 btn-block">Nueva Empresa</a>
                    <a href="<?=base_url?>systemDashboard/usuarios" class="btn btn-success btn-block">Nuevo Usuario</a>
                    <a href="<?=base_url?>admin/logout" class="btn btn-danger m-t-15 btn-block">Cerrar Sesión</a>
                <?php elseif(isset($_SESSION['usuario'])): ?>
                    <?php if($_SESSION['usuario']->tipo_usuario == 'ADMIN' || $_SESSION['usuario']->tipo_usuario == 'VENDEDOR'): ?>
                        <a href="<?=base_url?>cliente/crear" class="btn btn-info m-b-10 btn-block">Nuevo Cliente</a>
                        <a href="<?=base_url?>evento/crear" class="btn btn-success btn-block">Nuevo Evento</a>
                    <?php elseif($_SESSION['usuario']->tipo_usuario == 'TOUR_MANAGER'): ?>
                        <a href="<?=base_url?>eventos/nuevo" class="btn btn-info m-b-10 btn-block">Nuevo Evento</a>
                        <a href="<?=base_url?>eventos/recursos/solicitar" class="btn btn-success btn-block">Solicitar Recursos</a>
                    <?php endif; ?>
                    <a href="<?=base_url?>usuario/logout" class="btn btn-danger m-t-15 btn-block">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="<?=base_url?>usuario/login" class="btn btn-info m-b-10 btn-block">Iniciar Sesión</a>
                    <a href="<?=base_url?>usuario/registro" class="btn btn-success btn-block">Registrarse</a>
                <?php endif; ?>
            </span>
        </div>
    </div>
</aside>
<!-- ===== Left-Sidebar-End ===== -->

<!-- Page-Content -->
<div class="page-wrapper">