
  
  const CACHE_NAME = 'pwa-cache-v1'; 
const cacheUrls = [
      '/',
      '/index.html',
      '/about.html',
      '/contact.html',
      '/project.html',
      '/service.html',
      '/team.html',
      '/testimonial.html',
      '/css/style.css',
      '/css/bootstrap.min.css',
      '/app.js',
      '/logo-Sagosoft.png',
   
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(cacheUrls);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => name !== CACHE_NAME)
                    .map((name) => caches.delete(name))
            );
        })
    );
});