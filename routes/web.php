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

Route::get('login','AuthController@login')->name('login');
Route::get('logout','AuthController@logout')->name('logout');
Route::post('loginPost','AuthController@loginPost')->name('loginPost');
Route::post('register','AuthController@register')->name('register');

/// Frontend Routes
Route::get('/','FrontendController@index')->name('home');
Route::get('category/{id}','FrontendController@productsByCat')->name('category');
Route::get('addtocart/{id}','FrontendController@addtocart')->name('addtocart');
Route::get('catproducts/{id}','FrontendController@productsByCat')->name('catproducts');
Route::get('product/{id}','FrontendController@singleproduct')->name('product');
Route::post('addtocartproduct','FrontendController@addtocartproduct')->name('addtocartproduct');
Route::get('cart','FrontendController@cart')->name('cart');
Route::get('removeproductcart/{id}','FrontendController@removeProductFromCart')->name('removeproductcart');


Route::get('search','FrontendController@searchProducts')->name('search');
Route::get('pay','PaymentController@pay');
Route::get('paid','PaymentController@getPaymentStatus')->name('paid ');

//with middleware - requires login
Route::group(['prefix' => 'user','middleware' => 'AuthWare'], function () {
    Route::get('addtowishlist/{id}','FrontendController@addtowishlist')->name('addtowishlist');
    Route::get('wishlist','FrontendController@wishlist')->name('wishlist');
    Route::get('removefromwishlist/{id}','FrontendController@removefromwishlist')->name('removefromwishlist');
    Route::get('checkout','FrontendController@checkout')->name('checkout');
    Route::get('placeorder','FrontendController@placeorder')->name('placeorder');
});
