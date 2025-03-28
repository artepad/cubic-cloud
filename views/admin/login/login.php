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
    <title>Cubic Cloud - Panel de Administración</title>
    <!-- ===== Bootstrap CSS ===== -->
    <link href="<?= base_url ?>assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ===== Animation CSS ===== -->
    <link href="<?= base_url ?>assets/css/animate.css" rel="stylesheet">
    <!-- ===== Custom CSS ===== -->
    <link href="<?= base_url ?>assets/css/style.css" rel="stylesheet">
    <!-- ===== Color CSS ===== -->
    <link href="<?= base_url ?>assets/css/colors/default.css" id="theme" rel="stylesheet">
</head>

<body class="mini-sidebar">
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <section id="wrapper" class="login-register">
        <div class="login-box">
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

                <form class="form-horizontal form-material" id="loginform" action="<?= base_url ?>admin/validate" method="POST">
                    <h3 class="box-title m-b-20">Panel de Administración</h3>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="email" required="" placeholder="Correo electrónico" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" placeholder="Contraseña" name="password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary pull-left p-t-0">
                                <input id="checkbox-signup" type="checkbox" name="remember" value="1">
                                <label for="checkbox-signup"> Recuérdame </label>
                            </div>
                            <a href="<?= base_url ?>admin/recover" class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> ¿Olvidaste la contraseña?</a>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">ACCEDER AL PANEL</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- jQuery -->
    <script src="<?= base_url ?>assets/plugins/components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?= base_url ?>assets/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="<?= base_url ?>assets/js/sidebarmenu.js"></script>
    <!--slimscroll JavaScript -->
    <script src="<?= base_url ?>assets/js/jquery.slimscroll.js"></script>
    <!--Wave Effects -->
    <script src="<?= base_url ?>assets/js/waves.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="<?= base_url ?>assets/js/custom.js"></script>
    <script src="<?= base_url ?>assets/plugins/components/styleswitcher/jQuery.style.switcher.js"></script>
</body>

</html>