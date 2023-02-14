<?php
use App\Http\Controllers;
use App\Http\Controllers\AdminController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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
Route::group([
        'prefix'=>'admin',
        'middleware' => ['admin:admin'],
        'namespace'=>'App\Http\Controllers',
    ],
    function () {
        Route::get('/login', 'AdminController@loginForm');
        Route::post('/login', 'AdminController@store')->name('admin.login');
    }
);

Route::group([
        'prefix' => LaravelLocalization::setLocale(),
        'namespace'=>'App\Http\Controllers',
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ], function () {
        Route::group(['prefix'=>'admin/dashboard', 'middleware' => ['auth:sanctum,admin', 'verified']], function () {
            Route::get('/', function () {
                return view('dashboard.index');
            })->name('dashboard');
        });
    }
);


