<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public function soldItems()
    {
        // 1つのユーザーは、複数の販売商品を持つ。第二引数で多側の外部キーを指定しています。
        return $this->hasMany(Item::class, 'seller_id');
    }

    public function boughtItems()
    {
        // 1つのユーザーは、複数の購入商品を持つ。第二引数で多側の外部キーを指定しています。
        return $this->hasMany(Item::class, 'buyer_id');
    }

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
}