<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'provider', 'recent_price', 'available_stock', 'pending_stock'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
