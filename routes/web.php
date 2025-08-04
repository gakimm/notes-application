<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ShareController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard redirect notes
Route::get('/dashboard', function () {
    return redirect('/notes');
})->middleware(['auth', 'verified'])->name('dashboard');

// Note routes (authorized)
Route::middleware('auth')->group(function () {
    // Profile routes (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notes
    Route::resource('notes', NoteController::class)->parameters([
        'notes' => 'note:slug'
    ]);
    
    // Comment routes
    Route::post('/notes/{note:slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Share routes
    Route::post('/notes/{note:slug}/share', [ShareController::class, 'store'])->name('notes.share');
    Route::delete('/notes/{note:slug}/share/{user}', [ShareController::class, 'destroy'])->name('notes.unshare');
});

// Public note view
Route::get('/public/notes/{note:slug}', [NoteController::class, 'show'])
    ->name('notes.public.show');

require __DIR__.'/auth.php';