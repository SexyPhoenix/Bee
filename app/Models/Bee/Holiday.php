<?php

namespace App\Models\Bee;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'v2_holiday';

    protected $primaryKey = 'id';

    public static function getWorkDays($dates) 
    {     

        $holiday = self::whereBetween('date', [current($dates), end($dates)])
                    ->get()
                    ->pluck('date')
                    ->all();
                    
        return array_diff($dates, $holiday);
    }
}
