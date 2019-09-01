<?php

namespace App\Models\Bee;

use Illuminate\Database\Eloquent\Model;

class ApplyLock extends Model
{
    protected $table = 'v2_bee_apply_lock';

    protected $primaryKey = 'id';

    protected $fillable = ['lock_date', 'status_id'];
}
