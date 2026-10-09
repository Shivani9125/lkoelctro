<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/directory', function () {
    return view('welcome');
});

Route::get('/app', function () {
    if (file_exists(public_path('app/index.html'))) {
        return response()->file(public_path('app/index.html'));
    }
    return view('welcome');
});

Route::get('/app/{any}', function () {
    if (file_exists(public_path('app/index.html'))) {
        return response()->file(public_path('app/index.html'));
    }
    return view('welcome');
})->where('any', '.*');
