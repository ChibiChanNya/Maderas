<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Shipment;
use App\User;

use Carbon\Carbon;

class ShipmentController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'order_id' => 'required',
            'cost' => 'required',
            'certificate' => 'required',
            'status' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $shipment = new Shipment();
        $shipment->order_id = $request->order_id;
        $shipment->cost = $request->cost;
        $shipment->certificate = $request->certificate;
        $shipment->delivery_date = $request->delivery_date ?? null;
        $shipment->status = $request->status;
        $shipment->save();
        
        $admin_user = auth()->user();

        $admin_user->registerLog('creates shipment '. $shipment->id);
        return response()->json(['status' => 'success'], 200);
    }

    public function shipments_list(Request $request){

        $per_page = $request->per_page ?? 10;
        // dd($per_page);

        $orderColumn = 'id';
        $orderType = 'asc';

        if(!empty($request->sort)){
            $arrSort = explode('__',$request->sort);
            $orderColumn = $arrSort[0];
            $orderType = $arrSort[1];
        }

        $shipments = Shipment::with(['details' => function ($q){
            $q->select('name');
        }])->when($request->search, function($q,$req){
            $q->where('status','LIKE','%'.$req.'%');
        })->orderBy($orderColumn, $orderType)->paginate($per_page);

        $shipments->map(function ($i) {
            $details_arr = [];
            $pru_details = [];
            foreach ($i->details as $details) {
                $pru_details['product_id'] = $details->pivot->product_id;
                $pru_details['units'] = $details->pivot->units;
                array_push($details_arr, $pru_details);
            }
            unset($i->details);
            $i->shipment_details = $details_arr;
        });

        return $shipments;
    }

    /**
     * Update a Shipment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shipment_update(Request $request)
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

        $shipment_id = $request->input('id');
        $shipment = Shipment::find($shipment_id);
        if($shipment){
            $shipment->fill($request->all());
            $shipment->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates shipment '. $shipment->id);
            return $shipment;
        }
        
        return $this->respondWithError('no shipment found');
        
    }

    /**
     * Delete a Shipment
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shipment_delete(Request $request)
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

        $shipment_id = $request->input('id');
        $deletion = Shipment::find($shipment_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes shipment '. $shipment_id);

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
