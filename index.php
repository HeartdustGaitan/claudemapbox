<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    
    <!-- Meta tags para PWA -->
    <meta name="description" content="Aplicación para monitoreo de incidencias en tiempo real">
    <meta name="theme-color" content="#1a759f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Ciudadano Seguro">
    
    <!-- CSS -->
    <link rel="stylesheet" href="styles.css">
    
    <!-- Iconos para móviles -->
    <link rel="apple-touch-icon" href="./img/192x192.png">
    <link rel="icon" href="./icons/ico.ico" type="image/x-icon">
    
    <!-- Manifest para PWA -->
    <link rel="manifest" href="manifest.json">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form id="login-form" method="POST" action="login.php">
                <h2>Iniciar Sesión</h2>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="password" name="telefono" placeholder="telefono" required>
                <select name="tipo" required>
                    <option value="celula">Celula</option>
                </select>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
    
    <script>
    // Registrar el Service Worker para PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./sw.js')
                .then((registration) => {
                    console.log('Service Worker registrado con éxito:', registration.scope);
                })
                .catch((error) => {
                    console.error('Error al registrar el Service Worker:', error);
                });
        });
    }
    
    // Código para el botón de instalación de la PWA
    let deferredPrompt;
    const installContainer = document.createElement('div');
    const installButton = document.createElement('button');
    
    // Estilos para el contenedor y botón
    installContainer.style.position = 'fixed';
    installContainer.style.bottom = '20px';
    installContainer.style.left = '50%';
    installContainer.style.transform = 'translateX(-50%)';
    installContainer.style.zIndex = '9999';
    installContainer.style.display = 'none';
    
    installButton.textContent = 'Instalar aplicación';
    installButton.style.padding = '10px 15px';
    installButton.style.backgroundColor = '#007bff';
    installButton.style.color = 'white';
    installButton.style.border = 'none';
    installButton.style.borderRadius = '5px';
    installButton.style.fontWeight = 'bold';
    
    // Añadir el botón al contenedor
    installContainer.appendChild(installButton);
    // Añadir el contenedor al body
    document.body.appendChild(installContainer);

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevenir que Chrome muestre la mini-infobar automática
        e.preventDefault();
        // Guardar el evento para usarlo más tarde
        deferredPrompt = e;
        // Mostrar el botón de instalación
        installContainer.style.display = 'block';
    });

    installButton.addEventListener('click', async () => {
        // Ocultar el botón ya que no lo necesitamos más
        installContainer.style.display = 'none';
        // Mostrar el prompt de instalación
        deferredPrompt.prompt();
        // Esperar a que el usuario responda
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`Usuario eligió: ${outcome}`);
        // Limpiar la variable
        deferredPrompt = null;
    });

    window.addEventListener('appinstalled', () => {
        console.log('PWA instalada con éxito');
        alert('¡Aplicación instalada correctamente!');
        installContainer.style.display = 'none';
    });
    </script>
</body>
</html>
