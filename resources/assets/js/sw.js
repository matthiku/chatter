
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
