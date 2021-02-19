<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemsController extends Controller
{

    public function showItems(Request $request)
    {

        $query = Item::query();
 
         // カテゴリで絞り込み

        // Requestインスタンスのfilledメソッドで、パラメータが指定されているか調べる。返り値は真偽値。
        // hasメソッドもあるが、空文字列の場合もtrueを返す。例えば?category=というキー名だけのパラメータでもtrueになってしまうので使えない。
        if ($request->filled('category')) {
            // explodeメソッドで文字列を分割。第一引数は、区切り文字(デリミタ)を指定。(どの文字を境に文字を分割するか、今回なら：を境に分割する)第二引数には、分割する文字列を指定。
            // 戻り値は、文字列の配列になる。今回の場合は['secondary','7']になる。(数字は仮)
            // 分割した文字列をそれぞれ変数に代入。$categoryTypeにsecondary、$categoryIDに7を代入。
            list($categoryType, $categoryID) = explode(':', $request->input('category'));

            if ($categoryType === 'primary') {
                // itemとprimaryのリレーションは無いので、secondaryを通してprimaryを探す必要がある。
                // リレーション先のテーブルを元に絞り込みするので、whereHasを使う。
                // EloquentのwhereHasはサブクエリが発生するので遅いようだ。今回はひとまずこれですすめる。
                $query->whereHas('secondaryCategory', function ($query) use ($categoryID) {
                    $query->where('primary_category_id', $categoryID);
                });
            } else if ($categoryType === 'secondary') {
                $query->where('secondary_category_id', $categoryID);
            }
        }

        // キーワードで絞り込み
        if ($request->filled('keyword')) {
            
            // %で囲ってLIKE検索。escapeでXSS対策も行う。
            $keyword = '%' . $this->escape($request->input('keyword')) . '%';
            
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', $keyword);
                $query->orWhere('description', 'LIKE', $keyword);
                });
        }
        
        // orderByRawメソッドを使うと、ORDER BY句のSQLを直接記述できる。↓のSQLを展開すると、ORDER BY FIELD(state,'selling','bought')
        $items = $query->orderByRaw( "FIELD(state, '" . Item::STATE_SELLING . "', '" . Item::STATE_BOUGHT . "')" )
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('items.items')
            ->with('items', $items);
    }

    // キーワードをエスケープする
    // エスケープしないと、%や_を含むキーワードが入力された場合、意図しない結果が出てしまう。
    private function escape(string $value)
    {
        // str_replase(検索文字列,置き換え文字列,対象文字列)
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    // ルーターで、ルートパラメータを定義。定義したルートパラメータと同じ名前で引数を定義する。
    public function showItemDetail(Item $item)
    {
        return view('items.item_detail')
            ->with('item', $item);
    }

    // 商品購入画面

    public function showBuyItemForm(Item $item)
    {
        // 販売中でない商品の場合は、abort関数で404を返す。abort関数を呼び出した時点で処理が切り上げられるので、returnは不要。
        if (!$item->isStateSelling) {
            abort(404);
        }

        return view('items.item_buy_form')
            ->with('item', $item);
    }

    // 商品購入処理

    public function buyItem(Request $request, Item $item)
    {
        $user = Auth::user();

        // 販売中でなければ、404を返す
        if (!$item->isStateSelling) {
            abort(404);
        }

        $token = $request->input('card-token');

        // try/catchで例外が発生した場合の処理を書く。
        try {
            // settlementメソッドの中で例外が発生した場合、そこで処理が切り上げられ、catchに処理が移る。
            $this->settlement($item->id, $item->seller->id, $user->id, $token);
        } catch (\Exception $e) {
            Log::error($e); // storage/logs/laravel.logにログを記録する。
            return redirect()->back()
                ->with('type', 'danger')
                ->with('message', '購入処理が失敗しました。');
        }

        return redirect()->route('item', [$item->id])
            ->with('message', '商品を購入しました。');
    }

    // 決済をするsettlementメソッド

    private function settlement($itemID, $sellerID, $buyerID, $token)
    {
        // トランザクション。複数のSQL文によるデータ更新を1つの処理としてまとめてデータベースに反映させる。
        // トランザクションについて、https://oss-db.jp/dojo/dojo_01
        DB::beginTransaction();

        try {
            // 同じ商品を複数の人が同時に購入した場合、処理が並列に実行され、決済が重複して行われる可能性がある。
            // 多重決済を避けるためにレコードを排他ロックし、同じレコードに対する処理が並列に実行されないようにしている。
            $seller = User::lockForUpdate()->find($sellerID);
            $item   = Item::lockForUpdate()->find($itemID);

            if ($item->isStateBought) {
                throw new \Exception('多重決済');
            }

            $item->state     = Item::STATE_BOUGHT;
            $item->bought_at = Carbon::now();
            $item->buyer_id  = $buyerID;
            $item->save();

            $seller->sales += $item->price;
            $seller->save();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
    }
    
}