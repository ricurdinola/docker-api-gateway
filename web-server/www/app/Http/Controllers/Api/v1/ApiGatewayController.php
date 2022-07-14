<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiGatewayController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('token');
    }

    public function send(Request $request){

        //Get de In route and search for the actual out route.
        $route  = $request->path();
        $out_route = \DB::select("select out_route, in_route from services where :route regexp CONCAT('^',in_route)",  ['route' => $route]);


        if(empty($out_route)){
            return response()->json([
                'msg' => "Forbidden",
            ],403);

            //return response(null, 404) -> header('Content-Type', 'application/json');
        }else
        {
            $in_route = $out_route[0]->in_route;
            $out_route = $out_route[0]->out_route;

            //Get Inputs
            $parametros = $request->input();
            $bearer = $request->header('Authorization');

            $pos_params = stripos($in_route, '[:alpha:]');
            if ($pos_params !== false){
                $url_params = substr($route,$pos_params);
                $out_route = $out_route.'/'.$url_params;
            }

            if ($request->method()==='GET'){

                $jsonData = Http::withHeaders([
                    'X-API-KEY' => env('GATEWAY_APIKEY'),
                    'Authorization' => $bearer
                ])->get($out_route,$request->input());

                return response($jsonData->body(), 200) -> header('Content-Type', $jsonData->headers()['Content-Type'][0]);

            }elseif ($request->method()==='POST'){

                //Get de Files and attach to params.
                foreach ($_FILES as $clave => $valor) {
                    for ($i = 0; $i < count($valor['name']); $i++) {
                        $adjunto = new \CURLFile($_FILES[$clave]['tmp_name'][$i], $_FILES[$clave]['type'][$i], $_FILES[$clave]['name'][$i]);
                        $parametros[$clave.'[' . $i . ']'] = $adjunto;
                    }
                }

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $out_route,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_HEADER => 0,
                    CURLOPT_CUSTOMREQUEST => $request->method(),
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_POSTFIELDS => $parametros,
                    CURLOPT_HTTPHEADER => array(
                        'X-API-KEY:' . env('GATEWAY_APIKEY'),
                        'Content-Type:' . 'multipart/form-data',
                        'Authorization:'.$bearer
                    )
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                return response($response, 200) -> header('Content-Type', 'application/json');
            }
        }
    }
}
