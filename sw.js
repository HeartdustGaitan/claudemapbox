// Evento install: se activa cuando se instala el Service Worker
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open('my-cache').then(cache => {
            return cache.addAll([
                '/',
                '/styles.css?v=3',
                '/mapbox-gl.css',
                '/mapbox-gl.js',
                '/index.php',
                '/celulas.php',
                '/update_location.php'
                // Agrega más recursos estáticos según sea necesario
            ]);
        })
    );
});
self.addEventListener('sync', function(event) {
    if (event.tag === 'syncLocation') {
        event.waitUntil(updateLocationInBackground());
    }
});

async function updateLocationInBackground() {
    return new Promise((resolve, reject) => {
        // Verificar que la geolocalización esté disponible en el Service Worker
        if (!self.navigator || !self.navigator.geolocation) {
            console.error('Geolocalización no disponible en Service Worker');
            return reject();
        }

        // Obtener la ubicación actual y enviarla al servidor
        navigator.geolocation.getCurrentPosition(async (position) => {
            const { latitude, longitude } = position.coords;

            try {
                const response = await fetch('/update_location.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `Latitud=${latitude}&Longitud=${longitude}&user_id=${user_id}`
                });

                if (!response.ok) throw new Error('Error al actualizar la ubicación en el servidor');

                console.log('Ubicación actualizada exitosamente en segundo plano');
                resolve();
            } catch (error) {
                console.error('Error al enviar la ubicación:', error);
                reject();
            }
        });
    });
}

