<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

use fk\helpers\{
    debug\Capture, debug\FileWriter
};

require __DIR__ . '/../bootstrap/autoload.php';

$writer = new FileWriter(__DIR__ . '/../storage/logs/request_capture.log');
$capture = new Capture($writer, true);
Capture::softAdd(['route' => $_SERVER['REQUEST_URI'] ?? '']);
$capture->capture();

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.d
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

/**
 * @var \App\Http\Kernel $kernel
 */
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = \fk\utility\Http\Request::capture();
Capture::add(['form-data' => $request->input()]);

$response = $kernel->handle($request);

if ($request->expectsJson()) {
    $response->header('Content-Type', 'application/json;charset=utf8');
}

$response->send();

$kernel->terminate($request, $response);

$capture->add(['response' => $response->getContent()]);
