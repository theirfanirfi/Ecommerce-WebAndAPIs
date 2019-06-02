<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function verifyToken($token){
        return User::where(['token' => $token]);
    }

    public function returnUser($token){
        $user =  User::where(['token' => $token]);
        return $user->count() > 0 ? $user->first() : false;
    }

    public static function getMembers(){
        return DB::table('users')->where('users.role','=','0')
      //  ->leftjoin('checkout',['checkout.user_id' => 'users.id'])
     //   ->select('users.id as user_id','name','checkout.id as checkout_id','total_price','products_quantity',DB::raw('SUM(total_price) as totalSpent'),DB::raw('SUM(products_quantity) as totalBought'))
        ->select('id','name')
     //     //->groupby('user_id')
    //     ->groupby('name')
    //    // ->groupby('checkout_id')
    //     ->groupby('total_price')
    //     ->groupby('users.id')
    //     ->groupby('checkout.id')
    //     // ->groupby('totalSpent')
    //     // ->groupby('totalBought')
    //     ->groupby('products_quantity')
        ->orderBy('id','DESC');
    }

    public static function getProfile($token){
        return User::where(['token' => $token,'role' => 1])
        ->select('id','name','email','shipmentduration','password');
    }

    public static function getUserByToken($token){
        $user =  User::where(['token' => $token, 'role' => 0]);
        return $user->count() > 0 ? $user->first() : false;
    }
}
