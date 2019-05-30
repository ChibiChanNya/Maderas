<?php   

namespace App\Traits;

use App\LogAction;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


trait ModelEventLogger
{
    public static function bootModelEventLogger()
    {
        foreach (static::getRecordActivityEvents() as $eventName) {

            static::$eventName(function (Model $model) use ($eventName) {

                $log = new LogAction([
                    'user_id'   => Auth::user()->id,
                    'dt_action' => Carbon::now(),
                    'action'     => $eventName
                ]);          

                // dd($model->getOriginal());
                // $user = $model->getOriginal();
                // logs($model)->save($log);
                // dd($model->test);
                // $user->logs()->save($log);
                $model->morphMany('\App\LogAction', 'loggable')->orderBy('dt_action', 'desc')->save($log);
            });
        }    
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


    protected static function logs()
    {
        dd(self);
        return $this->morphMany('\App\LogAction', 'loggable')->orderBy('dt_action', 'desc');
    }

    protected static function test()
    {
        dd($this);
    }


}