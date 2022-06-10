<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

/**
 * Class RouteServiceProvider
 * @package App\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $apiNamespace = 'App\Http\Controllers';

    /**
     *
     */
    public function register()
    {
        Route::group([
            'middleware' => ['api_version:v1'],
            'namespace' => "{$this->apiNamespace}\V1",
            'prefix' => 'v1',
        ], function ($router) {
            require base_path('routes/v1.php');
        });
    }
}
