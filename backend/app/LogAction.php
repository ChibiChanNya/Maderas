<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Prettus\Repository\Contracts\Transformable;
// use Prettus\Repository\Traits\TransformableTrait;

class LogAction extends Model
{
    //
    // use TransformableTrait;
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'user_id',
        'action',
        'dt_action'

    ];

    public $timestamps = false;

    protected $dates = [
        'dt_action'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function loggable()
    {
        return $this->morphTo();
    }
}
