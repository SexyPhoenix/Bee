<?php

namespace App\Providers;

use App\Services\Bee\ApplyService;
use App\Services\Bee\ApplyMetaService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Bee\ApplyRepository;
use App\Validator\Bee\LockValidator;
use App\Validator\Bee\ClashValidator;
use App\Validator\Bee\YearValidator;
use App\Validator\Bee\RestValidator;
//use App\Decorator\Bee\MappingTypeDecorator;
use App\Decorator\Bee\WorkDecorator;
use App\Decorator\Bee\YearDecorator;
use App\Decorator\Bee\RestDecorator;
use App\Decorator\Bee\BusinessDecorator;
//use App\Decorator\Bee\BirthDecorator;
use Illuminate\Support\Facades\DB;

class BeeServiceProvider extends ServiceProvider
{
    private $applyService;
	
	private $applyMetaService;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //     DB::listen(function ($query) {
        //         $tmp = str_replace('?', '"'.'%s'.'"', $query->sql);
        //         $qBindings = [];
        //         foreach ($query->bindings as $key => $value) {
        //             if (is_numeric($key)) {
        //                 $qBindings[] = $value;
        //             } else {
        //                 $tmp = str_replace(':'.$key, '"'.$value.'"', $tmp);
        //             }
        //         }
        //         $tmp = vsprintf($tmp, $qBindings);
        //         $tmp = str_replace("\\", "", $tmp);
        //         info(' execution time: '.$query->time.'ms; '.$tmp."\n\n\t");

        //     }
        // );
       // DB::listen(function($query) {
       //     info($query->sql, ['#########################################################']);
       //     info($query->bindings);
       //     info($query->time);
       // });
       		
		//register validator
        $this->applyService = new ApplyService(

            $this->app->make(ApplyRepository::class),
            $this->getApplyValidator()      
        );

		//register decorator
        $this->applyMetaService = new ApplyMetaService(

            $this->app->make(ApplyRepository::class),
            $this->getApplyDecorator()            
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ApplyService::class, function($app) {

            return $this->applyService;
        });
		
        $this->app->bind(ApplyMetaService::class, function($app) {

            return $this->applyMetaService;
        });
    }
		
	public function getApplyValidator()
	{
		return [
			$this->app->make(LockValidator::class),
			$this->app->make(ClashValidator::class),
			$this->app->make(YearValidator::class),
			$this->app->make(RestValidator::class)		
		];
	}
	
	public function getApplyDecorator()
	{
		return [
                $this->app->make(WorkDecorator::class),
				//$this->app->make(MappingTypeDecorator::class),				
				$this->app->make(YearDecorator::class),
				$this->app->make(RestDecorator::class),
				$this->app->make(BusinessDecorator::class),
				//$this->app->make(BirthDecorator::class),	
		];		
	}
}
