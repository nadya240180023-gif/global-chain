<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Login as first user
$user = \App\Models\User::first();
auth()->login($user);

$request = Illuminate\Http\Request::capture();
$request->server->set('REQUEST_URI', '/comparison');
$response = $kernel->handle($request);

if ($response->getStatusCode() == 500) {
    if (method_exists($response, 'exception') && $response->exception) {
        echo $response->exception->getMessage() . "\n";
        echo $response->exception->getTraceAsString();
    } else {
        echo "500 Error without exception property.\n";
        echo $response->getContent();
    }
} else {
    echo $response->getStatusCode() . "\n";
    echo substr($response->getContent(), 0, 500) . "...\n";
}
