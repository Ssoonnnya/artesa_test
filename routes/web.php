<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/products/categories', [ProductController::class, 'getCategoriesForProducts']);
    Route::get('/products/offers', [ProductController::class, 'getProductsInCategoryAndChildren']);
    Route::get('/products/counts', [ProductController::class, 'getProductCountInCategories']);
    Route::get('/products/unique-counts', [ProductController::class, 'getUniqueProductCountInCategories']);
    Route::get('/products/breadcrumb', [ProductController::class, 'getCategoryBreadcrumb']);


    // Product routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/products/{product}/categories', [ProductController::class, 'addCategory']);
    Route::delete('/products/{product}/categories/{category}', [ProductController::class, 'removeCategory']);
    Route::post('/products/{product}/tags', [ProductController::class, 'addTag']);
    Route::delete('/products/{product}/tags/{tag}', [ProductController::class, 'removeTag']);

    // Category routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Tag routes
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{tag}', [TagController::class, 'show']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);

});

require __DIR__.'/auth.php';
