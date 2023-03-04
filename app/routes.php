<?php declare(strict_types = 1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use Blog\Foundation\Router\Route;

return [
    
    'register.form' => Route::get('/inscription', [AuthController::class, 'registerForm']),
    'login.form' => Route::get('/connection', [AuthController::class, 'loginForm']),
    'login.request' => Route::post('/connection', [AuthController::class, 'login']),
    'register.request' => Route::post('/inscription', [AuthController::class, 'register']),
    'logout' => Route::post('/deconnection', [AuthController::class, 'logout']),
    
    'home' => Route::get('/account', [HomeController::class, 'index']),
    'home.updateName' => Route::patch('/account', [HomeController::class, 'updateName']),
    'home.updateEmail' => Route::patch('/account/email', [HomeController::class, 'updateEmail']),
    'home.updatePassword' => Route::patch('/account/password', [HomeController::class, 'updatePassword']),
    
    'index' => Route::get('/', [PostController::class, 'index']),
    'posts.create' => Route::get('/posts/create', [PostController::class, 'createPost']),   
    'posts.store' => Route::post('/posts/create', [PostController::class, 'storePost']),   
    'posts.show' => Route::get('/posts/{slug}', [PostController::class, 'showPost']),
    'posts.comment' => Route::post('/posts/{slug}', [PostController::class, 'commentPost']),
    'posts.delete' => Route::delete('/posts/{slug}', [PostController::class, 'deletePost']),
    'posts.edit' => Route::get('/posts/{slug}/edit', [PostController::class, 'editPost']),
    'posts.update' => Route::patch('/posts/{slug}/edit', [PostController::class, 'updatePost']),
];