<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\User;
use App\WhishList;
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

    public function getLoggedInUserProducts(Request $req){
        $token = $req->input('token');
        $user= User::getUserByToken($token);
        if($user){
            $pro = Product::getLoggedInUserProducts($user->id);
            if($pro->count() > 0){
                return response()->json([
                   'isError' => false,
                   'isFound' => true,
                   'proudcts' => $pro->get(),
                   'message' => 'Loading'
                ]);
            }else {
                return response()->json([
                    'isError' => false,
                    'isFound' => false,
                    'message' => 'No products found'
                 ]);
            }
        }else {
            return response()->json([
                'isError' => true,
                'isFound' => false,
                'message' => 'You are not logged in to perform this action.'
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

    public function updateProfileDetails(Request $req){
        $token = $req->input('token');
        $name = $req->input('name');
        $email = $req->input('email');

        if(empty($name) || empty($email) || empty($token)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'None of the field can be empty.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                $user->name = $name;

                if($user->email !== $email){
                    $checkEmail = User::where(['email' => $email])->count();
                    if($checkEmail > 0){
                        return response()->json([
                            'isError' => true,
                            'isLoggedIn' => true,
                            'message' => 'The entered email cannot be assigned to you. It is already in use.'
                         ]);
                    }else {
                        $user->email = $email;
                    }
                }


                if($user->save()){
                    return response()->json([
                        'isError' => false,
                        'isLoggedIn' => true,
                        'isUpdated' => true,
                        'user'=> $user,
                        'message' => 'Details updated.'
                     ]);
                }else {
                    return response()->json([
                        'isError' => true,
                        'isLoggedIn' => true,
                        'message' => 'Error occurred in updating profile details. Please try again.'
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


    public function getuser(Request $req){
        $token = $req->input('token');

        if($token == null || empty($token)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'User must be provided.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                return response()->json([
                    'isError' => false,
                    'isLoggedIn' => true,
                    'isFound' => true,
                    'user' => $user,
                    'message' => 'Found'
                 ]);
            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => true,
                    'isFound' => false,
                    'message' => 'No user Found'
                 ]);
            }
        }
    }

    public function changepass(Request $req){
        $token = $req->input('token');
        $cp = $req->input('cp');
        $np = $req->input('np');

        if($token == null || empty($token) || empty($cp) || empty($np)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'None of the fields can be empty.'
             ]);
        }else if(strlen($np) < 6) {
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'Password length must be at least six characters.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                if(Hash::check($cp, $user->password)){
                    $user->password = Hash::make($np);
                    if($user->save()){
                        return response()->json([
                            'isError' => false,
                            'isLoggedIn' => true,
                            'isChanged' => true,
                            'message' => 'Password Updated.'
                         ]);
                    }else {
                        return response()->json([
                            'isError' => true,
                            'isLoggedIn' => true,
                            'message' => 'Error occurred in updating the password. Please try again.'
                         ]);
                    }
                }else {
                    return response()->json([
                        'isError' => true,
                        'isLoggedIn' => true,
                        'message' => 'Invalid current password'
                     ]);
                }

            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => true,
                    'message' => 'No user Found'
                 ]);
            }
        }
    }


    public function getWishList(Request $req){
        $token = $req->input('token');

        if($token == null || empty($token)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'User must be provided.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                $wl = WhishList::getWishListProducts($user->id);
                if($wl->count() > 0){
                    return response()->json([
                        'isError' => false,
                        'isLoggedIn' => true,
                        'isFound' => true,
                        'products' => $wl->get(),
                        'message' => 'loading...'
                     ]);
                }else {
                    return response()->json([
                        'isError' => false,
                        'isLoggedIn' => true,
                        'isFound' => false,
                        'message' => 'Your wishlist is empty.'
                     ]);
                }
            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => true,
                    'message' => 'Invalid User.'
                 ]);
            }
        }
    }


    public function addToWishList(Request $req){
        $token = $req->input('token');
        $product_id = $req->input('pid');

        if($token == null || empty($token) || $product_id == null || empty($product_id) || !is_numeric($product_id)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'User must be provided.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                $wlCheck = WhishList::where(['product_id' => $product_id,'user_id' => $user->id]);

                if($wlCheck->count() > 0){
                    if($wlCheck->first()->delete()){
                        $wl = WhishList::getWishListProducts($user->id)->get();
                        return response()->json([
                            'isError' => false,
                            'isLoggedIn' => true,
                            'isDeleted' => true,
                            'products' => $wl,
                            'message' => 'The product is removed from your wishlist.'
                         ]);
                    }else {
                        return response()->json([
                            'isError' => true,
                            'isLoggedIn' => true,
                            'isDeleted' => false,
                            'message' => 'Error occurred in removing the product from your wishlist. Please try again.'
                         ]);
                    }

                }else {
                $wl = new WhishList();
                $wl->user_id = $user->id;
                $wl->product_id = $product_id;
                if($wl->save()){
                    $wl = WhishList::getWishListProducts($user->id)->get();
                    return response()->json([

                        'isError' => false,
                        'isLoggedIn' => true,
                        'isAdded' => true,
                        'products' => $wl,
                        'message' => 'Product Added to your wish list.'
                     ]);
                }else {
                    return response()->json([
                        'isError' => false,
                        'isLoggedIn' => true,
                        'isAdded' => false,
                        'message' => 'Error occurred in Adding the product to wishlist.'
                     ]);
                }
            }
            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => true,
                    'message' => 'Invalid User.'
                 ]);
            }
        }
    }

    public function returnCart(Request $req){
        $cart = $req->input('ct');
        $cart = base64_decode($cart);
        $cart = json_decode($cart);
        $products = Product::whereIn('product_id',$cart);
        if($products->count() > 0){
            return response()->json([
               'isError' => false,
               'isFound' => true,
               'proudcts' => $products->get(),
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


    public function addToWishListProductsTab(Request $req){
        $token = $req->input('token');
        $product_id = $req->input('pid');

        if($token == null || empty($token) || $product_id == null || empty($product_id) || !is_numeric($product_id)){
            return response()->json([
                'isError' => true,
                'isLoggedIn' => true,
                'message' => 'User must be provided.'
             ]);
        }else {
            $user= User::getUserByToken($token);
            if($user){
                $wlCheck = WhishList::where(['product_id' => $product_id,'user_id' => $user->id]);

                if($wlCheck->count() > 0){
                    if($wlCheck->first()->delete()){
                        $products = Product::getLoggedInUserProducts($user->id)->get();
                        return response()->json([
                            'isError' => false,
                            'isLoggedIn' => true,
                            'isDeleted' => true,
                            'products' => $products,
                            'message' => 'The product is removed from your wishlist.'
                         ]);
                    }else {
                        return response()->json([
                            'isError' => true,
                            'isLoggedIn' => true,
                            'isDeleted' => false,
                            'message' => 'Error occurred in removing the product from your wishlist. Please try again.'
                         ]);
                    }

                }else {
                $wl = new WhishList();
                $wl->user_id = $user->id;
                $wl->product_id = $product_id;
                if($wl->save()){
                    $products = Product::getLoggedInUserProducts($user->id)->get();

                    return response()->json([

                        'isError' => false,
                        'isLoggedIn' => true,
                        'isAdded' => true,
                        'products' => $products,
                        'message' => 'Product Added to your wish list.'
                     ]);
                }else {
                    return response()->json([
                        'isError' => false,
                        'isLoggedIn' => true,
                        'isAdded' => false,
                        'message' => 'Error occurred in Adding the product to wishlist.'
                     ]);
                }
            }
            }else {
                return response()->json([
                    'isError' => true,
                    'isLoggedIn' => true,
                    'message' => 'Invalid User.'
                 ]);
            }
        }
    }

}
