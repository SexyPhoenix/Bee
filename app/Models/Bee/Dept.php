<?php

namespace App\Models\Bee;

use Illuminate\Database\Eloquent\Model;

class Dept extends Model
{
    protected $table = 'v2_dept';

    protected $primaryKey = 'dept_id';

    public function isRoot()
    {
        return in_array($this->up_dept, [0]);
    }

    public function scopeOfCorporation($query, $corporationId)
    {
        return '*' == $corporationId ? $query : $query->where('tenant_id', $corporationId);
    }
}
