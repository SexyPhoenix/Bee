<?php

namespace App\Http\Controllers\Bee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bee\ApplyImportService;
use App\Http\Response\Traits\ResponseTrait;

class ApplyImportController extends Controller
{
    use ResponseTrait;
    
    public function __construct(ApplyImportService $import) 
    {
    	$this->import     = $import;
    }

    public function import(Request $request)
    {
    	return $this->import->import($request);
    }

    public function storeImport(Request $request)
    {
        $result = $this->import->saveImport($request->import);

        if(true === $result) {

            return $this->withCreated(['message' => trans('bee.import.success')]);
        }

        return $this->withNotImplemented($result);
    }
}
