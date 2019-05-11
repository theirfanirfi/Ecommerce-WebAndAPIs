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

// Route::get('/', function () {
//     return view('welcome');
// });


/// Frontend Routes
Route::get('/','FrontendController@index')->name('home');
Route::get('category/{id}','FrontendController@productsByCat')->name('category');
Route::get('addtocart/{id}','FrontendController@addtocart')->name('addtocart');
Route::get('catproducts/{id}','FrontendController@productsByCat')->name('catproducts');
Route::get('product/{id}','FrontendController@singleproduct')->name('product');
Route::post('addtocartproduct','FrontendController@addtocartproduct')->name('addtocartproduct');

//with middleware - requires login
Route::group(['prefix' => 'user'], function () {
    Route::get('addtowishlist/{id}','FrontendController@addtowishlist')->name('addtowishlist');
});
