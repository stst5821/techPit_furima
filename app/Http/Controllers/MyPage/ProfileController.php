<?php

namespace App\Http\Controllers\MyPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// この記述をすることで、このファイル内では、Authというクラス名だけで利用できるようになる。
// これを書かなかったら、アクションメソッド内で、Auth::user()と書くところを、Illuminate\Support\Facades\Auth::user()を書かないといけなくなる。
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Mypage\Profile\EditRequest;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\User;

class ProfileController extends Controller
{
    public function showProfileEditForm()
    {
        return view('mypage.profile_edit_form') // bladeテンプレートからhtmlのレスポンスを生成。
        ->with('user',Auth::user()); // bladeテンプレートに変数(user)を渡す。(bladeでの変数名,変数に格納される値(今回の場合は、ログイン中のユーザー情報))
    }

    // メソッドインジェクション = ルートからコントローラーのメソッドを呼び出した際、引数のクラス(↓の場合は、EditRequest)を自動的に生成してくれる仕組み。
    // EditRequestクラスは、インスタンスが生成されたタイミングでバリデーションを実施するため、(EditRequest $request)と書くだけでバリデーションしてくれる。
    public function editProfile(EditRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->input('name');

        // $request->has()で、画像が送信されているか確認。されていれば、実行。
        if ($request->has('avatar')) {
            $fileName = $this->saveAvatar($request->file('avatar'));
            $user->avatar_file_name = $fileName;
        }
        
        $user->save();
        
        return redirect()->back()
            ->with('status','プロフィールを変更しました。'); // statusという値で文字列を保存している。
    }

    /**
      * アバター画像をリサイズして保存します
      *
      * @param UploadedFile $file アップロードされたアバター画像
      * @return string ファイル名
      */
    private function saveAvatar(UploadedFile $file): string
    {
        $tempPath = $this->makeTempPath();

        Image::make($file)->fit(200, 200)->save($tempPath);

        $filePath = Storage::disk('public')
            ->putFile('avatars', new File($tempPath));

        return basename($filePath);
    }

        /**
     * 一時的なファイルを生成してパスを返します。
    *
    * @return string ファイルパス
    */
    private function makeTempPath(): string
    {
        $tmp_fp = tmpfile();
        $meta   = stream_get_meta_data($tmp_fp);
        return $meta["uri"];
    }
}