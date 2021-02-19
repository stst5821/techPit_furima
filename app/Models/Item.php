<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    // 出品中
    const STATE_SELLING = 'selling';

    // 購入済
    const STATE_BOUGHT = 'bought';

    public function secondaryCategory()
    {
        // 商品は、1つのセカンドカテゴリを持つ。
        return $this->belongsTo(SecondaryCategory::class);
    }

    public function seller()
    {
        // 商品は、1つのsellerを持つ
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function condition()
    {
        // 商品は、1つのconditionを持つ
        return $this->belongsTo(ItemCondition::class, 'item_condition_id');
    }

    // 商品が出品中かどうかを返すアクセサ
    public function getIsStateSellingAttribute()
    {
        // $this->stateが、このアクセサを呼び出したリクエストのstate
        // self::STATE_SELLINGが、このモデル内で作った定数。この場合、sellingが入る。
        return $this->state === self::STATE_SELLING;
    }

    public function getIsStateBoughtAttribute()
    {
        return $this->state === self::STATE_BOUGHT;
    }
}