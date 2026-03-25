<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/esdeveniments', [EventController::class, 'index']);
Route::get('/esdeveniments/{id}', [EventController::class, 'show']);
