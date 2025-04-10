// Nombre de la caché
const CACHE_NAME = 'ciudadano-seguro-v1';

// Lista de recursos a cachear
const urlsToCache = [
  './',
  './styles.css',
  './index.php',
  './celulas.php',
  './offline.html',
  './img/192x192.png',
  './img/512x512.png',
  'https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js',
  'https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css',
  'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.js',
  'https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.2.0/mapbox-gl-draw.css'
];

// Evento install: se activa cuando se instala el Service Worker
self.addEventListener('install', event => {
  // Skipea la fase de espera y activa inmediatamente el service worker
  self.skipWaiting();
  
  console.log('Service Worker instalado');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cacheo de recursos iniciado');
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Error durante el cacheo de recursos:', error);
      })
  );
});

// Evento activate: se activa cuando el SW se activa
self.addEventListener('activate', event => {
  console.log('Service Worker activado');
  
  // Elimina las cachés antiguas
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Eliminando caché antigua:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  
  // Toma control inmediatamente de todas las páginas dentro del alcance
  return self.clients.claim();
});

// Evento fetch: intercepta las peticiones de red
self.addEventListener('fetch', event => {
  // Estrategia: Network First con fallback a caché y finalmente a offline.html
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Si la respuesta es válida, clonamos y la almacenamos en la caché
        if (response && response.status === 200) {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Si falla la red, intentamos recuperar de la caché
        return caches.match(event.request)
          .then(cachedResponse => {
            // Si hay respuesta en caché, la devolvemos
            if (cachedResponse) {
              return cachedResponse;
            }
            
            // Comprobamos si es una solicitud de página (HTML)
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('./offline.html');
            }
            
            // Si no es HTML, devolvemos una respuesta vacía
            return new Response('', {
              status: 404,
              statusText: 'Not found'
            });
          });
      })
  );
});

// Evento sync: para sincronización en segundo plano
self.addEventListener('sync', event => {
  console.log('Evento de sincronización recibido:', event.tag);
  
  if (event.tag === 'syncLocation') {
    event.waitUntil(
      // Recuperamos los datos de ubicación pendientes de sincronización
      // (esto debería implementarse con IndexedDB en una aplicación real)
      syncLocationData()
        .then(() => console.log('Sincronización de ubicación completada'))
        .catch(error => console.error('Error en sincronización:', error))
    );
  }
});

// Función para sincronizar datos de ubicación
async function syncLocationData() {
  // En una implementación real, aquí obtendrías los datos almacenados
  // en IndexedDB y los enviarías al servidor
  
  console.log('Intentando sincronizar datos de ubicación...');
  
  // Si tienes acceso a geolocalización en el SW (navegadores recientes)
  if ('geolocation' in self) {
    return new Promise((resolve, reject) => {
      self.geolocation.getCurrentPosition(async position => {
        try {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          
          // Obtenemos el user_id del almacenamiento local
          // (en una implementación real, esto debería venir de IndexedDB)
          const clientId = await getClientId();
          
          if (!clientId) {
            console.warn('No se encontró un ID de usuario para sincronizar');
            return resolve();
          }
          
          const response = await fetch('./update_location.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `Latitud=${lat}&Longitud=${lng}&user_id=${clientId}`
          });
          
          if (!response.ok) {
            throw new Error(`Error al sincronizar: ${response.status}`);
          }
          
          console.log('Ubicación sincronizada con éxito');
          resolve();
        } catch (error) {
          console.error('Error durante la sincronización:', error);
          reject(error);
        }
      }, error => {
        console.error('Error al obtener geolocalización:', error);
        reject(error);
      });
    });
  }
  
  console.warn('Geolocalización no disponible en el Service Worker');
  return Promise.resolve(); // Resolvemos la promesa para no bloquear
}

// Función auxiliar para obtener el ID del cliente activo
async function getClientId() {
  const clients = await self.clients.matchAll();
  if (clients && clients.length > 0) {
    // Aquí deberías implementar una forma de recuperar el user_id
    // de la sesión o del localStorage
    return null; // Por ahora devolvemos null
  }
  return null;
}