<?php

namespace App\Http\Middleware;

use App\Models\ApiKeysEvents;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckApikey
{

    private function saveEvent(Request $request) {
        $apikey = $request->header('X-API-KEY');
        $method = $request->method();
        $route  = $request->path();

        $apikeyEvent = new ApiKeysEvents();
        $apikeyEvent -> in_route = $route;
        $apikeyEvent -> method = $method;
        $apikeyEvent -> apikey = $apikey;
        $apikeyEvent -> ip_address = $request->ip();

        $params = $request->all();

        $secrets = ['password','password_confirmation','current_password'];

        foreach ($secrets as $secret) {
            if (array_key_exists($secret,$params)){
                $params[$secret] = 'secret';
            }
        };

        $apikeyEvent -> params = print_r($params,true);
        $apikeyEvent -> token = $request->bearerToken();
        $apikeyEvent -> save();

        return $apikeyEvent;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $apikeyEvent = $this->saveEvent($request);

        $request->request->add(['eventID' => $apikeyEvent->id]);

        if($request->hasHeader('X-API-KEY')){

            $apikey = $request->header('X-API-KEY');
            $method = $request->method();
            $route  = substr($request->getPathInfo(), 1);

            $pk = 'apikey'.'-'.$apikey.'-'.$method.'-'.$route;


            if (Cache::has($pk)) {
                $usuario = Cache::get($pk);
            } else {
                $usuario = DB::select("select 
                                                c.id,
                                                c.name
                                            from clients c inner join clients_keys ck 
                                                on c.id = ck.client_id
                                            inner join api_keys ak 
                                                on ak.client_key_id = ck.id
                                            inner join services s 
                                                on s.id = ak.services_id
                                            where active = '1' and apikey = :apikey and s.method = :method and :route 
                                            regexp 
                                            CASE
                                                    WHEN INSTR(in_route, '[:alpha:]')
                                                    THEN CONCAT('^', REPLACE(in_route, '[:alpha:]', '*'))
                                                    ELSE CONCAT('^', in_route, '$')
                                            END
                                        ",
                    [   'apikey' => $apikey,
                        'method' => $method,
                        'route' => $route
                    ]
                );

                // Guardamos el dato en cache por un día (en minutos)
                Cache::put($pk, $usuario, 1440);
            }

            if(!empty($usuario[0])){
                return $next($request);
            }
        }

        return response()->json([
            'msg' => "Forbidden",
        ],403);
    }
}