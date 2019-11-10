<?php   

namespace App\Traits;

use App\LogAction;
use App\OrderToProvider;
use App\ClientOrder;
use App\Supply;
use App\Product;
use App\Shipment;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


trait OperationsOrder
{
    public function makeOperation()
    {
        $class_table = get_class($this);
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
        // $details = $this::find($id)->details()->get();
        $details = $this::details()->get();

        $details->map(function ($i) use ($item_name,$supply_class,$stock_name){
            $supply_id = $i->pivot[$item_name];
            $add_units = $i->pivot['units'];
            $supply = $supply_class::find($supply_id);
            $actual_quantity = $supply[$stock_name];
            $supply[$stock_name] = $actual_quantity + $add_units;

            $supply->save();
        });

        $this->operation_dispatched = true;
        $this->save();
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