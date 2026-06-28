<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/payments/qris/callback', [\App\Http\Controllers\QrisWebhookController::class, 'handle']);
