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

});



// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
