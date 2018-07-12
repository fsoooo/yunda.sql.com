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

Route::get('/', 'IndexController@index');
Route::get('/person', 'PersonController@index');
Route::get('/warranty', 'WarrantyController@warrantyIndex');
Route::get('/warranty_person', 'WarrantyController@warrantyPersonIndex');
Route::get('/bank', 'BankController@index');
