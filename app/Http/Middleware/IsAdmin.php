<?php


namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class IsAdmin
{
    public function __construct()
    {
        $this->key = "ZjonskaSiVo9mL1z6qZIM";
    }
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        // if
        if(!$token){
            return response()->json(['errors' => ['message' => ['Không có token']]], 401);
        }
        $token = str_replace('Bearer ', '', $token);
        $decoded = JWT::decode($token, $this->key, array('HS256'));
        if($decoded->user->role == "Admin"){
            return $next($request);
        }
        return response()->json(['errors' => ['message' => ['Không có quyền truy cập']]], 401);
    }
}
