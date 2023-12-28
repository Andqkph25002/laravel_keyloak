<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $dataLogin  = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'grant_type' => 'client_credentials',
        ])->post('https://sso.toprate.io/realms/PHP-intern/protocol/openid-connect/token')->json();
       dd($dataLogin);

       
        return view('home');
    }
}
