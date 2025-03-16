<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url ?>assets/plugins/images/favicon.png">
    <title>Cubic Cloud - Recuperar Contraseña</title>
    <!-- Bootstrap CSS -->
    <link href="<?= base_url ?>assets/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animation CSS -->
    <link href="<?= base_url ?>assets/css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url ?>assets/css/style.css" rel="stylesheet">
    <!-- Color CSS -->
    <link href="<?= base_url ?>assets/css/colors/default.css" id="theme" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?= base_url ?>assets/plugins/components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
</head>

<body class="mini-sidebar">
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <section id="wrapper" class="login-register">
        <div class="login-box">
            <div class="white-box">
                <!-- Mensajes de error -->
                <?php if (isset($_SESSION['error_login'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error_login'] ?>
                    </div>
                <?php unset($_SESSION['error_login']);
                endif; ?>

                <form class="form-horizontal form-material" action="<?= base_url ?>usuario/requestReset" method="POST">
                    <h3 class="box-title m-b-20">Recuperar Contraseña</h3>
                    
                    <!-- Token CSRF para seguridad -->
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    
                    <div class="form-group">
                        <div class="col-xs-12">
                            <p>Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña.</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input class="form-control" type="email" required placeholder="Correo electrónico" name="email">
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Enviar Enlace de Recuperación</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url ?>usuario/login" class="text-dark"><i class="fa fa-arrow-left m-r-5"></i> Volver al inicio de sesión</a>
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
    <!-- Custom Theme JavaScript -->
    <script src="<?= base_url ?>assets/js/custom.js"></script>
</body>

</html>