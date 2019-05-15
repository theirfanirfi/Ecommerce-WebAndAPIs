<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Checkout extends Model
{
    //
    protected $table = "checkout";

    public function getNewOrders(){
        return DB::table('checkout')->where(['is_processed' => 0])
        ->leftjoin('order',['order.checkout_id' => 'checkout.id'])->get();
    }
}
