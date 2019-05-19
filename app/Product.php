<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category as Cat;
use DB;
class Product extends Model
{
    //
    protected $primaryKey = "product_id";

    public static function getProducts(){
        return DB::table('products')->leftjoin('categories',['products.cat_id' => 'categories.cat_id'])
        ->select('product_id','product_name','product_image','products.cat_id','products.created_at','quantity','sold','available','cat_title','cat_title','product_price');
    }

    public static function getProductsByCat($cat_id){
        return DB::table('products')->where(['products.cat_id' => $cat_id])->leftjoin('categories',['products.cat_id' => 'categories.cat_id'])
        ->select('product_id','product_name','product_image','products.cat_id','products.created_at','quantity','sold','available','cat_title','cat_title','product_price');
    }

    public function getCat(){
        return Cat::find($this->cat_id)->cat_title;
    }
}
