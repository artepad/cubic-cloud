<?php

class UsuarioController
{
    public function index()
    {
        echo "Controlador Usuario, Acción index";
    }
    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (isset($_SESSION['usuario'])) {
            header("Location: " . base_url);
            exit();
        }
        
        // Incluir directamente la vista de login sin layouts
        require_once 'views/usuarios/login.php';
    }
}
