<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InvoiceOperations;
use App\Shipment;
use Validator;

class InvoiceController extends Controller
{
    public function __construct() {
        $this->invoiceService = new InvoiceOperations();
    }

    public function invoices_list(Request $request)
    {
        $invoices = $this->invoiceService->listInvoices();

        // return response()->json(['status' => 'success'], 200);
        return $invoices;
    }

    public function unit_codes(Request $request)
    {
        $codes = $this->invoiceService->listUnitCodes();

        // return response()->json(['status' => 'success'], 200);
        return $codes;
    }

    public function payment_forms(Request $request)
    {
        $codes = $this->invoiceService->listPaymentForms();

        // return response()->json(['status' => 'success'], 200);
        return $codes;
    }

    public function payment_methods(Request $request)
    {
        $codes = $this->invoiceService->listPaymentMethods();

        // return response()->json(['status' => 'success'], 200);
        return $codes;
    }

    public function cfdi_uses(Request $request)
    {
        $codes = $this->invoiceService->listCfdiUses();

        // return response()->json(['status' => 'success'], 200);
        return $codes;
    }

    public function create_cfdi(Request $request)
    {
        $v = Validator::make($request->all(), [
            'shipment_id' => 'required',
            'cfdi_use' => 'required',
            'payment_form' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $shipment_id = $request->input('shipment_id');
        $cfdi_use = $request->input('cfdi_use');
        $payment_form = $request->input('payment_form');

        $shipment = Shipment::find($shipment_id);

        if ($shipment){
            $confirmation = $this->invoiceService->createCfdi($shipment, $cfdi_use, $payment_form);
            return $confirmation;
        }
        
        return $this->respondWithError('no shipment found');
    }

    public function send_cfdi(Request $request)
    {
        $v = Validator::make($request->all(), [
            'cfdi_uid' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $cfdi_uid = $request->input('cfdi_uid');

        $response = $this->invoiceService->sendCfdi($cfdi_uid);

        return $response;
    }

    public function cancel_cfdi(Request $request)
    {
        $v = Validator::make($request->all(), [
            'cfdi_uid' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $cfdi_uid = $request->input('cfdi_uid');

        $response = $this->invoiceService->cancelCfdi($cfdi_uid);

        return $response;
    }

    protected function respondWithError($error)
    {
        return response()->json([
            'status' => 'error',
            'errors' => $error
        ], 422);
    }
}
