<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\OperationsOrder;

class ClientOrder extends Model
{
    use OperationsOrder;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'contract', 'request_date', 'finish_date', 'payment_date', 'description', 'total_cost', 'status', 'money_debt', 'user',
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
        return $this->belongsToMany('App\Product', 'client_orders_details', 'order_id', 'product_id')->withPivot('product_id', 'units');
    }

    public function client(){
        return $this->belongsTo('App\Client', 'client_id');
    }

    public function shipments(){
        return $this->hasMany('\App\Shipment', 'order_id');
    }
}
