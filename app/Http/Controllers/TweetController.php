<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tweet;
use Auth;

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
        ]);
        // バリデーション:エラー
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.create')
            ->withInput()
            ->withErrors($validator);
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
}
