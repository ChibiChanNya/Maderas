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
        $client->invoice_uid = $response->Data->UID;
        $client->save();
    }    

    public function reverseOperation($units = null)
    {
        $class_table = get_class($this);
        // $class_table === OrderToProvider::class
        // $details = $this::find($id)->details()->get();
        if ($class_table === OrderToProvider::class) {
            $item_name = 'material_id';
            $supply_class = Supply::class;
            $stock_name = 'available_stock';
        }
        if ($class_table === Shipment::class) {
            $item_name = 'product_id';
            $supply_class = Product::class;
            $stock_name = 'stock';
        }
        // if ($class_table === ClientOrder::class) {
        //     $item_name = 'product_id';
        //     $supply_class = Product::class;
        //     $stock_name = 'stock';
        // }
        $details = $this::details()->get();

        $details->map(function ($i) use ($item_name,$supply_class,$stock_name){
            $supply_id = $i->pivot[$item_name];
            $rest_units = $i->pivot['units'];
            $supply = $supply_class::find($supply_id);
            $actual_quantity = $supply[$stock_name];
            $supply[$stock_name] = $actual_quantity - $rest_units;

            $supply->save();
        });

        $this->operation_dispatched = false;
        $this->save();
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