<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App\User;
use App\LogAction;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'username' => 'required|min:3',
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
        $user->username = $request->username;
        $user->full_name = $request->full_name;
        // dd(bcrypt($request->password));
        $user->password = $request->password;
        $user->device_ip = $request->ip();
        $user->description = $request->description;
        $user->permissions = $request->permissions;
        $user->email = $request->email ? $request->email : $request->username . '@email.com';
        $user->save();

        $admin_user = auth()->user();

        $admin_user->registerLog('creates user '. $user->id);
        return response()->json(['status' => 'success'], 200);
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
     * Get the list of Users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function users_list()
    {
        $users = User::all();
        return $users;
    }

    /**
     * Update a User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'username' => 'min:3',
            'full_name' => 'min:3',
            'password'  => 'min:3',
            'description'  => 'min:10',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $user_id = $request->input('id');
        if($user_id) {
            $user = User::find($user_id);
            if($user){
                $user->fill($request->all());
                $user->save();

                $admin_user = auth()->user();
                $admin_user->registerLog('updates user '. $user->id);
                return $user;
            }
            
            return $this->respondWithError('no user found');
        } else {
            $error = 'null action';
            return $this->respondWithError($error);
        }
        
    }

    /**
     * Delete a User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function user_delete(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $user_id = $request->input('id');
        $deletion = User::find($user_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes user '. $user_id);

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }

    public function all_log(){
        $all_log = LogAction::all();
        return $all_log;
    }

    public function user_log(Request $request){
        $user_id = $request->input('id');
        // dd($user_id);
        $all_log = LogAction::where('user_id',$user_id)->get();
        return $all_log;
    }

    protected function respondWithError($error)
    {
        return response()->json([
            'status' => 'error',
            'errors' => $error
        ], 422);
    }

}
