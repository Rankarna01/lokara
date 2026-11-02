// Lokara POS & Backoffice - Progressive Web App Service Worker
const CACHE_NAME = 'lokara-pos-cache-v2';
const PRECACHE_ASSETS = [
    '/manifest.json',
    '/images/logo-app.png',
    '/images/logo-lokarapos.png',
    '/images/opengraph.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('Pre-cache error:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Network-first strategy with cache fallback for navigation and static assets
self.addEventListener('fetch', (event) => {
    // Only handle http/https GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    if (!event.request.url.startsWith('http://') && !event.request.url.startsWith('https://')) {
        return;
    }

    const url = new URL(event.request.url);
    // Skip Livewire endpoints & assets entirely from SW caching
    if (url.pathname.includes('/livewire') || url.pathname.includes('/livewire-') || url.pathname.includes('/js/livewire/')) {
        return;
    }

    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

