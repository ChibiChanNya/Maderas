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