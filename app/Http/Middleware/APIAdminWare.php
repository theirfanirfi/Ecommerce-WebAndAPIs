<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
class APIAdminWare
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
        $user = new User();
        $token = $request->input('token');
        if(!empty($token)){
            $checkUser = $user->verifyToken($token);
            if($checkUser->count() > 0 && $checkUser->first()->role == 1){
                return $next($request);
            }else {
                return response()->json([
                    'error' => true,
                    'isAuthenticated' => false,
                    'unAuthorized' => true,
                ]);
            }
        }else {
            return response()->json([
                'error' => true,
                'isAuthenticated' => false,
            ]);
        }
    }
}
