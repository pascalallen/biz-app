<?php

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/connect-quickbooks', 'QuickbooksController@connect');

Route::group(['prefix' => 'api', 'middleware' => 'auth'], function() {
    Route::resource('accounts', 'API\AccountController');
    Route::resource('invoices', 'API\InvoiceController');
    Route::resource('customers', 'API\CustomerController');
    Route::resource('companies', 'API\CompanyController');
});
