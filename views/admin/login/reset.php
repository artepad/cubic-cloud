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
    <title>Cubic Cloud - Restablecer Contraseña</title>
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

                <form class="form-horizontal form-material" id="resetform" action="<?= base_url ?>admin/doReset" method="POST">
                    <h3 class="box-title m-b-20">Crear Nueva Contraseña</h3>
                    <input type="hidden" name="token" value="<?= isset($_GET['token']) ? htmlspecialchars($_GET['token']) : '' ?>">
                    
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required placeholder="Nueva contraseña" name="password" minlength="8">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required placeholder="Confirmar contraseña" name="confirm_password" minlength="8">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary p-t-0">
                                <p>Tu contraseña debe tener al menos 8 caracteres.</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Restablecer Contraseña</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url ?>admin/login" class="text-dark"><i class="fa fa-arrow-left m-r-5"></i> Volver al inicio de sesión</a>
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
    
    <script>
        // Validación en el lado del cliente
        document.getElementById('resetform').addEventListener('submit', function(e) {
            var password = document.getElementsByName('password')[0].value;
            var confirmPassword = document.getElementsByName('confirm_password')[0].value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
        });
    </script>
</body>

</html>