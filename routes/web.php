<?php

use Illuminate\Support\Facades\Route;


Route::get('/menu', function () {
    return view('menu');
});

Route::get('/modal', function () {
    return view('modal');
});