<?php
$user = \App\Models\User::first();
auth()->login($user);
$request = Illuminate\Http\Request::create('/comparison', 'GET');
$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 500) {
    if ($response->exception) {
        echo $response->exception->getMessage() . "\n";
        echo $response->exception->getTraceAsString();
    }
} else {
    // If it's not 500, we still want to make sure it's 200, maybe it works fine locally?
    echo "It returned " . $response->getStatusCode();
}
