<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Product;
use App\Ledger;
use App\Provider;

class DashBoardController extends Controller
{
    public function graph_one(Request $request)
    {
        $products = Product::all();

        $product_format = [];
        foreach($products as $product){
            $information = $this->product_operations($product);
            $format = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'contracts' => $information['contracts'],
                'stock' => $product->stock,
                'requested' => $information['requested'],
            ];

            array_push($product_format, $format);
        }

        return $product_format;
    }

    public function graph_two(Request $request)
    {

    }

    public function graph_three(Request $request)
    {
        $data = Ledger::select(DB::raw('MONTH(date) as month'),'type','amount')->get();

        $result = [
            "1" => 0,
            "2" => 0,
            "3" => 0,
            "4" => 0,
            "5" => 0,
            "6" => 0,
            "7" => 0,
            "8" => 0,
            "9" => 0,
            "10" => 0,
            "11" => 0,
            "12" => 0,
        ];
        foreach ($data as $element) {
            $type = strtolower($element['type']);
            $amount = 0;

            switch ($type) {
                case 'ingreso':
                    $amount =  $result[$element['month']] + (int)$element['amount'];
                    break;
                case 'pago':
                    $amount = $result[$element['month']] - (int)$element['amount'];
                    break;
            }

            $result[$element['month']] = $amount;
        }

        return $this->transform_months($result);
    }

    public function graph_four(Request $request)
    {
        $providers = Provider::all();

        $provider_format = [];
        foreach($providers as $provider){
            $information = $this->provider_operations($provider);
            $format = [
                'name' => $provider->name,
                'amount' => $information['amount'],
            ];

            array_push($provider_format, $format);
        }

        return $provider_format;
    }

    private function product_operations($product)
    {
        $order_details = $product->requested;
        $orders = $product->client_orders;

        $units = 0;
        $orders_array = [];
        foreach($order_details as $detail){
            $units += $detail->units;
        }

        foreach($orders as $order){
            array_push($orders_array, $order->contract);
        }

        return ['requested' => $units, 'contracts' => $orders_array];
    }

    private function provider_operations($provider)
    {
        $orders = $provider->orders;

        $amount = 0;
        foreach($orders as $order){
            $amount += $order->remaining_cost;
        }

        return ['amount' => $amount];
    }

    private function transform_months($ledgers)
    {
        $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $format = [];
        $index = 0;
        foreach($ledgers as $ledger){
            $format[$months[$index]] = $ledger;
            $index++;
        }

        return $format;
    }
    
}
