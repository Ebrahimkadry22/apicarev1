<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboard\AdminNotificationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientServicesController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserReviewController;
use App\Http\Requests\Post\PostStatusRequest;
use Illuminate\Support\Facades\Route;



Route::prefix('auth/')->group(function () {


    Route::controller(AdminController::class)->prefix('admin')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register',  'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::get('/user-profile',  'userProfile');
    });

    Route::controller(UserController::class)->prefix('user')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register',  'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
    });
    Route::controller(ClientController::class)->prefix('client')->group(function () {
        Route::post('/login', 'login');
        Route::post('/register',  'register');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::get('/user-profile',  'userProfile');
    });
});

Route::controller(PostController::class)->prefix('post')->group(
    function () {
        Route::post('/add', 'store')->middleware(['auth:user']);
        Route::get('/show', 'index')->middleware(['auth:admin']);
        Route::post('/delete/{id}', 'deletepost')->middleware(['auth:user']);
        Route::get('/{id}', 'showpost');
        Route::get('/approved ', 'approved');
    }
);

Route::prefix('admin/')->group(function () {
    Route::controller(PostStatusRequest::class)->prefix('post')->group(function () {
        Route::post('/status', 'changeStatus');
    });
});

Route::controller(ClientServicesController::class)->prefix('user/')->group(function () {
    Route::get('pending/orders', 'userOder')->middleware('auth:user');
    Route::put('update/order', 'updateOrder')->middleware('auth:user');
});


Route::prefix('user')->group(function () {
    Route::post('/review', [UserReviewController::class, 'store'])->middleware('auth:client');
    Route::get('/review/post/{id}', [UserReviewController::class, 'postRate']);
    Route::get('/Profile', [UserProfileController::class, 'userProfile']);
    Route::post('/Profile/edit', [UserProfileController::class, 'edit']);
    Route::post('/Profile/update', [UserProfileController::class, 'update']);
    Route::post('/Profile/posts/delete', [UserProfileController::class, 'delete']);
});




Route::controller(AdminNotificationController::class)
    ->middleware(['auth:admin'])
    ->prefix('admin/notifications')
    ->group(
        function () {
            Route::get('/all', 'index');
            Route::get('/unread', 'unread');
            Route::post('/markReadAll', 'markReadAll');
            Route::delete('/deleteAll', 'deleteAll');
            Route::delete('/delete/{id}', 'delete');
        }
    );

Route::prefix('client')->group(function () {
    Route::controller(ClientServicesController::class)->prefix('/order')->group(function () {
        Route::post('/request', 'addoder')->middleware('auth:client');
    });
});
