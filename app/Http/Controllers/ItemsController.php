<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemsController extends Controller
{
    public function showItems(Request $request)
    {
        // orderByRawメソッドを使うと、ORDER BY句のSQLを直接記述できる。↓のSQLを展開すると、ORDER BY FIELD(state,'selling','bought')
        $items = Item::orderByRaw( "FIELD(state, '" . Item::STATE_SELLING . "', '" . Item::STATE_BOUGHT . "')" )
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('items.items')
            ->with('items', $items);
    }

    // ルーターで、ルートパラメータを定義。定義したルートパラメータと同じ名前で引数を定義する。
    public function showItemDetail(Item $item)
    {
        return view('items.item_detail')
            ->with('item', $item);
    }
    
}