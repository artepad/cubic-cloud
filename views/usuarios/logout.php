<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión - CUBIC Cloud</title>
    
    <!-- Estilos Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .logout-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .logout-title {
            color: #2c7be5;
            margin-bottom: 20px;
        }
        .spinner-border {
            color: #2c7be5;
            width: 3rem;
            height: 3rem;
            margin: 20px 0;
        }
        .logout-message {
            font-size: 18px;
            color: #495057;
            margin-bottom: 20px;
        }
        .countdown {
            font-weight: bold;
            color: #2c7be5;
        }
    </style>
    
    <!-- Script de redirección -->
    <script>
        // Contador de tiempo para la redirección
        let secondsLeft = 3;
        
        window.onload = function() {
            // Actualizar el contador cada segundo
            const countdownElement = document.getElementById('countdown');
            
            const interval = setInterval(function() {
                secondsLeft--;
                if (countdownElement) {
                    countdownElement.innerText = secondsLeft;
                }
                
                if (secondsLeft <= 0) {
                    clearInterval(interval);
                    window.location.href = "<?php echo base_url; ?>usuario/login";
                }
            }, 1000);
        };
    </script>
</head>
<body>
    <div class="container">
        <div class="logout-container">
            <h2 class="logout-title">Cerrando Sesión</h2>
            
            <div class="spinner-border" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            
            <p class="logout-message">
                Has cerrado sesión correctamente.<br>
                Serás redirigido a la página de inicio de sesión en <span id="countdown" class="countdown">3</span> segundos.
            </p>
            
            <a href="<?php echo base_url; ?>usuario/login" class="btn btn-primary">
                Ir al login ahora
            </a>
        </div>
    </div>
</body>
</html>