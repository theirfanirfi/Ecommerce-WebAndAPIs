<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product as Pd;
use App\Category as Cat;
use App\RecentlyViewed as RV;
use App\WhishList as WL;
use Auth;
class FrontendController extends Controller
{
    //

    public function index(){
        //Session()->forget(['cart','total_cart_cost']);
        $rv = "";
        if(Auth::check()){
            $rv = new RV();
            $user = Auth::user();
            $rv = $rv->getRV($user->id);
            // if($rv->count() > 0){
            //     $rv = $rv->get();
            // }
        }

        $cats = Cat::all();
        $products = Pd::getProducts()->inRandomOrder()->limit(8)->get();
        $rpro = Pd::getProducts()->latest()->orderBy('product_id','DESC')->limit(8)->get();
        return view('Frontend.home',['cats' => $cats, 'products' => $products, 'recentproducts' => $rpro,'rvs' => $rv]);
    }

    public function addtocart($id){
        $product = Pd::where(['product_id' => $id]);
        if($product->count() > 0){
            if(Session()->has('cart')){
                $product = $product->first();
                $arr = Session()->get('cart');
                $price = Session()->get('total_cart_cost');
                 $pro = $arr['pro'];
                $num = $arr['num'];


                if(!array_key_exists($id, $pro)){
                    $pro[$product->product_id] = array('product_id' => $product->product_id,'product_name' => $product->product_name, 'quantity' => 1);
                    $arr['pro'] = $pro;
                    $num = $num + 1;
                    $arr['num'] = $num;
                    $price = $price + $product->product_price * 1;
                    $arr['pro'] = $pro;
                    Session()->put('cart',$arr);
                    Session()->put('total_cart_cost',$price);
                    return redirect()->back()->with('success','Product Added to cart.');
                }else {
                    return redirect('/product/'.$id)->with('info','Product is already in the cart.');
                }

            }else {
                $product = $product->first();
                $arr = array();
                $pro = array();
                $pro[$product->product_id] = array('product_id' => $product->product_id,'product_name' => $product->product_name, 'quantity' => 1);
               $num = 1;
               $arr['num'] = $num;
               $arr['pro'] = $pro;

                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$product->product_price);
                return redirect()->back()->with('success','Product Added to cart.');
            }
        }else {
            return redirect('/')->with('error','No such product exists in the store.');
        }
    }

    public function productsByCat($id){
        $cat = Cat::where(['cat_id' => $id]);
        if($cat->count() > 0){
            $products = Pd::where(['cat_id' => $id])->get();
            $cats = Cat::all();
            return view('Frontend.productbycat',['products' => $products,'cats' => $cats]);
        }else {
            return redirect()->back()->with('error','No such category exists in the system.');
        }
    }

    public function singleproduct($id){
        $product = Pd::where(['product_id' => $id]);
        if($product->count() > 0){
            $product = $product->first();
            $cats = Cat::all();
            return view('Frontend.singleproduct',['product' => $product,'cats' => $cats]);
        }else {
            return redirect()->back()->with('error','No such Product exists in the system.');
        }
    }

    public function addtocartproduct(Request $req){
        $id = $req->input('product_id');
        $quantity = $req->input('quantity');
        $product = Pd::where(['product_id' => $id]);
        if($quantity < 0 || !is_numeric($quantity)){
            return redirect()->back()->with('error','Invalid number of product inputs.');
        }else if($product->count() > 0){
            if(Session()->has('cart')){
                $product = $product->first();
                $arr = Session()->get('cart');
               $pro = $arr['pro'];
               $num = $arr['num'];
               $price = Session()->get('total_cart_cost');
               if(!array_key_exists($id, $pro)){
               $pro[$product->product_id] = array('product_id' => $product->product_id,'product_name' => $product->product_name, 'quantity' => $quantity);
               $arr['pro'] = $pro;
               $num = $num + $quantity;
               $arr['num'] = $num;
               $price = $price + $product->product_price * $quantity;
               }else {
                   $modifyProductInTheArray = $pro[$id];
                   $num = $arr['num'];
                   $num = $num - $modifyProductInTheArray['quantity'];
                   $price = $price - $product->product_price * $modifyProductInTheArray['quantity'];
                   $modifyProductInTheArray['quantity'] = $quantity;
                   unset($pro[$id]);
                   $pro[$id] = $modifyProductInTheArray;
                   $price = $price + $product->product_price * $quantity;
                   $arr['pro'] = $pro;
                   $num = $num + $quantity;
                   $arr['num'] = $num;


               }


                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$price);
                return redirect()->back()->with('success','Product Added to cart.');
            }else {
                $product = $product->first();
                $arr = array();
                $pro = array();
                $arr['num'] = $quantity;
              $pro[$product->product_id] = array('product_id' => $product->product_id,'product_name' => $product->product_name, 'quantity' => $quantity);
                $arr['pro'] = $pro;
                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$product->product_price * $quantity);
                return redirect()->back()->with('success','Product Added to cart.');
            }
        }else {
            return redirect('/')->with('error','No such product exists in the store.');
        }
    }

    public function addtowishlist($id){
        if($id < 0 || !is_numeric($id)){
            return redirect()->back()->with('error','Invalid product inputs.');
        }else {
            $product = Pd::where(['product_id' => $id]);
            if($product->count() > 0){
                $user = Auth::user();
                $wl = new WL();
                $wl->product_id = $id;
                $wl->user_id = $user->id;
                if($wl->save()){
                    $wls = WL::where(['user_id' => $user->id])->count();
                    Session()->put('wl',$wls);
                    return redirect()->back()->with('success','Product Added to your WishList.');
                }else {
            return redirect()->back()->with('error','Error occurred in adding the product to your WishList. Try Again.');
                }
            }else {
                return redirect()->back()->with('error','No such product exists in the store.');
            }
        }
    }

    public function wishlist(){
        $user = Auth::user();
        $wls = WL::getWishListProducts($user->id);
        $cats = Cat::all();
        return view('Frontend.wishlist',['user' => $user,'wl' => $wls,'cats' => $cats]);
    }

    public function removefromwishlist($id){
        $user = Auth::user();
        $wl = WL::where(['user_id' => $user->id,'id' => $id]);
        if($wl->count() > 0){
            if($wl->first()->delete()){
                $wls = WL::where(['user_id' => $user->id])->count();
                Session()->forget('wl');
                Session()->put('wl',$wls);
                return redirect()->back()->with('success','Item is removed from your wish list.');

            }else {
                return redirect()->back()->with('error','Error occurred in removing the item. Please try again.');
            }
        }else {
            return redirect()->back()->with('error','No such item exists in the your wish list to remove.');
        }
    }

    public function cart(){
        $cats = Cat::all();
        $products = "";
        $quantities = array();
       if(Session()->has('cart')){
        $cart = Session()->get('cart');
        $pro = $cart['pro'];
       // dd(array_keys($pro));
       $keys = array_keys($pro);
        $ids = array();
        $i = 0;
        while($i < count($keys)){
            $opro = $pro[$keys[$i]];
            $oproid = $opro['product_id'];
             $ids[] = $oproid;
             $quantities[$oproid] = $opro['quantity'];
            $i++;
       }

       Session()->put('cart_quantities',$quantities);
       $products = Pd::whereIn('product_id',$ids)->get();

        return view('Frontend.cart',['quantities' => $quantities,'cats' => $cats, 'products' => $products]);
       }else {
        return view('Frontend.cart',['quantities' => $quantities,'cats' => $cats, 'products' => $products]);
       }

    }

    public function removeProductFromCart($id){
        if(Session()->has('cart')){
        $cart = Session()->get('cart');
        $pro = $cart['pro'];
        $product = Pd::where(['product_id' => $id]);
        if($product->count() > 0){
            $product = $product->first();
        if(array_key_exists($id,$pro)){
            $rpro = $pro[$id];
            $num = $cart['num'];
            $num = $num - $rpro['quantity'];
            $price = Session()->get('total_cart_cost');

            $price = $price - $product->product_price * $rpro['quantity'];
            unset($pro[$id]);
            $cart['pro'] = $pro;
            $cart['num'] = $num;


            Session()->put('cart',$cart);
            Session()->put('total_cart_cost',$price);
            return redirect()->back()->with('success','Product removed from cart.');
        }
        }else {
            return redirect()->back()->with('error','You have not added products to the cart yet.');
        }
    }else {
        //no such product
        return redirect()->back()->with('error','No such product exists in the system.');

    }
    }

    public function searchProducts(Request $req){
        $name= $req->input('product_name');
        $cat_id =  $req->input('cat_id');
        $cats = Cat::all();
        if(empty($name) || empty($cat_id)){
        return redirect()->back()->with('error','Product title and category must be provide to search.');
        }else if(is_numeric($cat_id) && $cat_id != "all"){
            $scat = Cat::where(['cat_id' => $cat_id]);
            if($scat->count() > 0){
                $products = Pd::where(['cat_id' => $cat_id])->where('product_name', 'like', '%'.$name.'%');
                return view('Frontend.searchproduct',['products' => $products, 'cats' => $cats]);
            }else {
            return redirect()->back()->with('error','No such products category exists.');
            }
        }else if(!is_numeric($cat_id) && $cat_id == "all"){
            $products = Pd::where('product_name', 'like', '%'.$name.'%');
            return view('Frontend.searchproduct',['products' => $products, 'cats' => $cats]);
        }else {
            return redirect()->back()->with('error','Error.');
        }
    }

    public function checkout(){
        $cats = Cat::all();
        return view('Frontend.checkout',['cats' => $cats]);
    }


}

