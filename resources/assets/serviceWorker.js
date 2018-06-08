var CACHE_NAME = 'my-site-cache-v1'
var urlsToCache = [
  '/',
  '/css/app.css',
  '/js/manifest.js',
  '/js/app.js',
  '/js/vendor.js',
]

self.addEventListener('install', function (event) {
  // Perform install steps
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function (cache) {
        console.log('Opened cache')
        return cache.addAll(urlsToCache)
      })
  )
})

// {
//   // Exclude images from the precache
//   exclude: [/\.(?:png|jpg|jpeg|svg)$/],

//   // Define runtime caching rules.
//   runtimeCaching: [{
//     // Match any request ends with .png, .jpg, .jpeg or .svg.
//     urlPattern: /\.(?:png|jpg|jpeg|svg)$/,

//     // Apply a cache-first strategy.
//     handler: 'cacheFirst',

//     options: {
//       // Only cache 10 images.
//       expiration: {
//         maxEntries: 10,
//       },
//       cacheName: 'chatter-runtime-cache'
//     },
//   }, ],
// }