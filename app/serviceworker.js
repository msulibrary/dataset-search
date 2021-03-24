const version = "0.1.10";
const cacheName = `dataset-search-${version}`;
self.addEventListener('install', e => {
  e.waitUntil(
    caches.open(cacheName).then(cache => cache.addAll([
      `./`,
      `./about.php`,
      `./browse.php`,
      `./dashboard.php`,
      `./index.php`,
      `./search.php`,
      `./meta/styles/global.css`,
      `./meta/scripts/global.js`,
      `./meta/scripts/d3.v5.min.js`,
      `./meta/img/MSU-horiz-reverse-web-header.svg`,
      `./meta/img/lock-24px.svg`
    ])
        .then(() => self.skipWaiting()))
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.open(cacheName)
      .then(cache => cache.match(event.request, {ignoreSearch: true}))
      .then(response => response || fetch(event.request))
  );
});
