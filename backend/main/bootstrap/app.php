<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'Europe/Moscow'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('filesystems');
$app->configure('logging');
$app->configure('auth');
$app->configure('cors');
$app->configure('payments');
$app->configure('api');
$app->configure('services');
$app->configure('swagger-lume');
$app->configure('sms');
$app->configure('sms_code');
$app->configure('gallery');
$app->configure('mail');
$app->configure('utils');
$app->configure('cdn');
//$app->configure('database'); // if disabled $app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \App\Http\Middleware\TrustedProxiesMiddleware::class,
    \Fruitcake\Cors\HandleCors::class,
    \App\Http\Middleware\CorrectInputNull::class,
    //     App\Http\Middleware\ExampleMiddleware::class
]);

$app->routeMiddleware([
    'api_version' => \App\Http\Middleware\APIVersion::class,
    'auth' => App\Http\Middleware\Authenticate::class,
    'verify' => \App\Http\Middleware\Verify::class,
    'member' => \App\Http\Middleware\Member::class,
    'docs' => App\Http\Middleware\SecureApiDocs::class,
]);


/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\RouteServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\PaymentsProvider::class);
$app->register(App\Providers\SmsProvider::class);
$app->register(\Illuminate\Redis\RedisServiceProvider::class);
$app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(\Fruitcake\Cors\CorsServiceProvider::class);
$app->register(\Propaganistas\LaravelPhone\PhoneServiceProvider::class);
$app->register(\Illuminate\Notifications\NotificationServiceProvider::class);

if (!$app->environment(['preprod', 'production'])) {
    $app->register(\SwaggerLume\ServiceProvider::class);
}

if (!class_exists('FormRequestServiceProvider')) {
    class_alias('\Anik\Form\FormRequestServiceProvider', 'FormRequestServiceProvider');
}

$app->register(\Anik\Form\FormRequestServiceProvider::class);

if (!class_exists('Socialite')) {
    class_alias(\Laravel\Socialite\Facades\Socialite::class, 'Socialite');
}

$app->register(\Laravel\Socialite\SocialiteServiceProvider::class);

if (!class_exists('Curl')) {
    class_alias(\Ixudra\Curl\Facades\Curl::class, 'Curl');
}

$app->register(\Ixudra\Curl\CurlServiceProvider::class);

$app->register(Illuminate\Mail\MailServiceProvider::class);

if (!class_exists('Config')) {
    class_alias(\Illuminate\Support\Facades\Config::class, 'Config');
}

if ($app->environment() == 'local') {
    $app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
}
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
