<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url ?>assets/plugins/images/favicon.png">
    <title>Cubic Cloud - Registro de Usuario</title>
    <!-- ===== Bootstrap CSS ===== -->
    <link href="<?= base_url ?>assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ===== Animation CSS ===== -->
    <link href="<?= base_url ?>assets/css/animate.css" rel="stylesheet">
    <!-- ===== Custom CSS ===== -->
    <link href="<?= base_url ?>assets/css/style.css" rel="stylesheet">
    <!-- ===== Color CSS ===== -->
    <link href="<?= base_url ?>assets/css/colors/default.css" id="theme" rel="stylesheet">
    <!-- ===== Font Awesome ===== -->
    <link href="<?= base_url ?>assets/plugins/components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body class="mini-sidebar">
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <section id="wrapper" class="login-register">
        <div class="login-box" style="max-width: 500px;">
            <div class="white-box">
                <!-- Mostrar mensajes de error si existen -->
                <?php if (isset($_SESSION['error_login'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_login'] ?>
                    </div>
                <?php unset($_SESSION['error_login']);
                endif; ?>

                <!-- Mostrar mensajes de éxito si existen -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                    </div>
                <?php unset($_SESSION['success_message']);
                endif; ?>

                <form class="form-horizontal form-material" id="registerform" action="<?= base_url ?>usuario/save" method="POST">
                    <h3 class="box-title m-b-20">Registro de Usuarios</h3>
                    
                    <!-- Token CSRF para seguridad -->
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required placeholder="Nombre" name="nombre">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required placeholder="Apellido" name="apellido">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="email" required placeholder="Correo electrónico" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required placeholder="Contraseña" name="password" id="password" minlength="8">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required placeholder="Confirmar contraseña" name="confirm_password" id="confirm_password" minlength="8">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary p-t-0">
                                <p>La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula y un número.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">REGISTRARSE</button>
                        </div>
                    </div>
                    
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>¿Ya tienes una cuenta? <a href="<?= base_url ?>usuario/login" class="text-primary m-l-5"><b>Inicia sesión</b></a></p>
                        </div>
                    </div>
                </form>