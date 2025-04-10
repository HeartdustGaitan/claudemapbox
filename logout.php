<?php
session_start(); // Iniciar la sesión

// Verificar si existe una sesión activa
if (isset($_SESSION['user_id'])) {
    // Destruir todas las variables de sesión
    $_SESSION = array(); // Limpia las variables de sesión

    // Si se desea, destruir la sesión
    session_destroy();

    // Redirigir a la página de inicio o de inicio de sesión
    header("Location: index.php"); // Cambia esto a la página que desees
    exit();
} else {
    // Si no hay sesión activa, redirigir a la página de inicio de sesión
    header("Location: index.php");
    exit();
}
?>
