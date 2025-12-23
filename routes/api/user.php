<?php

use App\Http\Handlers\UserHandler;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.token', 'auth.role:administrator,manager'])->post('/users', [UserHandler::class, 'store']);
Route::middleware('auth.token')->put('/users/{user}', [UserHandler::class, 'update']);
Route::middleware('auth.token')->get('/users', [UserHandler::class, 'index']);
