const CACHE_NAME = 'wbmm-cache-v1';
const urlsToCache = [
  './login',
  './assets/css/custom.css',
  './assets/js/wbmm.js',
  'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
  'https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&display=swap'
];

// Install Service Worker and cache core static assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Static assets pre-cached');
        return cache.addAll(urlsToCache);
      })
  );
});

// Network First Caching Strategy (Crucial for dynamic PHP/MySQL applications)
// 1. Try to fetch the fresh page from the internet first (so databases and login work).
// 2. If the user is offline (e.g., inside the market), fall back to cached files.
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request);
      })
  );
});
