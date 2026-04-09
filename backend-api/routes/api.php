<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PurchaseController;
use App\Http\Controllers\Api\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/esdeveniments', [EventController::class, 'index']);
Route::get('/esdeveniments/{id}', [EventController::class, 'show']);

Route::post('/purchase', [PurchaseController::class, 'store']);

Route::get('/admin/stats', [AdminController::class, 'stats']);
Route::post('/admin/events', [AdminController::class, 'createEvent']);
