<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ciudadano Seguro - Inicio de Sesión</title>
    
    <!-- PWA meta tags -->
    <meta name="description" content="Aplicación para monitoreo de incidencias en tiempo real">
    <meta name="theme-color" content="#1a759f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ciudadano Seguro">
    
    <!-- CSS -->
    <link rel="stylesheet" href="./styles.css?v=4">
    
    <!-- Favicon y íconos para móviles -->
    <link rel="shortcut icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="./img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="./img/192x192.png">
    
    <!-- PWA manifest -->
    <link rel="manifest" href="./manifest.json">
    
    <!-- Splash screen para iOS -->
    <link rel="apple-touch-startup-image" href="./img/splash.png">
    
    <style>
        /* Estilos adicionales específicos para esta página */
        .login-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            display: block;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin: 10px 0;
            text-align: center;
        }
        
        .version-info {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <?php 
            // Mostrar mensaje de error si existe
            if (isset($_GET['error'])) {
                echo '<div class="error-message">Nombre o teléfono incorrectos</div>';
            }
            ?>
            
            <img src="./img/logo.png" alt="Ciudadano Seguro" class="login-logo">
            
            <form id="login-form" method="POST" action="login.php">
                <h2>Iniciar Sesión</h2>
                <input type="text" name="nombre" placeholder="Nombre" required autocomplete="username">
                <input type="password" name="telefono" placeholder="Teléfono" required autocomplete="current-password">
                <select name="tipo" required>
                    <option value="celula">Célula</option>
                </select>
                <button type="submit">Iniciar Sesión</button>
            </form>
            
            <p class="version-info">Versión 1.0.1</p>
        </div>
    </div>
    
    <script>
    // Registrar el Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./sw.js')
                .then(registration => {
                    console.log('Service Worker registrado con éxito:', registration.scope);
                })
                .catch(error => {
                    console.error('Error al registrar el Service Worker:', error);
                });
        });
    }
    
    // Guardar credenciales para el modo offline
    document.getElementById('login-form').addEventListener('submit', function(event) {
        if ('localStorage' in window) {
            const nombre = this.querySelector('input[name="nombre"]').value;
            // Almacenamos solo el nombre para referencia (no contraseñas)
            localStorage.setItem('lastUsername', nombre);
        }
    });
    </script>
</body>
</html>