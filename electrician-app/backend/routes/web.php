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

Route::get('/assets/{file}', function ($file) {
    $path = public_path('assets/' . $file);
    if (!file_exists($path)) {
        $path = public_path('app/assets/' . $file);
    }
    if (file_exists($path)) {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimes = [
            'js' => 'application/javascript',
            'css' => 'text/css',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'json' => 'application/json',
        ];
        return response()->file($path, ['Content-Type' => $mimes[$ext] ?? 'application/octet-stream']);
    }
    abort(404);
})->where('file', '.*');

