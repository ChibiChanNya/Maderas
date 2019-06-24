<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\OrderToProvider;
use App\User;

use Carbon\Carbon;

class OrderToProviderController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'provider_id' => 'required',
            'request_date' => 'required',
            'status' => 'required',
            'remaining_cost' => 'required',
            'order_details' => 'required',
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
        // $order->material_id = $request->material_id;
        // dd(bcrypt($request->password));
        // $order->units = $request->units;
        $order->request_date = $request->request_date;
        $order->delivery_date = $request->delivery_date ?? null;
        $order->payment_date = $request->payment_date ?? null;
        $order->description = $request->description;
        $order->total_cost = $request->total_cost;
        $order->status = $request->status;
        $order->invoice_id = $request->invoice_id ?? null;
        $order->remaining_cost = $request->remaining_cost;
        $order->save();

        $order_details = $request->order_details;
        // dd($order_details);
        foreach($order_details as $key => $value){
            // dd($value['material_id']);
            $order->details()->attach($value['material_id'],['units' => $value['units'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
        }
        // dd($order_details[0]['units']);
        // $order->details()->attach($order_details, ['units' => $units]);
        
        $admin_user = auth()->user();

        $admin_user->registerLog('creates order '. $order->id . ' to provider');
        return response()->json(['status' => 'success'], 200);
    }

    public function orders_list(Request $request){

        $per_page = $request->per_page ?? 10;
        // dd($per_page);

        $orderColumn = 'id';
        $orderType = 'asc';

        if(!empty($request->sort)){
            $arrSort = explode('__',$request->sort);
            $orderColumn = $arrSort[0];
            $orderType = $arrSort[1];
        }

        $orders = OrderToProvider::with(['details' => function ($q){
            $q->select('name');
        }])->when($request->search, function($q,$req){
            $q->where('status','LIKE','%'.$req.'%');
        })->orderBy($orderColumn, $orderType)->paginate($per_page);

        $orders->map(function ($i) {
            $details_arr = [];
            $pru_details = [];
            foreach ($i->details as $details) {
                $pru_details['material_id'] = $details->pivot->material_id;
                $pru_details['units'] = $details->pivot->units;
                array_push($details_arr, $pru_details);
            }
            unset($i->details);
            $i->order_details = $details_arr;
        });

        return $orders;
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

            if(!empty($request->order_details)){
                $order->details()->detach();

                $order_details = $request->order_details;
                foreach($order_details as $key => $value){
                    $order->details()->attach($value['material_id'],['units' => $value['units'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                }
            }

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
