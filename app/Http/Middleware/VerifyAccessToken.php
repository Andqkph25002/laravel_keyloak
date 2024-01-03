<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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


        // Lấy Access Token từ header Authorization
        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $decodedToken = json_decode(base64_decode(explode('.', $accessToken)[1]), true);
        try {
            $users = User::all();
            foreach ($users as $user) {
                if ($user->user_id_keycloak === $decodedToken['sub']) {
                    return $next($request);
                }
            }
        } catch (\Exception $th) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
