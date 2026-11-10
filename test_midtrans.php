<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY');
\Midtrans\Config::$isProduction = false;
try {
    $token = \Midtrans\Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => uniqid(),
            'gross_amount' => 10000
        ]
    ]);
    echo "TOKEN: " . $token . "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
