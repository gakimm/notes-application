<?php

use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

// routes/api.php
Route::get('/users/search', [ShareController::class, 'search']);