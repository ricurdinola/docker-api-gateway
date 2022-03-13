<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function ping(Request $request){
        $parametros = $request->input();

        //Get de Files and attach to params.
        foreach ($_FILES as $clave => $valor) {
            for ($i = 0; $i < count($valor['name']); $i++) {
                $adjunto= new \CURLFile($_FILES[$clave]['tmp_name'][$i],$_FILES[$clave]['type'][$i],$_FILES[$clave]['name'][$i]);
                $parametros['adjuntos['.$i.']'] = $adjunto;
            }
            //return response($parametros, 200) -> header('Content-Type', 'application/json');
        }

        return response()->json([
            'status' => 'success',
            'status_code' => '200',
            'parametros' => $parametros,
            'msg' => 'Aca Estamos '. $request->method().'! :-)',
        ]);
    }

    //
}
