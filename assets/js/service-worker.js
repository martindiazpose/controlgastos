self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('cache-v1').then((cache) => {
            return cache.addAll([
                '/',
                '/index.html',
                '/controlgastos.html',
                '/login.html',
                '/style.css',
                '/scripts.js',
                '/carpeta.png',
                '/manifest.json',
                'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
                'https://cdn.jsdelivr.net/npm/chart.js',
                'https://code.jquery.com/jquery-3.5.1.min.js',
                'https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js',
                'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'
            ]).catch((error) => {
                console.error('Failed to cache resources:', error);
            });
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