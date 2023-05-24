<?php

namespace App\Http\Middleware;

use Closure;

class SupervisorMiddleware
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
      if ($request->header('user-type') != 2) {
         return response('Unauthorized', 401);
      }
      return $next($request);
   }
}
