<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/frontend/index.html');
});

Route::get('/dashboard', function () {
    return redirect('/frontend/dashboard.html');
});