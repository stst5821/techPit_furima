<?php

// pay.jpの設定。envに書いた公開鍵と秘密鍵を指定。
// env(第一引数,第二引数は指定した環境変数が見つからなかった場合の初期値)
return [
    'public_key' => env('PAYJP_PUBLIC_KEY'),
    'secret_key' => env('PAYJP_SECRET_KEY'),
];