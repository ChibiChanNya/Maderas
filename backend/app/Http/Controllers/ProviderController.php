<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Provider;
use App\User;

class ProviderController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'business_name' => 'required|min:3',
            'description'  => 'required',
            'rfc' => 'required|min:3',
            'clabe' => 'required|min:3',
            'bank' => 'required|min:3',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $provider = new Provider();
        $provider->name = $request->name;
        $provider->business_name = $request->business_name;
        // dd(bcrypt($request->password));
        $provider->description = $request->description;
        $provider->rfc = $request->rfc;
        $provider->clabe = $request->clabe;
        $provider->bank = $request->bank;
        $provider->save();

        $admin_user = auth()->user();

        $admin_user->registerLog('creates provider '. $provider->id);
        // return response()->json(['status' => 'success'], 200);
        return $provider;
    }

    public function providers_list(){
        $provider = Provider::all();
        return $provider;
    }

    /**
     * Update a Provider
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function provider_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'min:3',
            'business_name' => 'min:3',
            'description'  => 'min:3',
            'rfc' => 'min:3',
            'clabe' => 'min:3',
            'bank' => 'min:3',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $provider_id = $request->input('id');
        $provider = Provider::find($provider_id);
        if($provider){
            $provider->fill($request->all());
            $provider->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates provider '. $provider->id);
            return $provider;
        }
        
        return $this->respondWithError('no provider found');
        
    }

    /**
     * Delete a User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function provider_delete(Request $request)
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

        $provider_id = $request->input('id');
        $deletion = Provider::find($provider_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes provider '. $provider_id);

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }
}
