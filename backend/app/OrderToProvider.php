<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderToProvider extends Model
{
    protected $table = 'orders_to_providers';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id', 'material_id', 'units', 'request_date', 'delivery_date', 'total_cost', 'status', 'invoice_id', 'remaining_cost'
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
        return $this->belongsToMany('App\Supply', 'orders_to_providers_details', 'order_id', 'material_id')->withPivot('material_id', 'units');
    }
}
