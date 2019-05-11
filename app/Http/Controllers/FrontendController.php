<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product as Pd;
use App\Category as Cat;
use App\RecentlyViewed as RV;
use Auth;
class FrontendController extends Controller
{
    //

    public function index(){

        $rv = "";
        if(Auth::check()){
            $rv = new RV();
            $user = Auth::user();
            $rv = $rv->getRV($user->id);
            if($rv->count() > 0){
                $rv = $rv->get();
            }
        }

        $cats = Cat::all();
        $products = Pd::inRandomOrder()->limit(8)->get();
        $rpro = Pd::latest()->limit(8)->get();
        return view('Frontend.home',['cats' => $cats, 'products' => $products, 'recentproducts' => $rpro,'rvs' => $rv]);
    }

    public function addtocart($id){
        $product = Pd::where(['product_id' => $id]);
        if($product->count() > 0){
            if(Session()->has('cart')){
                $product = $product->first();
                $arr = Session()->get('cart');
                $arr[] = $product->product_id;
                $price = Session()->get('total_cart_cost');
                $price = $price + $product->product_price;
                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$price);
                return redirect()->back()->with('success','Product Added to cart.');
            }else {
                $product = $product->first();
                $arr = array();
                $arr[] = $product->product_id;
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
        if($product->count() > 0){
            if(Session()->has('cart')){
                $product = $product->first();
                $arr = Session()->get('cart');
                $arr[] = $product->product_id;
                $price = Session()->get('total_cart_cost');
                $price = $price + $product->product_price * $quantity;
                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$price);
                return redirect()->back()->with('success','Product Added to cart.');
            }else {
                $product = $product->first();
                $arr = array();
                $arr[] = $product->product_id;
                Session()->put('cart',$arr);
                Session()->put('total_cart_cost',$product->product_price * $quantity);
                return redirect()->back()->with('success','Product Added to cart.');
            }
        }else {
            return redirect('/')->with('error','No such product exists in the store.');
        }
    }
}
