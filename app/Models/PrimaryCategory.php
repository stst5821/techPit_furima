<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrimaryCategory extends Model
{
    // 1つのPrimaryCategoryは、複数のsecondaryCategoryを持つ。
    public function secondaryCategories()
    {
        // hasmany(第一引数,第二引数,第三引数)
        // 第一引数(1：多の、多側のEloquentModelの完全修飾クラス名を指定する),
        // 第二引数(多側のキーとなるカラム名を指定する。指定しなかった場合は、クラス名から自動で決まる。)
        // 第三引数(1側のキーとなるカラム名を指定する。指定しなかった場合は、idが使われる。)
        return $this->hasMany(SecondaryCategory::class);
    }
}