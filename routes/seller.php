<?php

use Illuminate\Support\Facades\Route;

Route::get('/seller', function () {
    return view('test.seller');
});