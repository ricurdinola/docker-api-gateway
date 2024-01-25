<?php
namespace App\Services;

use App\Models\ApiKeysEvents;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayService
{

    private function registerResponse(Request $request, Response|String $jsonData)
    {
        try {
            $apikeyEvent  = ApiKeysEvents::FindOrFail($request->eventID);
            if ($jsonData instanceof Response) {
                $apikeyEvent -> response = $jsonData->body();
            }else{
                $apikeyEvent -> response = $jsonData;
            }
            $apikeyEvent -> save();
        }catch(Exception $e){
            Log::error("Ocurrio un error al guardar la respuesta con el id del evento: ".$request->eventID);
        }
    }

    public function send(Request $request)
    {
        //Get de In route and search for the actual out route.
        $route  = $request->path();
        $out_route = \DB::select("select  
                                        out_route, 
                                        in_route 
                                  from services 
                                  where :route 
                                  regexp 
                                  CASE
                                        WHEN INSTR(in_route, '[:alpha:]')
                                        THEN CONCAT('^', REPLACE(in_route, '[:alpha:]', '*'))
                                        ELSE CONCAT('^', in_route, '$')
                                 END
                            ",  ['route' => $route]);

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

            if ($request->method() === 'GET' ){

                $jsonData = Http::withHeaders([
                    'X-API-KEY' => config('app.GATEWAY_APIKEY'),
                    'Authorization' => $bearer
                ])->get($out_route,$request->input());

                //Guardamos Respuesta
                $this->registerResponse($request,$jsonData);

                $gatewayResponse = response($jsonData->body(), 200);
                $gatewayResponse -> header('Content-Type', $jsonData->headers()['Content-Type'][0]);

                if (isset($jsonData->headers()['Content-Disposition'][0])){
                    $gatewayResponse -> header('Content-Disposition',$jsonData->headers()['Content-Disposition'][0]);
                }

                return $gatewayResponse;

            }elseif ($request->method()==='PUT'){

                $jsonData = Http::withHeaders([
                    'X-API-KEY' => config('app.GATEWAY_APIKEY'),
                    'Authorization' => $bearer
                ])->put($out_route,$request->input());

                //Guardamos Respuesta
                $this->registerResponse($request,$jsonData);

                return response($jsonData->body(), 200) -> header('Content-Type', $jsonData->headers()['Content-Type'][0]);

            }elseif ($request->method()==='PATCH'){

                $jsonData = Http::withHeaders([
                    'X-API-KEY' => config('app.GATEWAY_APIKEY'),
                    'Authorization' => $bearer
                ])->patch($out_route,$request->input());

                //Guardamos Respuesta
                $this->registerResponse($request,$jsonData);

                return response($jsonData->body(), 200) -> header('Content-Type', $jsonData->headers()['Content-Type'][0]);

            }elseif ($request->method()==='DELETE'){

                $jsonData = Http::withHeaders([
                    'X-API-KEY' => config('app.GATEWAY_APIKEY'),
                    'Authorization' => $bearer
                ])->delete($out_route,$request->input());

                //Guardamos Respuesta
                $this->registerResponse($request,$jsonData);

                return response($jsonData->body(), 200) -> header('Content-Type', $jsonData->headers()['Content-Type'][0]);

            }
            elseif ($request->method()==='POST'){

                $parametros_send = null;

                //Recorremos cada parametro recibido para armar el nuevo array de parametros
                foreach ($parametros as $clave => $valor) {

                    /*Si es un array, se mandan varios parametros con el siguiente formato
                        array_param[0]: "1"
                        array_param[1]: "2"
                    */

                    if (is_array($valor)){
                        $i = 0;
                        foreach ($valor as $subClave => $subValor) {
                            $parametros_send[$clave.'[' . $i . ']'] = $subValor;
                            $i++;
                        }
                    }else{
                        $parametros_send[$clave] = $valor;
                    }
                }

                //Hacemos lo mismo para los Files
                foreach ($_FILES as $clave => $valor) {
                    for ($i = 0; $i < count($valor['name']); $i++) {
                        $adjunto = new \CURLFile($_FILES[$clave]['tmp_name'][$i], $_FILES[$clave]['type'][$i], $_FILES[$clave]['name'][$i]);
                        $parametros_send[$clave.'[' . $i . ']'] = $adjunto;
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
                    CURLOPT_POSTFIELDS => $parametros_send,
                    CURLOPT_HTTPHEADER => array(
                        'X-API-KEY:' . config('app.GATEWAY_APIKEY'),
                        'Content-Type:' . 'multipart/form-data',
                        'Authorization:'.$bearer
                    )
                ));

                $response = curl_exec($curl);
                curl_close($curl);
                $contentType = curl_getinfo( $curl , CURLINFO_CONTENT_TYPE );

                //Guardamos Respuesta
                $this->registerResponse($request,$response);

                return response($response, 200) -> header('Content-Type', $contentType);
            }
        }
    }

    public static function checkBearerPresent(Request $request){
        $token = $request->bearerToken();

        if (!$token) {
            $response = [
                'status_code' => 401,
                'status' => 'Unauthorized',
                'msg' => 'Debe autenticarse para realizar la operación.'
            ];
            response()->json($response)->send();
            exit;
        }
        if (!auth()->check()) {
            $response = [
                'status_code' => 401,
                'status' => 'Unauthorized',
                'msg' => 'Token InvÃ¡lido para realizar la operación.'
            ];
            response()->json($response)->send();
            exit;
        }
    }

    public static function getPermisos(Request $request){
        $token = $request->bearerToken();
        return json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))),true)['permisos'];
    }

    public static function checkPermiso(Request $request, $permiso_requerido,$obligatorio){
        $token = $request->bearerToken();

        $permisos = self::getPermisos($request);

        if(in_array($permiso_requerido, $permisos) == false){

            if($obligatorio === 'S'){
                $response = [
                    'status_code' => 403,
                    'status' => 'Forbidden',
                    'msg' => 'No posee los permisos para realizar la operación.'
                ];
                response()->json($response)->send();
                exit;
            }
            else{
                return false;
            }
        }else{
            return true;
        }
    }
}