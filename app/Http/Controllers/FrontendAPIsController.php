<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\User;
use App\Category as Cat;
use Auth;
use Illuminate\Support\Facades\Hash;

class FrontendAPIsController extends Controller
{
    //

    public function getProducts(){
        $pro = Product::all();
        if($pro->count() > 0){
            return response()->json([
               'isError' => false,
               'isFound' => true,
               'proudcts' => $pro,
               'message' => 'Loading'
            ]);
        }else {
            return response()->json([
                'isError' => false,
                'isFound' => false,
                'message' => 'No products found'
             ]);
        }
    }

    public function product($id){
        if(!is_numeric($id) || empty($id)){
            return response()->json([
                'isError' => true,
                'isFound' => false,
                'message' => 'Product Id must be provided.'
             ]);
        } else {
        $pro = Product::where(['product_id' => $id]);
        if($pro->count() > 0){
            return response()->json([
               'isError' => false,
               'isFound' => true,
               'product' => $pro->first(),
               'message' => 'Loading'
            ]);
        }else {
            return response()->json([
                'isError' => false,
                'isFound' => false,
                'message' => 'No products found'
             ]);
        }
    }
    }

    public function getcats(){
        $cat = Cat::all();
        if($cat->count() > 0){
            return response()->json([
               'isError' => false,
               'isFound' => true,
               'categories' => $cat,
               'message' => 'Loading'
            ]);
        }else {
            return response()->json([
                'isError' => false,
                'isFound' => false,
                'message' => 'Categories not found'
             ]);
        }
    }

    public function getcatproducts($id){
        if(!is_numeric($id) || empty($id)){
            return response()->json([
                'isError' => true,
                'isFound' => false,
                'message' => 'Category must be provided.'
             ]);
        } else {
        $pro = Product::where(['cat_id' => $id]);
        if($pro->count() > 0){
            return response()->json([
               'isError' => false,
               'isFound' => true,
               'products' => $pro->get(),
               'message' => 'Loading'
            ]);
        }else {
            return response()->json([
                'isError' => false,
                'isFound' => false,
                'message' => 'No products found'
             ]);
        }
    }
    }

    public function loginPost(Request $req){
        $email = $req->input('email');
        $password = $req->input('password');
        if(empty($password) || empty($email)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => false,
                'message' => 'None of the field can be empty.'
             ]);
        }else {
        if(Auth::attempt(['email' => $email, 'password' => $password])){
            $user = User::where(['email' => $email, 'role' => 0]);
            // $wls = WL::where(['user_id' => $user->id])->count();
            // Session()->put('wl',$wls);
            if($user->count() > 0){
                $user = $user->first();
            $token = Hash::make(time().$user->id.$user->name.time()*4);
            $user->token = $token;
            if($user->save()){
                return response()->json([
                    'isError' => false,
                    'isLoggedIn' => true,
                    'user' => $user,
                    'message' => 'Loginging in....'
                 ]);
            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => false,
                    'message' => 'Error occurred. Please try again.'
                 ]);
            }
        }else {
            return response()->json([
                'isError' => true,
                'isLoggedIn' => false,
                'message' => 'Invalid credentials'
             ]);
        }

        }else {
            return response()->json([
                'isError' => true,
                'isLoggedIn' => false,
                'message' => 'Invalid credentials'
             ]);
        }
    }
    }

}
