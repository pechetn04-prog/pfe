<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the console kernel to initialize the application state
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$admin = User::where('role', 'Admin')->first();
if (!$admin) {
    die("Error: No Admin user found in the database. Please make sure database seeding is completed.\n");
}

Auth::login($admin);
echo "Logged in as Admin: " . $admin->email . "\n";

try {
    $controller = app(\App\Http\Controllers\AdminDashboardController::class);
    $response = $controller->index();
    echo "Success! Controller returned a response.\n";
    if (method_exists($response, 'render')) {
        echo "Rendering view...\n";
        $html = $response->render();
        echo "View rendered successfully. HTML length: " . strlen($html) . "\n";
    } else {
        echo "Response was not a view object.\n";
    }
} catch (\Throwable $e) {
    echo "\n=== EXCEPTION CAUGHT ===\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
