<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciudadano Seguro - Inicio</title>
    
    <!-- Meta tags para PWA -->
    <meta name="description" content="Aplicación para monitoreo de incidencias en tiempo real">
    <meta name="theme-color" content="#1a759f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Ciudadano Seguro">
    
    <!-- Iconos para móviles -->
    <link rel="apple-touch-icon" href="./img/192x192.png">
    <link rel="icon" href="./icons/ico.ico" type="image/x-icon">
    
    <!-- Manifest para PWA -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Estilos modernos inline (no se necesita archivo CSS externo) -->
    <style>
        :root {
            --primary-color: #1a759f;
            --primary-dark: #00668c;
            --accent-color: #4cc9f0;
            --text-color: #333;
            --background-light: #f8f9fa;
            --background-dark: #f0f2f5;
            --success-color: #28a745;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --radius: 10px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--background-light) 0%, var(--background-dark) 100%);
            color: var(--text-color);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .app-container {
            width: 100%;
            max-width: 420px;
            min-height: 500px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            padding: 0;
            position: relative;
        }
        
        .app-header {
            background: var(--primary-color);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .logo-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: var(--shadow);
        }
        
        .logo {
            width: 90px;
            height: 90px;
        }
        
        .app-title {
            font-size: 28px;
            margin: 0;
            font-weight: 600;
        }
        
        .app-subtitle {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--text-color);
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #f9f9f9;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 117, 159, 0.2);
            outline: none;
            background-color: white;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            text-align: center;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .footer {
            text-align: center;
            font-size: 14px;
            color: #666;
            margin-top: 20px;
        }
        
        .install-btn {
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            box-shadow: var(--shadow);
            animation: pulse 2s infinite;
            display: none;
        }
        
        .install-btn:hover {
            background-color: var(--primary-dark);
            animation: none;
        }
        
        @keyframes pulse {
            0% {
                transform: translateX(-50%) scale(1);
            }
            50% {
                transform: translateX(-50%) scale(1.05);
            }
            100% {
                transform: translateX(-50%) scale(1);
            }
        }
        
        /* Responsive ajustes */
        @media (max-width: 480px) {
            .app-container {
                border-radius: 0;
                height: 100vh;
                max-width: 100%;
                box-shadow: none;
            }
            
            .app-header {
                padding: 20px;
            }
            
            .logo-container {
                width: 100px;
                height: 100px;
            }
            
            .logo {
                width: 80px;
                height: 80px;
            }
            
            .app-title {
                font-size: 24px;
            }
            
            .form-container {
                padding: 20px;
            }
        }
        
        /* Dispositivos muy pequeños */
        @media (max-height: 600px) {
            .logo-container {
                width: 80px;
                height: 80px;
                margin-bottom: 10px;
            }
            
            .logo {
                width: 60px;
                height: 60px;
            }
            
            .app-title {
                font-size: 20px;
            }
            
            .app-subtitle {
                font-size: 14px;
            }
            
            .form-container {
                padding: 15px;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .form-control {
                padding: 12px;
            }
            
            .btn {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="app-header">
            <div class="logo-container">
                <!-- Si tienes un logo, colócalo aquí -->
                <img src="./img/logo.png" alt="Logo" class="logo" onerror="this.src='https://via.placeholder.com/90x90?text=CS';this.onerror='';">
            </div>
            <h1 class="app-title">Ciudadano Seguro</h1>
            <p class="app-subtitle">Monitoreo de Incidencias en Tiempo Real</p>
        </div>
        
        <div class="form-container">
            <form id="login-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="nombre">Nombre de Usuario</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingresa tu nombre" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="password" id="telefono" name="telefono" class="form-control" placeholder="Ingresa tu teléfono" required autocomplete="current-password">
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo de Usuario</label>
                    <select id="tipo" name="tipo" class="form-control" required>
                        <option value="celula">Célula</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
            
            <div class="footer">
                &copy; <?php echo date('Y'); ?> Ciudadano Seguro. Todos los derechos reservados.
            </div>
        </div>
    </div>
    
    <!-- Botón de instalación de PWA (aparecerá cuando sea posible instalar) -->
    <button id="install-btn" class="install-btn">Instalar Aplicación</button>
    
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
    const installButton = document.getElementById('install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevenir que Chrome muestre la mini-infobar automática
        e.preventDefault();
        // Guardar el evento para usarlo más tarde
        deferredPrompt = e;
        // Mostrar el botón de instalación
        installButton.style.display = 'block';
    });

    installButton.addEventListener('click', async () => {
        // Ocultar el botón ya que no lo necesitamos más
        installButton.style.display = 'none';
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
        installButton.style.display = 'none';
    });
    </script>
</body>
</html>
