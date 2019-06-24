<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Supply;
use App\User;

class SupplyController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'type' => 'required|min:3',
            'provider_id'  => 'required|numeric',
            'recent_price'  => 'required',
            'available_stock' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $supply = new Supply();
        $supply->name = $request->name;
        $supply->type = $request->type;
        // dd(bcrypt($request->password));
        $supply->provider_id = $request->provider_id;
        $supply->recent_price = $request->recent_price;
        $supply->available_stock = $request->available_stock;
        $supply->save();

        $admin_user = auth()->user();

        $admin_user->registerLog('creates supply '. $supply->id);
        return response()->json(['status' => 'success'], 200);
    }

    public function supplies_list(){
        $supply = Supply::all();
        return $supply;
    }

    /**
     * Update a Supply
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function supply_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'min:3',
            'type' => 'min:3',
            'provider_id'  => 'numeric',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $supply_id = $request->input('id');
        $supply = Supply::find($supply_id);
        if($supply){
            $supply->fill($request->all());
            $supply->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates supply '. $supply->id);
            return $supply;
        }
        
        return $this->respondWithError('no provider found');
        
    }

    /**
     * Delete a User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function supply_delete(Request $request)
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

        $supply_id = $request->input('id');
        $deletion = Supply::find($supply_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes supply '. $supply_id);

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }
}
