importScripts("precache-manifest.98676f90810c92abe2f2c23362d9e80a.js", "https://storage.googleapis.com/workbox-cdn/releases/3.2.0/workbox-sw.js");


/**
 * CACHING static files
 *
 * The service worker file should reference the self.__precacheManifest variable 
 * to obtain a list of ManifestEntrys obtained as part of the compilation
 */
workbox.precaching.precacheAndRoute(self.__precacheManifest || [])

/**
 * dynamic caching
 */
// workbox.routing.registerRoute('end point url', workbox.strategies.networkFirst({
//   cacheName: 'cache-name'
// }));

