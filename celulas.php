// Esta sección debe reemplazar el código actual de geolocalización en celulas.php

// Remover este bloque:
/*
if ('serviceWorker' in navigator && 'SyncManager' in window) {
    navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('Service Worker registrado:', registration);

        // Intenta sincronizar la ubicación
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    registration.sync.register('syncLocation').then(() => {
                        console.log('Sincronización de ubicación registrada.');
                    });
                },
                (error) => console.error('Error al obtener la ubicación:', error),
                {
                    enableHighAccuracy: true,
                    maximumAge: 0,
                    timeout: 10000,
                }
            );
        }
    }).catch(function(error) {
        console.log('Error al registrar el Service Worker:', error);
    });
}
*/

// Reemplazar con este código mejorado:
var marker;
var user_id = <?php echo $_SESSION['user_id']; ?>;

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