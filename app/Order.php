<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Order extends Model
{
    //
    protected $table = "order";

    public static function getOrderProducts($checkout_id){
        return DB::table('order')->where(['checkout_id' => $checkout_id])
        ->leftjoin('products',['products.product_id' => 'order.product_id']);
    }
}
