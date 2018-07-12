<?php
/**
 * Created by PhpStorm.
 * User: cyt
 * Date: 2017/12/22
 * Time: 14:14
 */
namespace App\Http\Middleware;
use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin' , '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');
        return $response;
    }
}