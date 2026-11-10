<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
]);
$app->make('db');

$store = \App\Models\Store::first();
if ($store) {
    $app->instance('current_store', $store);
    echo "Store: " . $store->name . "\n";
} else {
    echo "No store found!\n";
    exit;
}

$setting = \App\Models\PaymentSetting::getSettings();
echo "qris_mode: " . $setting->qris_mode . "\n";
echo "server_key set: " . (!empty($setting->server_key) ? 'YES - ' . substr($setting->server_key, 0, 15) . '...' : 'NO (fallback ke .env)') . "\n";
echo "qris_acquirer: " . ($setting->qris_acquirer ?? 'null (default gopay)') . "\n";
echo "is_qris_active: " . ($setting->is_qris_active ? 'true' : 'false') . "\n";
echo ".env server_key: " . substr(config('midtrans.server_key'), 0, 15) . "...\n";
echo ".env is_production: " . (config('midtrans.is_production') ? 'true' : 'false') . "\n";
