<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserinfoController;
use App\Http\Controllers\PasswordResetController;

use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\CompaniesPasswordResetController;

use App\Http\Controllers\PostController;
use App\Http\Controllers\waterbillsController;
use App\Http\Controllers\securitybillsController;
use App\Http\Controllers\electricbillsController;
use App\Http\Controllers\FormGeneralController;
use App\Http\Controllers\FormEnvironmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Routes
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send-reset-password-email', [PasswordResetController::class, 'send_reset_password_email']);
Route::post('/reset-password/{token}', [PasswordResetController::class, 'reset']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// Companies Routes
Route::post('/company/register', [CompaniesController::class, 'register']);
Route::post('/company/login', [CompaniesController::class, 'login']);
Route::post('/company/send-reset-password-email', [CompaniesPasswordResetController::class, 'send_reset_password_email']);
Route::post('/company/reset-password/{token}', [CompaniesPasswordResetController::class, 'reset']);

//Search Routes
Route::get('/user_infos/search', [UserinfoController::class, 'search']);
Route::get('/form_generals/search', [FormGeneralController::class, 'search']);
Route::get('form_environments/search', [FormEnvironmentController::class, 'search']);
Route::get('electricbills/search', [electricbillsController::class, 'search']);
Route::get('securitybills/search', [securitybillsController::class, 'search']);
Route::get('waterbills/search', [waterbillsController::class, 'search']);

// Protected User, Companies Routes
Route::middleware(['auth:sanctum'])->group(function(){

    // User Routes
    Route::get('/loggeduser', [UserController::class, 'logged_user']);
    Route::post('/changepassword', [UserController::class, 'change_password']);
    Route::resource('user_infos', UserinfoController::class);
    Route::post('user_infos/{user_info}', [UserinfoController::class, 'update'])->name('user_infos.update');
    Route::get('loggedUserInfo', [UserinfoController::class, 'logged_user_info']);
    
    //Post Routes
    Route::resource('posts', PostController::class);
    Route::post('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::post('postlike', [PostController::class, 'storeLike'])->name('posts.like');
    Route::delete('postlike', [PostController::class, 'destroyLike'])->name('posts.likedestroy');
    Route::post('postcomment', [PostController::class, 'storeComment'])->name('posts.comment');
    Route::delete('postcomment', [PostController::class, 'deleteComment']);
    Route::post('postshare', [PostController::class, 'storeShare'])->name('posts.share');
    Route::delete('postshare', [PostController::class, 'deleteShare']);

    // Companies Routes
    Route::post('/company/logout', [CompaniesController::class, 'logout']);
    Route::get('/company/loggedcompany', [CompaniesController::class, 'logged_company']);
    Route::post('/company/changepassword', [CompaniesController::class, 'change_password']);

    //Form General Request
    Route::resource('form_generals', FormGeneralController::class);
    Route::post('form_generals/{form_general}', [FormGeneralController::class, 'update'])->name('form_generals.update');

    //Form Environment Request
    Route::resource('form_environments', FormEnvironmentController::class);
    Route::post('form_environments/{form_environment}', [FormEnvironmentController::class, 'update'])->name('form_environments.update');

    //Electric bills Request
    Route::resource('electricbills', electricbillsController::class);
    Route::post('electricbills/{electricbill}', [electricbillsController::class, 'update'])->name('electricbills.update');

    //Security bills Request
    Route::resource('securitybills', securitybillsController::class);
    Route::post('securitybills/{securitybill}', [securitybillsController::class, 'update'])->name('securitybills.update');

    //Water bills Request
    Route::resource('waterbills', waterbillsController::class);
    Route::post('waterbills/{waterbills}', [waterbillsController::class, 'update'])->name('waterbills.update');

});

