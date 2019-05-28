<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login','refresh']]);
    }

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'full_name' => 'required|min:3',
            'password'  => 'required|min:3',
            'description'  => 'min:10',
            'email' => 'email|unique:users',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $user = new User();
        $user->name = $request->name;
        $user->full_name = $request->full_name;
        // dd(bcrypt($request->password));
        $user->password = $request->password;
        $user->device_ip = $request->ip();
        $user->description = $request->description;
        $user->permissions = '00000';
        $user->email = $request->email ? $request->email : $request->name . '@email.com';
        $user->save();
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['name', 'password']);
        $token = auth()->attempt($credentials);
        if (!$token) {
            return response()->json(['error' => 'Bad Credentials'], 401);
        }

        $device_ip = request(['device_ip']);
        $user = auth()->user();
        // dd($device_ip);
        $user->last_login = Carbon::now();
        $user->device_ip = $request->ip();
        $user->access_token = $token;
        $user->save();

        $response = $this->respondWithToken($token);
        $response->original['permissions'] = $user->permissions;

        return $response->original;
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = auth()->user();
        $user->access_token = null;
        $user->save();

        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $token= explode(" ", $request->header('authorization'))[1];
        $user = $request->input('name');
        $possible_user = User::where('name',$user)->where('access_token',$token)->first();
        if($possible_user->count() > 0){
            $new_token = auth()->refresh();
            $possible_user->access_token = $new_token;
            $possible_user->save();
            return $this->respondWithToken($new_token);
            // return response()->json(['success','user found'], 200);
        } else {
            return response()->json(['error','user not found'], 400);
        }
        // return $this->respondWithToken(auth()->refresh());
    }

    public function prueba()
    {
        return 'Hola';
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
