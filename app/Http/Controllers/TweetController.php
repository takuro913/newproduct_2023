<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tweet;
use Auth;
use App\Models\User;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $tweets = Tweet::getAllOrderByUpdated_at();
       return response()->view('tweet.index',compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      return response()->view('tweet.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$requestの中身をテーブルに保存する

         // バリデーション
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
            //↓編集部分
            'img_path' => ['image','mimes:jpeg,png,jpg,gif'],
           
        ]);
        
        // バリデーション:エラー
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.create')
            ->withInput()
            ->withErrors($validator);  
                    
        }
       
        // 画像フォームでリクエストした画像を取得
       // $img = $request->file('img_path');

        // 画像情報がセットされていれば、保存処理を実行
        /*if (isset($img)) {
            // storage > public > img配下に画像が保存される
            $path = $img->store('img','public');
            // store処理が実行できたらDBに保存処理を実行
            if ($path) {
                // DBに登録する処理
                Tweet::create([
                    'img_path' => $path,
                ]);
            }
        }*/
           if ($image) {
            // 拡張子の取得
            $extension = $image->getClientOriginalExtension();

            // 新しいファイル名を作る（ランダムな文字数とする）
            $new_name = uniqid() . "." . $extension;

            // 一時的にtmpフォルダに保存する
            $image_path = Storage::putFileAs(
                'tmp', $request->file('image'), $new_name
            );

        } else {
            $new_name = 'noimage.jpg';
            $extension = '0';
            $image_path = 'noimage.jpg';
        }

        

        // create()は最初から用意されている関数
        // 戻り値は挿入されたレコードの情報
         $data = $request->merge(['user_id' => Auth::user()->id])->all();
         $result = Tweet::create($data);

        
        // ルーティング「todo.index」にリクエスト送信（一覧ページに移動）
        return redirect()->route('tweet.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //一件分のデータを取り出す
        $tweet = Tweet::find($id);
        return response()->view('tweet.show', compact('tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         $tweet = Tweet::find($id);
         return response()->view('tweet.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
          //バリデーション
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
        ]);
        //バリデーション:エラー
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.edit', $id)
            ->withInput()
            ->withErrors($validator);
        }
        //データ更新処理
        $result = Tweet::find($id)->update($request->all());
        return redirect()->route('tweet.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $result = Tweet::find($id)->delete();
        return redirect()->route('tweet.index');
    }
      public function mydata()
    {
    // Userモデルに定義したリレーションを使用してデータを取得する．
     $tweets = User::query()
      ->find(Auth::user()->id)
      ->userTweets()
      ->orderBy('created_at','desc')
      ->get();
     return response()->view('tweet.index', compact('tweets'));
    }
    public function timeline()
    {
      // フォローしているユーザを取得する
      $followings = User::find(Auth::id())->followings->pluck('id')->all();
      // 自分とフォローしている人が投稿したツイートを取得する
      $tweets = Tweet::query()
        ->where('user_id', Auth::id())
        ->orWhereIn('user_id', $followings)
        ->orderBy('updated_at', 'desc')
        ->get();
      return view('tweet.index', compact('tweets'));
    }

}
