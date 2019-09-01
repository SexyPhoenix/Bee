<?php

namespace App\Models\Bee;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function validateForPassportPasswordGrant($password) 
    {
    	return true;
    }
}
