<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
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
            return response(['error' => 'Unauthorized']);
        }
        if ($this->isTokenExpired($accessToken)) {
            return response()->json(['error' => 'Access token expired'], 401);
        }
        $decodedToken = json_decode(base64_decode(explode('.', $accessToken)[1]), true);
        try {
            $user = User::where('user_id_keycloak', $decodedToken['sub'])->first();
            if ($user) {
                Auth::login($user);
                return $next($request);
            }
            return response(['error' => 'Unauthorized']);
        } catch (\Exception $th) {
            throw new \Exception('Unauthorized');
        }
    }

    private function isTokenExpired($accessToken)
    {
        $decodedToken = json_decode(base64_decode(explode('.', $accessToken)[1]), true);
        if ($decodedToken['exp'] != "") {
            return Carbon::now()->gt(Carbon::createFromTimestamp($decodedToken['exp']));
        }
        return true;
    }
}
