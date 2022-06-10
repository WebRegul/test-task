<?php

namespace App\Providers;

use App\Registries\Member;
use App\Services\User;
use App\Services\Images\ImageAdapters\AbstractImageAdapter;
use App\Services\Images\ImageAdapters\ImagickImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(User::class, function () {
            return new User();
        });

        $this->app->bind(AbstractImageAdapter::class, ImagickImage::class);

        $this->app->singleton(Member::class, function () {
            return new Member();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('real_date', 'App\Validators\RealDate@validate');
        Validator::extend('normal_password', 'App\Validators\NormalPassword@validate');
        Validator::extend('normal_slug', 'App\Validators\NormalSlug@validate');
        Validator::extend('real_images', 'App\Validators\RealImages@validate');

        Collection::macro('recursive', function () {
            return $this->map(function ($value) {
                if (is_array($value) || is_object($value)) {
                    return collect($value)->recursive();
                }

                return $value;
            });
        });
    }
}
