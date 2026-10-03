<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/app', function () {
    return response()->file(public_path('app/index.html'));
});

Route::get('/app/{any}', function () {
    return response()->file(public_path('app/index.html'));
})->where('any', '.*');
