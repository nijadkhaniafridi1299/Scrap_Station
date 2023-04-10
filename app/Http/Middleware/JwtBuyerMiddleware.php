<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
// use Tymon\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Auth;

class JwtBuyerMiddleware extends BaseMiddleware
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
            $user = Auth::guard('buyer')->authenticate();

           
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['status'=> 401,'status' => false,'message' => 'Token is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(['status'=> 401,'status' => false,'message' => 'Token is Expired']);
            }else{
                return response()->json(['code'=> 401,'status' => false,'message' => 'Authorization Token not found']);
            }
        }
        $request->merge(['buyer_id' => $user->buyer_id]);
        $request->merge(['created_source' => "buyer"]);
        $request->merge(['created_by' => $user->buyer_id]);
        $request->merge(['updated_source' => "buyer"]);
        return $next($request);
    }
}
