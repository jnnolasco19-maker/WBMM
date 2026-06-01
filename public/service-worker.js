const CACHE_NAME = 'wbmm-cache-v2';

// Install Service Worker without pre-caching pages
self.addEventListener('install', event => {
  self.skipWaiting();
});

// Clean up old caches on activation to instantly clear any stuck "offline" copies
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          return caches.delete(cache);
        })
      );
    }).then(() => clients.claim())
  );
});

// Dummy fetch handler to satisfy Chrome's PWA installation requirements.
// We do not intercept any requests, allowing Chrome's main browser thread
// to solve InfinityFree's security challenge and load 100% online in real-time.
self.addEventListener('fetch', event => {
  // Let the browser handle all network requests normally
});
