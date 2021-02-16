<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 商品一覧画面(トップ)
Route::get('', 'ItemsController@showItems')->name('top');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// 商品詳細画面

Route::get('items/{item}', 'ItemsController@showItemDetail')->name('item');

// 商品出品

Route::middleware('auth')
->group(function () {
    Route::get('sell', 'SellController@showSellForm')->name('sell');
    Route::post('sell', 'SellController@sellItem')->name('sell');
});

// プロフィール画面

Route::prefix('mypage')
->namespace('Mypage') // 名前空間の接頭辞を指定できる。これを書くと、get()内に、MyPage\ProfilecontrollerとMypageを書かずに済む。
->middleware('auth') // ログインしている状態でのみアクセスできる。ミドルウェアはルーティングとコントローラーの間に割り込んで処理する。
->group(function(){
    Route::get('edit-profile','ProfileController@showProfileEditForm')->name('mypage.edit-profile');
    Route::post('edit-profile','ProfileController@editProfile')->name('mypage.edit-profile');

    Route::get('sold-items', 'SoldItemsController@showSoldItems')->name('mypage.sold-items');
});