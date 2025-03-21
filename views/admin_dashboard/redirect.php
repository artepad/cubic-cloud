<?php
// Forzar que no se envíen headers HTTP
ob_end_clean();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redireccionando...</title>
    <script>
        // Esperar 1 segundo y luego redirigir
        setTimeout(function() {
            window.location.href = "<?= base_url ?>systemDashboard/usuarios";
        }, 1000);
    </script>
</head>
<body>
    <div style="text-align: center; margin-top: 100px;">
        <h2>Operación completada correctamente</h2>
        <p>Serás redirigido en un momento...</p>
        <a href="<?= base_url ?>systemDashboard/usuarios">Haz clic aquí si no eres redirigido automáticamente</a>
    </div>
</body>
</html>