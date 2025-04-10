<?php
session_start();

// Detectar si la URL contiene el parámetro idemergencia
if (isset($_GET['IdIncidencias'])) {
    $incidenciaId = (int)$_GET['IdIncidencias']; // Convertir el ID a entero para mayor seguridad

    // Guardar el ID en la sesión
    $_SESSION['incidenciaId'] = $incidenciaId;

    // Imprimir el ID en la consola del navegador
    echo "<script>console.log('Incidencia ID: ' + $incidenciaId);</script>";
}

// Coordenadas por defecto para el mapa
$coordinates = [-104.8943342313912, 21.50461745218106];

// Aquí podrías incluir tu lógica de consulta o continuar con el resto de tu código
?>






<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css?v=3">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.js"></script>
    <link href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.css" rel="stylesheet" />
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css" rel="stylesheet" />
    <style>
    /* Estilos generales */
    body {
        margin: 0;
        font-family: 'Arial', sans-serif;
        background-color: #eef2f3; /* Color de fondo suave */
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
</style>

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
                zoom: 12,
                attributionControl: false
            });
            // Control de Pantalla Completa
map.addControl(new mapboxgl.FullscreenControl(), 'top-right');

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


                var marker;

                function updateLocation(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    fetch('update_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'lat=' + lat + '&lng=' + lng
                    });

                    if (!marker) {
                        marker = new mapboxgl.Marker()
                            .setLngLat([lng, lat])
                            .addTo(map);
                    } else {
                        marker.setLngLat([lng, lat]);
                    }

                    map.setCenter([lng, lat]);
                }

                navigator.geolocation.watchPosition(updateLocation, function(error) {
                    console.error("Error de geolocalización: " + error.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });

            // Function to save the drawn polygon
            /*
            document.getElementById('save-polygon').addEventListener('click', function() {
                var data = draw.getAll();
                if (data.features.length > 0) {
                    var coordinates = JSON.stringify(data.features[0].geometry.coordinates);
                    fetch('save_polygon.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'user_id=' + user_id + '&coordinates=' + encodeURIComponent(coordinates)
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Polígono guardado con éxito!');
                        } else {
                            alert('Error al guardar el polígono.');
                        }
                    });
                } else {
                    alert('Dibuja un polígono antes de guardarlo.');
                }
            });
*/
var markers = {};
var emergencyMarkers = {};
let popupsActive = false; // Estado para controlar la visibilidad de los popups

// Función para alternar el estado de los popups
function togglePopups() {
    popupsActive = !popupsActive;
    document.getElementById('togglePopups').textContent = popupsActive ? 'Desactivar Etiquetas' : 'Activar Etiquetas';

    // Alternar visibilidad de los popups en emergencyMarkers
    Object.values(emergencyMarkers).forEach(({ marker, popup }) => {
        if (popupsActive) {
            popup.addTo(map); // Muestra el popup
        } else {
            popup.remove(); // Oculta el popup
        }
    });

    // Alternar visibilidad de los popups en markers
    Object.values(markers).forEach(marker => {
        if (popupsActive) {
            marker.getPopup().addTo(map);
        } else {
            marker.getPopup().remove();
        }
    });
}

let lastEmergencyId = null;

function fetchLocations() {
    fetch('get_locations.php')
        .then(response => response.json())
        .then(data => {
            // Remove markers not present in the new data
            Object.keys(emergencyMarkers).forEach(id => {
                if (!data.some(location => location.id === parseInt(id))) {
                    emergencyMarkers[id].marker.remove();
                    delete emergencyMarkers[id];
                }
            });

            Object.keys(markers).forEach(usuario_id => {
                if (!data.some(location => location.usuario_id === parseInt(usuario_id))) {
                    markers[usuario_id].remove();
                    delete markers[usuario_id];
                }
            });

            lastEmergencyId = null; // Reset last emergency id

            data.forEach(location => {
                if (location.tipo === 'emergency2') {
                    var marker = new mapboxgl.Marker({ color: 'purple' })
                        .setLngLat([location.lng, location.lat])
                        .addTo(map);

                    var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                    var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);

                    // Store the marker and popup together
                    emergencyMarkers[location.id] = { marker, popup };

                    // Attach the popup to the marker
                    marker.setPopup(popup);

                    // Mostrar/ocultar el popup según el estado
                    if (popupsActive) {
                        popup.addTo(map); // Muestra el popup si está activo
                    }

                    // Set lastEmergencyId to the current emergency location ID
                    lastEmergencyId = location.id;
                } else {
                    if (markers[location.usuario_id]) {
                        markers[location.usuario_id].setLngLat([location.lng, location.lat]);
                    } else {
                        var marker = new mapboxgl.Marker()
                            .setLngLat([location.lng, location.lat])
                            .addTo(map);

                        var popupContent = `<strong>${location.nombre}</strong><br>Teléfono: ${location.telefono || 'No disponible'}`;
                        var popup = new mapboxgl.Popup({ offset: 25 }).setHTML(popupContent);

                        // Asignar el popup al marcador
                        markers[location.usuario_id] = marker;
                        marker.setPopup(popup);

                        // Mostrar/ocultar el popup según el estado
                        if (popupsActive) {
                            popup.addTo(map);
                        }

                        // Agregar evento de clic al marcador
                        marker.getElement().addEventListener('click', function() {
                            navigator.clipboard.writeText(location.telefono)
                                .then(() => {
                                    alert('Número copiado al portapapeles: ' + lastEmergencyId);

                                    if (lastEmergencyId) {
                                        fetch('get_celula.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                IdAgente: location.usuario_id,
                                                IdIncidencia: lastEmergencyId
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(result => {
                                            if (result.success) {
                                                console.log('Relación guardada correctamente.');
                                            } else {
                                                console.error('Error al guardar la relación:', result.error);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error en la solicitud:', error);
                                        });
                                    } else {
                                        console.warn('No hay emergencias recientes registradas.');
                                    }
                                })
                                .catch(err => {
                                    console.error('Error al copiar el número: ', err);
                                });
                        });
                    }
                }
            });
        });
}

// Asignar el evento al botón
document.getElementById('togglePopups').addEventListener('click', togglePopups);

// Llamar a fetchLocations cada segundo
setInterval(fetchLocations, 1000);


        });
    </script>
    

</head>
<body>
    <div id="map"></div>

    <div class="menu">
       
            <button id="togglePopups">Activar Etiquetas</button>
            <button id="save-polygon">Guardar Polígono</button>
    </div>


</body>
</html>
