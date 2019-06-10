<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\OrderToProvider;
use App\User;

class OrderToProviderController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'provider_id' => 'required',
            'material_id' => 'required',
            'units'  => 'required',
            'request_date' => 'required',
            'delivery_date' => 'required',
            'total_cost' => 'required',
            'status' => 'required',
            'remaining_cost' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $order = new OrderToProvider();
        $order->provider_id = $request->provider_id;
        $order->material_id = $request->material_id;
        // dd(bcrypt($request->password));
        $order->units = $request->units;
        $order->request_date = $request->request_date;
        $order->delivery_date = $request->delivery_date;
        $order->total_cost = $request->total_cost;
        $order->status = $request->status;
        $order->invoice_id = $request->invoice_id ?? null;
        $order->remaining_cost = $request->remaining_cost;
        $order->save();

        $admin_user = auth()->user();

        $admin_user->registerLog('creates order '. $order->id . ' to provider');
        return response()->json(['status' => 'success'], 200);
    }

    public function orders_list(){
        $order = OrderToProvider::all();
        return $order;
    }

    /**
     * Update an Order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $order_id = $request->input('id');
        $order = OrderToProvider::find($order_id);
        if($order){
            $order->fill($request->all());
            $order->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates order '. $order->id);
            return $order;
        }
        
        return $this->respondWithError('no order found');
        
    }

    /**
     * Delete an Order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_delete(Request $request)
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

        $order_id = $request->input('id');
        $deletion = OrderToProvider::find($order_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes order '. $order_id . ' to provider');

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }

    protected function respondWithError($error)
    {
        return response()->json([
            'status' => 'error',
            'errors' => $error
        ], 422);
    }

}
