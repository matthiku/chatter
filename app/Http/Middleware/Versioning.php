<?php

namespace App\Http\Middleware;

use Closure;

class Versioning
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $frontend_timestamp = filemtime(base_path().'/public/js/app.js');

        if ($frontend_timestamp) {
            $response->header('x-version-control', $frontend_timestamp);
            // $response->header('Cache-Control', 'no-cache, must-revalidate');
        }
        
        return $next($request);
    }
}
