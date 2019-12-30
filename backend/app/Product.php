<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku','name', 'price', 'description', 'box_volume', 'materials_volume', 
        'width','height','length','stock','product_service_code','unit_code',
        'unit_description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function requested(){
        return $this->hasMany('App\ClientOrderDetails', 'product_id');
        //return $this->belongsToMany('App\ClientOrder', 'client_orders_details', 'product_id', 'order_id')->withPivot('product_id', 'units');
    }

    public function client_orders(){
        //return $this->hasMany('App\ClientOrderDetails', 'product_id');
        return $this->belongsToMany('App\ClientOrder', 'client_orders_details', 'product_id', 'order_id');
    }
}
