<?php   

namespace App\Traits;

use App\LogAction;
use App\OrderToProvider;
use App\Supply;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


trait OperationsOrder
{
    public function makeOperation($id)
    {
        $class_table = get_class($this);
        // $class_table === OrderToProvider::class
        $details = $this::find($id)->details()->get();

        $details->map(function ($i) {
            $supply_id = $i->pivot['material_id'];
            $add_units = $i->pivot['units'];
            $supply = Supply::find($supply_id);
            $actual_quantity = $supply->available_stock;
            $supply->available_stock = $actual_quantity + $add_units;

            $supply->save();
        });
    }    

    public function reverseOperation($id)
    {
        $class_table = get_class($this);
        // $class_table === OrderToProvider::class
        $details = $this::find($id)->details()->get();

        $details->map(function ($i) {
            $supply_id = $i->pivot['material_id'];
            $rest_units = $i->pivot['units'];
            $supply = Supply::find($supply_id);
            $actual_quantity = $supply->available_stock;
            $supply->available_stock = $actual_quantity - $rest_units;

            $supply->save();
        });
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