<?php

use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('media', MediaController::class)->parameters(['media' => 'media']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{user}/collection', [CollectionController::class, 'index']);
Route::post('users/{user}/collection/{media}', [CollectionController::class, 'store']);
Route::delete('users/{user}/collection/{media}', [CollectionController::class, 'destroy']);
