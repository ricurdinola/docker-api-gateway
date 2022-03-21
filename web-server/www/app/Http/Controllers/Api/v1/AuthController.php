<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'loginExt','register','getUserById']]);
        $this->middleware('token');
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function loginExt()
    {
        $credentials = request(['n_cuil_cuit', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => User::passwordRules(),
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return response()->json([
            'msg' => "Registro Exitoso",
            'data' => $check
        ],201);

        //return redirect("dashboard")->withSuccess('You have signed-in');
    }

    public function getUserById(Request $request)
    {
        $requestedId = $request->input('id');
        $user = User::where('id', $requestedId)->get();

        return response()->json([
            'data' => $user
        ],201);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();

        $validator = Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => User::passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (! isset($input['current_password']) || ! Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        });

        if ($validator->fails()) {
            $response['status_code'] = 400;
            $response['status'] = 'fail';
            $response['msg'] = $validator->errors();

            return $response;
        }

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        return response()->json([
            'msg' => "Contraseña Modificada",
        ],201);
    }

}
