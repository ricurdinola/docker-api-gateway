<?php

namespace app\Http\Middleware;

use App\Services\GatewayService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CheckBearerToken
{

    public function handle(Request $request, Closure $next)
    {

        $method = $request->method();
        $route  = substr($request->getPathInfo(), 1);

        $br = 'bearer'.'-'.$method.'-'.$route;
        if (Cache::has($br)) {
            $servicio = Cache::get($br);
        } else {
            $servicio = DB::select("select *
                                      from services s 
                                      where s.method = :method and :route 
                                        regexp 
                                        CASE
                                                WHEN INSTR(in_route, '[:alpha:]')
                                                THEN CONCAT('^', REPLACE(in_route, '[:alpha:]', '*'))
                                                ELSE CONCAT('^', in_route, '$')
                                        END
                                     ",
                [
                    'method' => $method,
                    'route' => $route
                ]);

            // Guardamos el dato en cache por un día (en minutos)
            Cache::put($br, $servicio, 1440);
        }

        if(empty($servicio[0])){
            return response()->json(['msg' => "Forbidden",],403);
        }

        $servicio = $servicio[0];

        if ($servicio->login === 'S'){

            GatewayService::checkBearerPresent($request);

            if(!empty($servicio->permisos)){
                $permisos =  GatewayService::getPermisos($request);
                if (count( array_intersect($permisos, json_decode($servicio->permisos))) > 0){
                    return $next($request);
                }else{
                    return response()->json(['msg' => "Forbidden",],403);
                }
            }
        }

        return $next($request);
    }
}