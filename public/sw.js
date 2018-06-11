importScripts("precache-manifest.98676f90810c92abe2f2c23362d9e80a.js", "https://storage.googleapis.com/workbox-cdn/releases/3.2.0/workbox-sw.js");

/*global workbox*/
/*eslint no-undef: "error"*/

/**
 * CACHING static files
 *
 * The service worker file should reference the self.__precacheManifest variable 
 * to obtain a list of ManifestEntrys obtained as part of the compilation
 */
workbox.precaching.precacheAndRoute(self.__precacheManifest || [])


// fonts etc should be cached and fetched from cache first, then validated from the network
workbox.routing.registerRoute(
  /.*(?:fonts\.googleapis|gstatic)\.com.*$/,
  workbox.strategies.staleWhileRevalidate({
    cacheName: 'google-fonts'
  })
)
// our own static assets
workbox.routing.registerRoute(
  new RegExp('/static/'),
  workbox.strategies.cacheFirst({
    cacheName: 'static-files'
  })
)
// third-party images (avatars)
// see https://developers.google.com/web/tools/workbox/modules/workbox-cacheable-response
workbox.routing.registerRoute(
  new RegExp('.*(?:.jpg|.png)'),
  workbox.strategies.cacheFirst({
    cacheName: 'image-cache',
    plugins: [
      new workbox.cacheableResponse.Plugin({
        statuses: [0, 200],
      }),
      new workbox.expiration.Plugin({
        maxEntries: 20,
      }),
    ]
  })
)


/**
 * dynamic caching
 */
workbox.routing.registerRoute(
  '/home',
  workbox.strategies.networkFirst({
    cacheName: 'semi-static'
  })
)


/**
 * API GET requests cached with networkFirst
 */
workbox.routing.registerRoute(
  new RegExp('/api/*'),
  workbox.strategies.networkFirst({
    cacheName: 'api-get-requests'
  })
)
