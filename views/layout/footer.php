</div>
    </div>
</div>
<!-- ===== Page-Content-End ===== -->
</div>
<!-- ===== Main-Wrapper-End ===== -->

<script>
// Confirmación para cerrar sesión
document.addEventListener('DOMContentLoaded', function() {
    // Buscar todos los enlaces de cierre de sesión
    const logoutLinks = document.querySelectorAll('a[href*="admin/logout"]');
    
    // Añadir evento de confirmación a cada enlace
    logoutLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            if (!confirm('¿Estás seguro de que deseas cerrar sesión?')) {
                event.preventDefault();
            }
        });
    });
});
</script>

<footer class="footer t-a-c">
    © <?php echo date("Y"); ?> Cubic Cloud
    <span class="version-text">v 0.0.0</span>
</footer>

<!-- Required JS Files -->
<script src="<?=base_url?>assets/plugins/components/jquery/dist/jquery.min.js"></script>
<script src="<?=base_url?>assets/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?=base_url?>assets/js/jquery.slimscroll.js"></script>
<script src="<?=base_url?>assets/js/waves.js"></script>
<script src="<?=base_url?>assets/js/sidebarmenu.js"></script>
<script src="<?=base_url?>assets/js/custom.js"></script>
<script src="<?=base_url?>assets/plugins/components/chartist-js/dist/chartist.min.js"></script>
<script src="<?=base_url?>assets/plugins/components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
<script src='<?=base_url?>assets/plugins/components/moment/moment.js'></script>
<script src='<?=base_url?>assets/plugins/components/fullcalendar/fullcalendar.js'></script>
<script src="<?=base_url?>assets/js/db2.js"></script>
<script src="<?=base_url?>assets/plugins/components/styleswitcher/jQuery.style.switcher.js"></script>

<!-- Page-specific scripts -->
<?php if (isset($pageSpecificScripts)) echo $pageSpecificScripts; ?>

</body>
</html>


