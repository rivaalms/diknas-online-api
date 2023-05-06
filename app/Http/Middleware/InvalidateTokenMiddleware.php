<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvalidateTokenMiddleware
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */

   /**
    * The duration of inactivity after which token will be invalidated.
    */
   const INACTIVITY_DURATION = 10; //in seconds

   public function handle(Request $request, Closure $next)
   {
      $user = Auth::user();

      if ($user && $user->api_token) {
         $lastActivityTime = strtotime($user->last_activity_time);

         if ($lastActivityTime && time() - $lastActivityTime > self::INACTIVITY_DURATION) {
            $user->api_token = null;
            $user->save();
         }
      }

      return $next($request);
   }
}

/* NOTE - This method algorithm is to check when the last time user create request from the app to the API. 
*         This algorithm requires API to always update the DB to mark when the last time users made a request,
*         which is inefficient for large scale. So this middleware is UNUSED, but kept for documentary and in case of needed.
 */