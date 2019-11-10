<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\OperationsOrder;

class Shipment extends Model
{
    use OperationsOrder;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id', 'cost', 'certificate', 'delivery_date', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    // public function order_details()
    // {
    //     return $this->hasMany('orders_to_providers_details', 'order_id');
    // }
    public function details(){
        // return $this->belongsToMany('App\Product', 'client_orders_details', 'order_id', 'product_id','order_id')->withPivot('product_id', 'units');
        return $this->belongsToMany('App\Product', 'shipments_details', 'shipment_id', 'product_id')->withPivot('product_id', 'units');
    }
}
