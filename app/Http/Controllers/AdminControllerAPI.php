<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category as Cat;
use App\Product as Pd;
class AdminControllerAPI extends Controller
{
    //

    public function addCategory(Request $req){
        $title = $req->input('cat_title');
        if($title != ""){
        if($req->hasFile('image')){
            $file = $req->file('image');
            $image_name = $file->getClientOriginalName();
            $path = "./uploads/categories/";
            $cat = new Cat();
            $cat->cat_title = $title;
            $cat->cat_image = "http://192.168.10.4/Ecommerce/public/uploads/categories/".$title.time();
            if($file->move($path,$image_name)){

                if($cat->save()){
                    return response()->json([
                        'isAuthenticated' => true,
                        'isError' => false,
                        'isImageError' => false,
                        'isSaved' => true,
                        'isUploaded' => true,
                        'message' => "Category Added."
                    ]);
                }else {
                    return response()->json([
                        'isAuthenticated' => true,
                        'isError' => false,
                        'isImageError' => false,
                        'isSaved' => false,
                        'isUploaded' => true,
                        'message' => "Image Uploaded but not saved. Try again."
                    ]);
                }
            }else {
                return response()->json([
                    'isAuthenticated' => true,
                    'isError' => true,
                    'isImageError' => true,
                    'isSaved' => false,
                    'isUploaded' => false,
                    'message' => "Error occurred in uploading the category image"
                ]);
            }
        }else {
            return response()->json([
                'isAuthenticated' => true,
                'isError' => true,
                'isImageError' => true,
                'message' => "Image must be provided."
            ]);
        }
    }else {
        return response()->json([
            'isAuthenticated' => true,
            'isError' => true,
            'message' => "Category title must be provided."
        ]);
    }
    }


    public function getCategories(Request $req){
        $cats = Cat::all();
        if($cats->count() > 0){
            return response()->json([
                'isError' => false,
                'isAuthenticated' => true,
                'isFound' => true,
                'cats' => $cats
            ]);
        }else {
            return response()->json([
                'isError' => false,
                'isAuthenticated' => true,
                'isFound' => false
            ]);
        }
    }

    public function getProducts(){
        $products = Pd::getProducts();
        if($products->count() > 0){
            return response()->json([
                'isAuthenticated' => true,
                'isError' => false,
                'isFound' => true,
                'products' => $products->get()
            ]);
        }else {
            return response()->json([
                'isAuthenticated' => true,
                'isError' => false,
                'isFound' => false,
                'message' => 'No products found'
            ]);
        }
    }

    public function addproduct(Request $req){
        $name = $req->input('product_name');
        $quantity = $req->input('product_quantity');
        $price = $req->input('product_price');
        $cat_id = $req->input('cat_id');

        if(empty($name) || empty($quantity) ||empty($cat_id)){
            return response()->json([
                'isAuthenticated' => true,
                'isError' => true,
                'message' => "Arguments must be provided."
            ]);
        }else {
            if($req->hasFile('image')){
                $file = $req->file('image');
                $extension = $file->getClientOriginalExtension();
                $product_image_name = time().$quantity.$cat_id.rand(0,10000).".".$extension;
                $path = "./uploads/products/";

                if($file->move($path,$product_image_name)){
                    $product = new Pd();
                    $product->product_name = $name;
                    $product->quantity = $quantity;
                    $product->available = $quantity;
                    $product->sold = 0;
                    $product->product_price = $price;
                    $product->cat_id = $cat_id;
                    $product->product_image = "http://192.168.10.4/Ecommerce/public/uploads/products/".$product_image_name;

                    if($product->save()){
                        return response()->json([
                            'isAuthenticated' => true,
                            'isError' => false,
                            'isSaved' => true,
                            'message' => "Product Added."
                        ]);
                    }else {
                        return response()->json([
                            'isAuthenticated' => true,
                            'isError' => true,
                            'message' => "Error occurred in saving the uploaded image. Please try again."
                        ]);
                    }
                }else {
                    return response()->json([
                        'isAuthenticated' => true,
                        'isError' => true,
                        'message' => "Error occurred in uploading the image. Please try again."
                    ]);
                }

            }else {
                return response()->json([
                    'isAuthenticated' => true,
                    'isError' => true,
                    'message' => "Product Image must be provided."
                ]);
            }
        }
    }

    public function getProduct(Request $req){
        $product_id = $req->input('product_id');
        $product = Pd::where(['product_id' => $product_id]);
        if($product->count() > 0){
            return response()->json([
                'isAuthenticated' => true,
                'isError' => false,
                'isFound' => true,
                'product' => $product->first()
            ]);
        }else {
            return response()->json([
                'isAuthenticated' => true,
                'isError' => true,
                'isFound' => false,
                'message' => "No such Product exists in the system."
            ]);
        }
    }

    public function deleteproduct(Request $req){
        $product_id = $req->input('product_id');

        $product = Pd::where(['product_id' => $product_id]);
        if($product->count() > 0){

            if($product->first()->delete()){
                return response()->json([
                    'isAuthenticated' => true,
                    'isError' => false,
                    'isFound' => true,
                    'isDeleted' => true,
                    'message' => 'product deleted.'
                ]);
            }else {
                return response()->json([
                    'isAuthenticated' => true,
                    'isError' => true,
                    'isFound' => true,
                    'isDeleted' => false,
                    'message' => 'Error occurred in deleting the product. Try again.'
                ]);
            }


        }else {
            return response()->json([
                'isAuthenticated' => true,
                'isError' => true,
                'isFound' => false,
                'message' => "No such Product exists in the system."
            ]);
        }
    }


}
