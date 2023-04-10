<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
// use Tymon\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Auth;

class JwtDriverMiddleware extends BaseMiddleware
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
     
        try {
            $user = Auth::guard('driver')->authenticate();

           
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status'=> 401,'status' => false,'message' => 'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status'=> 401,'status' => false,'message' => 'Token is Expired']);
            }else{
                return response()->json(['code'=> 401,'status' => false,'message' => 'Authorization Token not found']);
            }
        }
        $request->merge(['driver_id' => $user->driver_id]);
        $request->merge(['created_source' => "driver"]);
        $request->merge(['created_by' => $user->driver_id]);
        $request->merge(['updated_source' => "driver"]);
        return $next($request);
    }
}
