<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->bearerToken();
        if (!$accessToken) {
            throw new \Exception('Unauthorized');
        }
        $decodedToken = json_decode(base64_decode(explode('.', $accessToken)[1]), true);
        try {
            $user = User::where('user_id_keycloak', $decodedToken['sub'])->first();
            if ($user) {
                Auth::login($user);
                return $next($request);
            }
        } catch (\Exception $th) {
            throw new \Exception('Unauthorized');
        }
    }
}
