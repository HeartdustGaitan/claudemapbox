<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


function saveEmergency($lat, $lng) {
    include 'db_connection.php';

    $nombre = "incidencia";
    $stmt = $conn->prepare("INSERT INTO incidencias (Descripcion, Latitud, Longitud) VALUES (?, ?, ?)");
    $stmt->bind_param("sdd", $nombre, $lat, $lng);
    
    if ($stmt->execute()) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Error al agregar la emergencia.";
    }

    $stmt->close();
    $conn->close();
}

// Detectar si la URL contiene coordenadas
if (isset($_SERVER['REQUEST_URI'])) {
    $urlPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathParts = explode('/', $urlPath);

    if (count($pathParts) > 2) {
        $coords = explode(',', end($pathParts));
        
        if (count($coords) == 2) {
            $lat = (float)$coords[0];
            $lng = (float)$coords[1];
            saveEmergency($lat, $lng);
        }
    }
}

// Función para obtener la última emergencia
function getLastEmergencyCoordinates() {
    include 'db_connection.php';

    $stmt = $conn->prepare("SELECT Latitud, Longitud FROM incidencias ORDER BY IdIncidencias DESC LIMIT 1");
    $stmt->execute();
    $stmt->bind_result($lat, $lng);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    return [$lat, $lng];
}

// Obtener coordenadas de la última emergencia
list($lastLat, $lastLng) = getLastEmergencyCoordinates();
$coordinates = [$lastLng ?? -104.894627351769, $lastLat ?? 21.504914173858914]; // Usar coordenadas por defecto si no hay emergencias
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard</title>
    
    <!-- Meta tags para PWA -->
    <meta name="description" content="Aplicación para monitoreo de incidencias en tiempo real">
    <meta name="theme-color" content="#1a759f">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Ciudadano Seguro">
    
    <!-- CSS -->
    <link rel="stylesheet" href="styles.css?v=3">
    
    <!-- Mapbox CSS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet" />
    <link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.css" rel="stylesheet" />
    
    <!-- Manifest para PWA -->
    <link rel="manifest" href="manifest.json">
    
    <!-- Scripts de Mapbox -->
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.js"></script>
    
    <style>
    /* Estilos generales */
    body {
        margin: 0;
        font-family: 'Arial', sans-serif;
        background-color: #eef2f3; /* Color de fondo suave */
        height: 100vh;
        width: 100vw;
        overflow: hidden;
    }

    #map {
        width: 100%;
        height: 100vh;
        position: absolute;
        top: 0;
        left: 0;
    }

    /* Menú horizontal */
    .menu {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #ffffff; /* Fondo blanco */
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex; /* Usar flexbox para el menú horizontal */
        gap: 10px; /* Espaciado entre botones */
    }

    .menu button {
        padding: 10px 15px;
        background: #71c4ef; /* Color verde */
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background 0.3s, transform 0.2s; /* Añadir efectos de transición */
    }

    .menu button:hover {
        background: #00668c; /* Color verde oscuro al pasar el mouse */
        transform: scale(1.05); /* Efecto de aumento en hover */
    }

    /* Contenedores */
    #emergency-form-container, #emergency-list-container, #assign-route-container {
        position: absolute;
        bottom: 20px;
        right: 10px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-width: 300px;
        display: none; /* Mantener oculto inicialmente */
    }

    /* Encabezados */
    #emergency-form-container h2, #emergency-list-container h2, #assign-route-container h2 {
        margin-top: 0;
        color: #343a40; /* Color de texto más oscuro */
    }

    /* Etiquetas y campos de entrada */
    #emergency-form-container label, #emergency-list-container label, #assign-route-container label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #495057; /* Color de texto */
    }

    #emergency-form-container input[type="text"], #assign-route-container select {
        width: 100%;
        padding: 10px;
        margin-bottom: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        transition: border-color 0.3s;
    }

    #emergency-form-container input[type="text"]:focus, #assign-route-container select:focus {
        border-color: #28a745; /* Cambio de color al enfocar */
        outline: none; /* Eliminar el contorno */
    }

    /* Botones de formulario */
    #emergency-form-container button, #assign-route-container button {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s, transform 0.2s; /* Añadir efectos de transición */
    }

    #emergency-form-container button:hover, #assign-route-container button:hover {
        background-color: #218838;
        transform: scale(1.05); /* Efecto de aumento en hover */
    }

    /* Tarjetas */
    .card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        margin: 5px 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s; /* Añadir efectos de transición */
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Sombra al pasar el mouse */
    }

    .card button {
        background: red;
        color: white;
        border: none;
        padding: 5px;
        cursor: pointer;
        border-radius: 5px;
        transition: background 0.3s; /* Añadir efectos de transición */
    }

    .card button:hover {
        background: darkred;
    }
    
    /* Indicador de conexión */
    #connection-status {
        position: absolute;
        bottom: 10px;
        left: 10px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        z-index: 1000;
        background-color: #28a745;
        color: white;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    #connection-status.offline {
        background-color: #dc3545;
    }
    
    #status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #fff;
        margin-right: 5px;
    }
    
    /* Ajustes específicos para móviles */
    @media (max-width: 768px) {
        .menu {
            padding: 8px;
            gap: 5px;
        }
        
        .menu button {
            padding: 8px 12px;
            font-size: 14px;
        }
    }
