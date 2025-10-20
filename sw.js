const CACHE_NAME = 'hiit-trainer-v1.1.0';
const urlsToCache = [
  '/training-app/',
  '/training-app/index.html',
  '/training-app/manifest.json',
  '/training-app/favicon.ico',
  '/training-app/favicon-16.png',
  '/training-app/favicon-32.png',
  '/training-app/apple-touch-icon.png',
  '/training-app/icon-192.png',
  '/training-app/icon-512.png',
  '/training-app/logo.svg',
  'https://cdn.tailwindcss.com',
  'https://cdn.jsdelivr.net/npm/chart.js'
];

// Install event - cache resources
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('Cache opened');
        return cache.addAll(urlsToCache);
      })
  );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request)
      .then((response) => {
        // Return cached version or fetch from network
        return response || fetch(event.request);
      }
    )
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Background sync for offline workout data
self.addEventListener('sync', (event) => {
  if (event.tag === 'background-sync-workout') {
    event.waitUntil(syncWorkoutData());
  }
});

// Push notifications
self.addEventListener('push', (event) => {
  let data = {
    title: 'HIIT Trainer',
    body: '¡Es hora de entrenar! 💪',
    icon: '/training-app/icon-192.png',
    badge: '/training-app/favicon-32.png',
    tag: 'workout-reminder'
  };
  
  // Si el push trae datos, usarlos
  if (event.data) {
    try {
      const pushData = event.data.json();
      data = { ...data, ...pushData };
    } catch (e) {
      data.body = event.data.text();
    }
  }
  
  const options = {
    body: data.body,
    icon: data.icon,
    badge: data.badge,
    vibrate: [200, 100, 200],
    tag: data.tag,
    requireInteraction: false,
    data: {
      dateOfArrival: Date.now(),
      url: data.url || '/training-app/?action=start'
    },
    actions: [
      {
        action: 'start',
        title: '🏃 Entrenar Ahora',
        icon: data.icon
      },
      {
        action: 'later',
        title: '⏰ Más Tarde',
        icon: data.badge
      }
    ]
  };
  
  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  
  const urlToOpen = event.notification.data.url || '/training-app/?action=start';
  
  if (event.action === 'start') {
    // Abrir la app y comenzar entrenamiento
    event.waitUntil(
      clients.matchAll({ type: 'window', includeUncontrolled: true })
        .then((clientList) => {
          // Si ya hay una ventana abierta, enfocarla
          for (let client of clientList) {
            if (client.url.includes('/training-app') && 'focus' in client) {
              return client.focus().then(() => {
                return client.navigate(urlToOpen);
              });
            }
          }
          // Si no hay ventana abierta, abrir una nueva
          if (clients.openWindow) {
            return clients.openWindow(urlToOpen);
          }
        })
    );
  } else if (event.action === 'later') {
    // Programar recordatorio en 2 horas
    console.log('Recordatorio pospuesto');
  } else {
    // Click en la notificación (no en botones)
    event.waitUntil(
      clients.openWindow(urlToOpen)
    );
  }
});

// Sync workout data function
async function syncWorkoutData() {
  // This would sync offline workout data to the server
  console.log('Syncing workout data...');
  // Implementation would depend on your backend API
}