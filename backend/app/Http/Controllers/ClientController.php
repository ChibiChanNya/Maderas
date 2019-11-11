<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Client;
use App\InvoiceOperations;

class ClientController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'business_name' => 'required',
            'rfc'  => 'required|min:12',
            'email' => 'required',
            'zip' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $client = new Client();
        $client->name = $request->name;
        $client->business_name = $request->business_name;
        $client->description = $request->description;
        // dd(bcrypt($request->password));
        $client->rfc = $request->rfc;
        $client->money_debt = $request->money_debt ?? 0.0;
        $client->email = $request->email;
        $client->zip_code = $request->zip;
        $client->save();

        $invoiceService = new InvoiceOperations();
        $invoiceService->createClient($client);

        $admin_user = auth()->user();

        $admin_user->registerLog('creates client '. $client->id);
        // return response()->json(['status' => 'success'], 200);
        return $client;
    }

    public function clients_list(){
        $client = Client::all();
        return $client;
    }

    /**
     * Update a Client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function client_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'min:3',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $client_id = $request->input('id');
        $client = Client::find($client_id);
        if($client){
            $client->fill($request->all());
            $client->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates client '. $client->id);
            return $client;
        }
        
        return $this->respondWithError('no clients found');
        
    }

    /**
     * Delete a Client
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function client_delete(Request $request)
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

        $client_id = $request->input('id');
        $deletion = Client::find($client_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes client '. $client_id);

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }
}
