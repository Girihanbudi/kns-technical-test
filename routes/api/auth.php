<?php

use App\Http\Handlers\AuthHandler;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthHandler::class, 'login']);
