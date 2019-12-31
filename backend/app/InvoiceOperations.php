<?php   

namespace App;

use App\LogAction;
use App\OrderToProvider;
use App\ClientOrder;
use App\Supply;
use App\Product;
use App\Shipment;
use App\Client;
use Auth;
use Carbon\Carbon;
use GuzzleHttp;
use Exception;
use Illuminate\Database\Eloquent\Model;


class InvoiceOperations
{
    public function __construct()
    {
        $this->headers = [
            'F-PLUGIN' => env('INVOICE_PLUGIN'),
            'F-Api-Key' => env('INVOICE_KEY'),
            'F-Secret-Key' => env('INVOICE_SECRET'),
        ];
        $this->http = new GuzzleHttp\Client(['base_uri' => 'http://devfactura.in/api/', 'headers' => $this->headers]);
    }

    public function createClient(Client $client)
    {
        $name = $client->name;
        $email = $client->email;
        $razons = $client->business_name;
        $rfc = $client->rfc;
        $zip = $client->zip_code;

        $invoice_client = [
            'nombre' => $name,
            'email' => $email,
            'razons' => $razons,
            'rfc' => $rfc,
            'codpos' => $zip,
        ];

        $params = ['json' => $invoice_client];

        //$request = $this->http->request('GET', 'v1/clients');
        $request = $this->http->request('POST', 'v1/clients/create', $params);
        //dd($request);
        //dd($request->getStatusCode());
        $response = json_decode((string) $request->getBody()->getContents(), false);
        // dd($response->Data->UID);
        // dd($response['Data']->UID);
        if ($response->status == 'error') {
            //TODO: erase client when is an error
            $data = json_encode($response->message);
            throw new Exception($data);
        }
        $client->invoice_uid = $response->Data->UID;
        $client->save();
    }   

    public function updateClient(Client $client)
    {
        $name = $client->name;
        $email = $client->email;
        $razons = $client->business_name;
        $rfc = $client->rfc;
        $zip = $client->zip_code;
        $uid = $client->invoice_uid;

        $existance = $this->http->request('GET', 'v1/clients/' . $rfc);
        $response_existance = json_decode((string) $existance->getBody()->getContents(), false);
        if ($response_existance->message == 'El cliente no existe') {
            return $this->createClient($client);
        }

        $invoice_client = [
            'nombre' => $name,
            'email' => $email,
            'razons' => $razons,
            'rfc' => $rfc,
            'codpos' => $zip,
        ];

        $params = ['json' => $invoice_client];

        //$request = $this->http->request('GET', 'v1/clients');
        $request = $this->http->request('POST', 'v1/clients/' . $uid . '/update', $params);
        $response = json_decode((string) $request->getBody()->getContents(), false);
    }

    public function createCfdi(Shipment $shipment, string $cfdi_use, string $payment_form)
    {
        $client_order = $shipment->order;
        $client = $client_order->client;
        $client_reference = $client->invoice_uid;
        $document_type = "factura";
        $payment_method = "PUE";
        $currency = "MXN";

        $details = $shipment->details;
        $concepts = [];
        foreach($details as $key => $value){
            $item = [
                'ClaveProdServ' => $value['product_service_code'],
                'NoIdentificacion' => $value['sku'],
                'Cantidad' => $value->pivot->units,
                'ClaveUnidad' => $value['unit_code'],
                'Unidad' => $value['unit_description'],
                'ValorUnitario' => $value['price'],
                'Descripcion' => $value['description'],
            ];

            $response = $this->checkParameters($item, $value);

            if (!empty($response)) {
                $data = json_encode($response);
                throw new Exception($data);
            }

            array_push($concepts, $item);
        }
        
        $request = $this->http->request('GET', 'v1/series');
        $response = json_decode((string) $request->getBody()->getContents(), false);
        $key = array_search("F", array_column($response->data, 'SerieName'));
        $serie_code = $response->data[$key]->SerieID;

        $invoice_info = [
            'Receptor' => [
                'UID' => $client_reference,
            ],
            'TipoDocumento' => $document_type,
            'Conceptos' => $concepts,
            'UsoCFDI' => $cfdi_use,
            'Serie' => $serie_code,
            'FormaPago' => $payment_form,
            'MetodoPago' => $payment_method,
            'Moneda' => $currency,
        ];

        $params = ['json' => $invoice_info];
        $request = $this->http->request('POST', 'v3/cfdi33/create', $params);
        $response = json_decode((string) $request->getBody()->getContents(), false);

        if ($response->response == 'error') {
            $data = json_encode($response->message);
            throw new Exception($data);
        }
        
        return json_encode($response);
    } 

    public function listUnitCodes()
    {
        $request = $this->http->request('GET', 'v3/catalogo/ClaveUnidad');
        $response = json_decode((string) $request->getBody()->getContents(), true);

        return $response;
    }  

    public function listPaymentMethods()
    {
        $request = $this->http->request('GET', 'v3/catalogo/MetodoPago');
        $response = json_decode((string) $request->getBody()->getContents(), true);

        return $response;
    }   

    public function listPaymentForms()
    {
        $request = $this->http->request('GET', 'v3/catalogo/FormaPago');
        $response = json_decode((string) $request->getBody()->getContents(), true);

        return $response;
    }   

    public function listCfdiUses()
    {
        $request = $this->http->request('GET', 'v3/catalogo/UsoCfdi');
        $response = json_decode((string) $request->getBody()->getContents(), true);

        return $response;
    }   

    public function listInvoices()
    {
        $request = $this->http->request('GET', 'v3/cfdi33/list');
        $response = json_decode((string) $request->getBody()->getContents(), true);

        return $response;
    }   

    protected function checkParameters($array, $item)
    {
        $errors = [];
        foreach ($array as $key => $value) {
            if (!isset($value)) {
                array_push($errors, 'No ' . $key . ' in product ' . $item['id']);
            }
        }

        return $errors;
    }

    protected static function getRecordActivityEvents()
    {
        if (isset(static::$recordEvents)) {
            return static::$recordEvents;
        }

        return [
            'created',
            'updated',
            'deleted',
        ];
    }

}