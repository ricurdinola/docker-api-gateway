<?php

namespace App\Http\Controllers;

use App\Services\GatewayService;
use Illuminate\Http\Request;

class GatewayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('apikey');
        $this->middleware('bearer');
    }

    public function send(Request $request)
    {
        $gatewayService = new GatewayService();
        return($gatewayService ->send($request));
    }
}
