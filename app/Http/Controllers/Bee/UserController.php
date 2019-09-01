<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Bee\UserRepository;
use App\Http\Response\Traits\ResponseTrait;

class UserController extends Controller
{
    use ResponseTrait;

    public $user;
    
    public function __construct(UserRepository $user) 
    {
        $this->user = $user;
    }

    public function login(Request $request)
    {   
        $data = $this->user->login($request);

        if(is_null($data['user'])) return $this->withNotImplemented(trans('bee.no.email'));

        if(!$data['token']) return $this->withNotImplemented(trans('bee.validate.error'));

        return $this->responseJson($data);
    }

    public function getUser()
    {		
    	return $this->user->getUser();
    }

    public function getUserPermission()
    {   
        return $this->user->getUserPermission(auth('api')->user());
    }

    public function getRegion(Request $request)
    {       
        return $this->user->getRegion(auth('api')->user());
    }

    public function filterUser(Request $request)
    {	
    	return $this->user->filterUser($request);
    }

    public function filterDept()
    {   
        return $this->user->filterDept(auth('api')->user());
    }

    public function fileUpload(Request $request)
    {   
        return $this->user->fileUpload($request);
    }
}
