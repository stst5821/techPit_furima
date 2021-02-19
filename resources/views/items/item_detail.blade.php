@extends('layouts.app')

@section('title')
{{$item->name}} | 商品詳細
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-8 offset-2 bg-white">
            <div class="row mt-3">
                <div class="col-8 offset-2">
                    @if (session('message'))
                    <div class="alert alert-{{ session('type', 'success') }}" role="alert">
                        {{ session('message') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- あっとincludeで別のbladeを読み込む 第一引数でbladeの名前、第二引数で、そのbladeに渡す変数を連想配列で指定する。今回は1つの変数しか移動しない。 -->
            @include('items.item_detail_panel', [
            'item' => $item
            ])

            <div class="row">
                <div class="col-8 offset-2">
                    @if ($item->isStateSelling)
                    <a href="{{route('item.buy', [$item->id])}}" class="btn btn-secondary btn-block">購入</a>
                    @else
                    <button class="btn btn-dark btn-block" disabled>売却済み</button>
                    @endif
                </div>
            </div>

            <!-- 改行を含む変数をそのまま出力すると、改行が出力されない。そのためnl2br関数で、説明文の中の改行をbrタグに変換している。 -->
            <!-- 先にe関数(HTMLをエスケープする)にかけてから、nl2br関数をかける。逆にするとbrタグがエスケープされてしまう。 -->
            <div class="my-3">{!! nl2br(e($item->description)) !!}</div>
        </div>
    </div>
</div>
@endsection