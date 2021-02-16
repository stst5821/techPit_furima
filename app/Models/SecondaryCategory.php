<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecondaryCategory extends Model
{
    // secondaryカテゴリは、1つのprimaryカテゴリを持つ。
    public function primaryCategory()
    {
        return $this->belongsTo(PrimaryCategory::class);
    }
}