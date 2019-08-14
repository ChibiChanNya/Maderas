<?php

namespace App\Http\Controllers;

use App\Ledger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class LedgerController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'type' => 'required',
            'recipient' => 'required',
            'amount' => 'required',
            'person' => 'required',
            'concept' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $ledger = new Ledger();
        $ledger->type = $request->type;
        // $order->material_id = $request->material_id;
        // dd(bcrypt($request->password));
        // $order->units = $request->units;
        $ledger->recipient = $request->recipient;
        $ledger->amount = $request->amount;
        $ledger->date = $request->date ?? null;
        $ledger->person = $request->person;
        $ledger->concept = $request->concept;
        $ledger->save();

        // dd($order_details[0]['units']);
        // $order->details()->attach($order_details, ['units' => $units]);

        $admin_user = auth()->user();

        $admin_user->registerLog('creates ledger '. $ledger->id);
        return response()->json(['status' => 'success'], 200);
    }

    public function ledgers_list(Request $request)
    {
        $orderColumn = 'id';
        $orderType = 'asc';

        if(!empty($request->sort)){
            $arrSort = explode('__',$request->sort);
            $orderColumn = $arrSort[0];
            $orderType = $arrSort[1];
        }

        $ledgers = Ledger::orderBy($orderColumn, $orderType)->get();

        return $ledgers;
    }

    public function ledger_update(Request $request)
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

        $ledger_id = $request->input('id');
        $ledger = Ledger::find($ledger_id);
        if($ledger){
            $ledger->fill($request->all());
            $ledger->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates product '. $ledger->id);
            return $ledger;
        }

        return $this->respondWithError('no products found');

    }

    public function ledger_delete(Request $request)
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

        $ledger_id = $request->input('id');
        $deletion = Ledger::find($ledger_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes product '. $ledger_id);

        return response()->json([
            'status' => 'done'
        ], 200);

    }
}