</style>
</head>
<body>
    <div id="map"></div>

    <div class="menu">
        <button id="togglePopups">Activar Etiquetas</button>
        <a href="logout.php" class="logout-button">
            <button>Cerrar Sesión</button>
        </a>
    </div>
    
    <!-- Indicador de estado de conexión -->
    <div id="connection-status">
        <span id="status-indicator"></span>
        <span id="status-text">Conectado</span>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            mapboxgl.accessToken = 'pk.eyJ1IjoiZ3VpZ281NjciLCJhIjoiY20xOXNqY2o0MTh3ZzJrb2l5amg3OWUwaiJ9.PrF_B7HbcB3B_95mKdsdFA';

            // Coordenadas de la última emergencia
            const coordinates = [<?php echo $coordinates[0]; ?>, <?php echo $coordinates[1]; ?>];

            // Crear el mapa
            var map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: coordinates, // Usar las coordenadas de la última emergencia
                zoom: 12
            });

            // Configurar Mapbox Draw
            var draw = new MapboxDraw({
                displayControlsDefault: false,
                controls: {
                    polygon: true,
                    trash: true
                }
            });

            // Añadir el control de dibujo al mapa
            map.addControl(draw);

            // Añadir controles de navegación al mapa
            map.addControl(new mapboxgl.NavigationControl());

            // Variables para la geolocalización
            var marker;
            var user_id = <?php echo $_SESSION['user_id']; ?>;
            
            // Monitorear el estado de la conexión
            function updateConnectionStatus() {
                const statusDiv = document.getElementById('connection-status');
                const statusText = document.getElementById('status-text');
                
                if (navigator.onLine) {
                    statusDiv.classList.remove('offline');
                    statusText.textContent = 'Conectado';
                } else {
                    statusDiv.classList.add('offline');
                    statusText.textContent = 'Sin conexión';
                }
            }
            
            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);
            updateConnectionStatus();

            function updateLocation(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                console.log('Enviando ubicación:', { lat, lng, user_id });

                // Guardar en localStorage para uso offline/sincronización
                if ('localStorage' in window) {
                    localStorage.setItem('lastLocation', JSON.stringify({
                        lat: lat,
                        lng: lng,
                        timestamp: new Date().getTime()
                    }));
                }

                // Enviar al servidor
                fetch('update_location.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'Latitud=' + lat + '&Longitud=' + lng + '&user_id=' + user_id
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.text();
                })
                .then(data => console.log('Respuesta del servidor:', data))
                .catch(error => {
                    console.error('Error al enviar ubicación:', error);
                    
                    // Programar sincronización cuando vuelva la conexión
                    if ('serviceWorker' in navigator && 'SyncManager' in window) {
                        navigator.serviceWorker.ready
                            .then(registration => {
                                registration.sync.register('syncLocation')
                                    .then(() => console.log('Sincronización programada'))
                                    .catch(err => console.error('Error al programar sincronización:', err));
                            });
                    }
                });

                // Actualizar marcador en el mapa
                if (!marker) {
                    marker = new mapboxgl.Marker()
                        .setLngLat([lng, lat])
                        .addTo(map);
                } else {
                    marker.setLngLat([lng, lat]);
                }

                // Centrar mapa en la ubicación actual
                map.setCenter([lng, lat]);
            }

            // Solicitar geolocalización de alta precisión
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(
                    updateLocation,
                    function(error) {
                        console.error("Error de geolocalización:", error.message);
                        
                        // Mostrar alerta al usuario
                        alert("No se pudo obtener tu ubicación. Por favor verifica los permisos de la aplicación.");
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 15000,
                        timeout: 20000
                    }
                );
            } else {
                console.error("Geolocalización no soportada en este dispositivo");
                alert("Tu dispositivo no soporta geolocalización, necesaria para el funcionamiento de la app.");
            }

            var markers = {};
            var emergencyMarkers = { emergency: {}, emergency1: {} }; // Separar marcadores de cada tipo de emergencia
            let popupsActive = false; // Estado para controlar la visibilidad de los popups

            // Función para alternar el estado de los popups
            function togglePopups() {
                popupsActive = !popupsActive;
                document.getElementById('togglePopups').textContent = popupsActive ? 'Desactivar Etiquetas' : 'Activar Etiquetas';

                // Mostrar/ocultar popups de emergencia tipo "emergency"
                Object.values(emergencyMarkers.emergency).forEach(({ popup }) => {
                    if (popupsActive) {
                        popup.addTo(map); // Mostrar el popup
                    } else {
                        popup.remove(); // Ocultar el popup
                    }
                });

                // Mostrar/ocultar popups de emergencia tipo "emergency1"
                Object.values(emergencyMarkers.emergency1).forEach(({ popup }) => {
                    if (popupsActive) {
                        popup.addTo(map); // Mostrar el popup
                    } else {
                        popup.remove(); // Ocultar el popup
                    }
                });

                // Mostrar/ocultar popups de otros tipos de markers (usuarios)
                Object.values(markers).forEach(marker => {
                    if (popupsActive) {
                        marker.getPopup().addTo(map);
                    } else {
                        marker.getPopup().remove();
                    }
                });
            }

            // Función para obtener y actualizar las ubicaciones
            function fetchLocations() {
                fetch('get_locations2.php')
                    .then(response => response.json())
                    .then(data => {
                        // Eliminar marcadores que no están en el array para cada tipo de emergencia
                        Object.keys(emergencyMarkers.emergency).forEach(id => {
                            if (!data.some(location => location.id === id && location.tipo === 'emergency')) {
                                emergencyMarkers.emergency[id].marker.remove();
                                delete emergencyMarkers.emergency[id];
                            }
                        });
                        
                        Object.keys(emergencyMarkers.emergency1).forEach(id => {
                            if (!data.some(location => location.id === id && location.tipo === 'emergency1')) {
                                emergencyMarkers.emergency1[id].marker.remove();
                                delete emergencyMarkers.emergency1[id];
                            }
                        });

                        // Eliminar marcadores de usuario que no están en los datos
                        Object.keys(markers).forEach(id => {
                            if (!data.some(location => location.usuario_id === id)) {
                                markers[id].remove(); // Eliminar el marcador del mapa
                                delete markers[id]; // Eliminar del objeto markers
                            }
                        });

                        // Procesar cada ubicación obtenida
                        data.forEach(location => {
                            if (location.tipo === 'emergency') {
                                if (!emergencyMarkers.emergency[location.id]) { 
                                    var marker = new mapboxgl.Marker({ color: 'red' })
                                        .setLngLat([location.lng, location.lat])
                                        .addTo(map);
                                    
                                    // Asignar zIndex alto para que esté encima de los usuarios
                                    marker.getElement().style.zIndex = 100;

                                    var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                                    var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);
                                    
                                    emergencyMarkers.emergency[location.id] = { marker, popup };
                                    marker.setPopup(popup);

                                    if (popupsActive) {
                                        popup.addTo(map);
                                    }
                                }
                            } else if (location.tipo === 'emergency1') {
                                if (!emergencyMarkers.emergency1[location.id]) { 
                                    var marker = new mapboxgl.Marker({ color: 'orange' })
                                        .setLngLat([location.lng, location.lat])
                                        .addTo(map);
                                    
                                    // Asignar zIndex alto para que esté encima de los usuarios
                                    marker.getElement().style.zIndex = 100;

                                    var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                                    var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);
                                    
                                    emergencyMarkers.emergency1[location.id] = { marker, popup };
                                    marker.setPopup(popup);

                                    if (popupsActive) {
                                        popup.addTo(map);
                                    }
                                }
                            } else {
                                if (markers[location.usuario_id]) {
                                    markers[location.usuario_id].setLngLat([location.lng, location.lat]);
                                } else {
                                    var marker = new mapboxgl.Marker()
                                        .setLngLat([location.lng, location.lat])
                                        .addTo(map);

                                    // Asignar zIndex bajo para que esté debajo de los marcadores de emergencia
                                    marker.getElement().style.zIndex = 1;

                                    var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                                    var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);
                                    
                                    markers[location.usuario_id] = marker;
                                    marker.setPopup(popup);

                                    if (popupsActive) {
                                        popup.addTo(map);
                                    }
                                }
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error al obtener ubicaciones:', error);
                        // No mostrar alertas aquí para no molestar al usuario en caso de pérdida momentánea de conexión
                    });
            }

            // Asignar el evento al botón
            document.getElementById('togglePopups').addEventListener('click', togglePopups);

            // Llamar a fetchLocations cada segundo con manejo de errores mejorado
            const locationInterval = setInterval(function() {
                if (navigator.onLine) {
                    fetchLocations();
                }
            }, 1000);
            
            // Limpiar intervalo si el usuario abandona la página
            window.addEventListener('beforeunload', function() {
                clearInterval(locationInterval);
            });
        });
        
        // Registrar el Service Worker (si no está ya registrado)
        if ('serviceWorker' in navigator && !navigator.serviceWorker.controller) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('./sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado:', registration.scope);
                    })
                    .catch(error => {
                        console.error('Error al registrar el Service Worker:', error);
                    });
            });
        }
    </script>
</body>
</html>
