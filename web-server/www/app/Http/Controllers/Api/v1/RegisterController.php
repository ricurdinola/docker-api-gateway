<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller{

    public function __construct()
    {
        $this->middleware('token');
    }

    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'd_nombre' => 'required',
            'd_apellido' => 'required',
            'f_nacimiento' => 'required',
            'n_dni' => 'required',
            'n_cuil_cuit' => 'required',
            'n_tramite' => 'required',
            'b_dni_front' => 'required',
            'b_dni_back' => 'required',
            'id_departamento' => 'required',
            'id_localidad' => 'required',
            'd_domicilio' => 'required',
            'd_email' => 'required|email|unique:users,email',
            'n_telefono' => 'required',
            'd_password' => 'required|confirmed|min:6',
            'd_password_confirmation' => 'required',
            'm_agree' => 'required'
        ], [
            'd_nombre.required' => 'Ingrese su nombre',
            'd_apellido.required' => 'Ingrese su apellido',
            'f_nacimiento.required' => 'Ingrese su fecha de nacimiento',
            'n_dni.required' => 'Ingrese su DNI',
            'n_cuil_cuit.required' => 'Ingrese su CUIL/CUIT',
            'n_tramite.required' => 'Ingrese su número de trámite',
            'b_dni_front.required' => 'Iingrese el frente de su DNI',
            'b_dni_back.required' => 'Ingrese el dorso de su DNI',
            'b_cons_cuil.required' => 'Ingrese su constancia de CUIL',
            'id_departamento.required' => 'Ingrese su departamento',
            'id_localidad.required' => 'Ingrese su localidad',
            'd_domicilio.required' => 'Ingrese su domicilio',
            'd_email.required' => 'Ingrese su email',
            'd_email.email' => 'La dirección de email ingresada no es válida',
            'd_email.unique' => 'La dirección de email ingresada ya se encuentra registrada',
            'n_telefono.required' => 'Ingrese su telefono',
            'd_password.required' => 'Ingrese su contraseña',
            'd_password.confirmed' => 'Las contraseñas no coinciden',
            'd_password.min' => 'La contraseña debe tener al menos seis caracteres',
            'd_password_confirmation.required' => 'Confirme su contraseña',
            'm_agree.required' => 'Debe aceptar los terminos y condiciones',
        ]);

        if ($validator->fails()) {
            $response['status_code'] = 400;
            $response['status'] = 'fail';
            $response['msg'] = $validator->errors();

            return $response;
        }

        // Retrieve the validated input...
        $validated = $validator->validated();

        if($validated['m_agree'] == 1){

            //Da de alta el usuario
            $userId = User::create([
                'name' => $validated['d_nombre'] . ' ' . $validated['d_apellido'],
                'email' => $validated['d_email'],
                'n_cuil_cuit' => $validated['n_cuil_cuit'],
                'password' => Hash::make($validated['d_password'])
            ])->id;

            //Da de alta la persona
            $response = Http::withHeaders([
                'X-API-KEY' => env('GATEWAY_APIKEY')
            ])->post('localhost/newPersona', [
                'd_nombre' => $validated['d_nombre'],
                'd_apellido' => $validated['d_apellido'],
                'f_nacimiento' => $validated['f_nacimiento'],
                'n_dni' => $validated['n_dni'],
                'n_cuil_cuit' => $validated['n_cuil_cuit'],
                'n_tramite' => $validated['n_tramite'],
                'id_usuario' => $userId
            ]);

            $response = json_decode($response, true);
            if ($response['status_code']!= 200){

                User::destroy($userId);
                return $response;

            }else{

                $idPersona = $response['id_persona'];

                //Da de alta el domicilio
                $response = Http::withHeaders([
                    'X-API-KEY' => env('GATEWAY_APIKEY')
                ])->post('localhost/newDomicilio', [
                    'id_persona' => $idPersona,
                    'id_departamento' => $request->id_departamento,
                    'id_localidad' => $request->id_localidad,
                    'd_domicilio' => $request->d_domicilio
                ]);
                if ($response['status_code']!= 200){
                    return $response;
                }

                //Da de alta el telefono
                $response = Http::withHeaders([
                    'X-API-KEY' => env('GATEWAY_APIKEY')
                ])->post('localhost/newContacto', [
                    'id_persona' => $idPersona,
                    'c_tipo' => 'TEL',
                    'd_dato' => $request->n_telefono
                ]);
                if ($response['status_code']!= 200){
                    return $response;
                }

                //Da de alta el email
                $response = Http::withHeaders([
                    'X-API-KEY' => env('GATEWAY_APIKEY')
                ])->post('localhost/newContacto', [
                    'id_persona' => $idPersona,
                    'c_tipo' => 'EMAIL',
                    'd_dato' => $request->d_email
                ]);
                if ($response['status_code']!= 200){
                    return $response;
                }


                $parametros = array();
                $parametros['id_persona'] = $idPersona;
                $parametros['c_tipo_documentacion'] = 'DNI_FRONT';
                $parametros['b_file[0]'] = new \CURLFile($_FILES['b_dni_front']['tmp_name'], $_FILES['b_dni_front']['type'], $_FILES['b_dni_front']['name']);
                $response = $this->altaDocumentacion($parametros);
                
                if ($response['status_code']!= 200){
                    return $response;
                }

                $parametros = array();
                $parametros['id_persona'] = $idPersona;
                $parametros['c_tipo_documentacion'] = 'DNI_BACK';
                $parametros['b_file[0]'] = new \CURLFile($_FILES['b_dni_back']['tmp_name'], $_FILES['b_dni_back']['type'], $_FILES['b_dni_back']['name']);
                $response = $this->altaDocumentacion($parametros);
                
                if ($response['status_code']!= 200){
                    return $response;
                }

                if ($_FILES['b_cons_cuil']['error']==0){
                    $parametros = array();
                    $parametros['id_persona'] = $idPersona;
                    $parametros['c_tipo_documentacion'] = 'C_CUIL';
                    $parametros['b_file[0]'] = new \CURLFile($_FILES['b_cons_cuil']['tmp_name'], $_FILES['b_cons_cuil']['type'], $_FILES['b_cons_cuil']['name']);
                    $response = $this->altaDocumentacion($parametros);

                    if ($response['status_code']!= 200){
                        return $response;
                    }
                }

                return response()->json([
                    'status_code' => 200,
                    'status' => 'success'
                ]);
            }
        }
    }

    private function altaDocumentacion($parametros){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'localhost/newDocumentacion',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $parametros,
            CURLOPT_HTTPHEADER => array(
                'X-API-KEY:' . env('GATEWAY_APIKEY'),
                'Content-Type:' . 'multipart/form-data'
            )
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }
}