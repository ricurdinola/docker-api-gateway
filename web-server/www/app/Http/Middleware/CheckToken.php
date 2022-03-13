<?php

namespace App\Http\Middleware;

use App\Models\ApiKeysEvents;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apikey = $request->header('X-API-KEY');
        $method = $request->method();
        $route  = $request->path();

        $apikeyEvent = new ApiKeysEvents();
        $apikeyEvent -> in_route = $route;
        $apikeyEvent -> method = $method;
        $apikeyEvent -> apikey = $apikey;
        $apikeyEvent -> ip_address = $request->ip();
        $apikeyEvent -> save();

        $route = substr($request->getPathInfo(), 1);

        if($request->hasHeader('X-API-KEY')){
            //die(print_r($request->input()));
            $usuario = DB::select("select 
                                            c.id,
                                            c.name
                                        from clients c inner join clients_keys ck 
                                            on c.id = ck.client_id
                                        inner join api_keys ak 
                                            on ak.client_key_id = ck.id
                                        inner join services s 
                                            on s.id = ak.services_id
                                        where active = '1' and apikey = :apikey and s.method = :method and s.in_route = :route",
                [   'apikey' => $apikey,
                    'method' => $method,
                    'route' => $route
                ]
            );

            if(!empty($usuario[0])){
                return $next($request);
            }
        }

        return response()->json([
            'msg' => "Forbidden",
        ],403);
    }
}
