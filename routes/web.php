<?php

use App\Http\Controllers\CheckController;
use App\Http\Controllers\CobaController;
use App\Http\Controllers\FileUpload;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CheckController::class, 'welcome']);
Route::post('/', [CheckController::class, 'checkfile']);
Route::get('/result', [ResultController::class, 'hasil']);

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'ProfileController@index')->name('profile');
Route::put('/profile', 'ProfileController@update')->name('profile.update');
Route::post('file-delete', [FileUpload::class, 'deleteFile'])->name('file.delete');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/file-upload', [FileUpload::class, 'createForm']);
Route::post('/file-upload', [FileUpload::class, 'fileUpload'])->name('fileUpload');
Route::get('test', fn () => phpinfo());
