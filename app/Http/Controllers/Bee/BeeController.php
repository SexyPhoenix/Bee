<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BeeController extends Controller
{
    public function index() {

    	return view('bee.bee');
    }
}