<?php

use Illuminate\Http\Request;

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

// Route::get('/',function(){
// echo "working";
// })->middleware('APIWare');

//routes for admin login

Route::post('nigol','UserControllerAPI@login');

Route::group(['prefix' => 'auth','middleware' => 'APIAdminWare'],function(){
//Route::post('login')
Route::post('/addcat','AdminControllerAPI@addCategory');

Route::get('/getCategories','AdminControllerAPI@getCategories');
Route::get('/getproducts','AdminControllerAPI@getProducts');
Route::get('/getproduct','AdminControllerAPI@getProduct');
Route::get('/deleteproduct','AdminControllerAPI@deleteproduct');

Route::post('addproduct','AdminControllerAPI@addproduct');
Route::post('updateproduct','AdminControllerAPI@updateProduct');

Route::get('getneworders', 'AdminControllerAPI@getNewOrders');
Route::get('getoldorders', 'AdminControllerAPI@getOlderOrders');
Route::get('getcheckout','AdminControllerAPI@getcheckout');
Route::get('getorderproducts','AdminControllerAPI@getOrderProducts');
Route::get('shiporder','AdminControllerAPI@shipOrder');
Route::get('getmem','AdminControllerAPI@getmembers');
Route::get('getmemcheckouts','AdminControllerAPI@getMemberCheckouts');

//profile
Route::get('getprofile','AdminControllerAPI@getprofile');
Route::post('updateprofile','AdminControllerAPI@updateProfile');


});


//frontend app routes

Route::get('login','FrontendAPIsController@loginPost');


Route::get('getproducts','FrontendAPIsController@getProducts');
Route::get('product/{id}','FrontendAPIsController@product');
Route::get('getcats','FrontendAPIsController@getcats');
Route::get('getcatproducts/{id}','FrontendAPIsController@getcatproducts');

Route::get('s','AdminControllerAPI@sendEmail');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//user app routes - login required
Route::group(['prefix' => 'user','middleware' => 'UserAPIWare'], function () {
Route::get('updateprofile','FrontendAPIsController@updateProfileDetails');
Route::get('getproducts','FrontendAPIsController@getLoggedInUserProducts');

Route::get('getuser','FrontendAPIsController@getuser');
Route::get('changepass','FrontendAPIsController@changepass');

Route::get('addtowishlist','FrontendAPIsController@addToWishList');

Route::get('addtowishlisttab','FrontendAPIsController@addToWishListProductsTab');

Route::get('wishlist','FrontendAPIsController@getWishList');

Route::post('cart','FrontendAPIsController@returnCart');
Route::get('paycart/{id}','PaymentController@paycart');
});
