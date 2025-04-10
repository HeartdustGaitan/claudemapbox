<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="styles.css">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="apple-touch-icon" href="./icons/icon-192x192.png">
    <link rel="icon" href="icons/ico.ico" type="image/x-icon">
    <meta name="theme-color" content="#00bcd4">
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
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
            .then((registration) => {
                console.log('Service Worker registrado con éxito:', registration);
            })
            .catch((error) => {
                console.error('Error al registrar el Service Worker:', error);
            });
    }
</script>
</body>
</html>
