<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'business_name', 'description', 'logo', 'money_debt', 'rfc', 'clabe', 'bank'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function orders(){
        return $this->hasMany('App\OrderToProvider', 'provider_id');
        //return $this->belongsToMany('App\ClientOrder', 'client_orders_details', 'product_id', 'order_id')->withPivot('product_id', 'units');
    }
}
