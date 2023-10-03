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
        //$request�̒��g���e�[�u���ɕۑ�����
        
         // �o���f�[�V����
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
            //���ҏW����
            'img_path' => ['image','mimes:jpeg,png,jpg,gif'],          
        ]);
        
        // �o���f�[�V����:�G���[
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.create')
            ->withInput()
            ->withErrors($validator);                      
        }
         
        // �摜�t�H�[���Ń��N�G�X�g�����摜���擾
         $img = $request->file('img_path');        
        // �摜��񂪃Z�b�g����Ă���΁A�ۑ����������s
         if (isset($img)) {
            // storage > public > img�z���ɉ摜���ۑ������
            $path = $img->store('img','public');  
            $path=explode('/',$path);       
        }

         
        // ? �ҏW �t�H�[�����瑗�M����Ă����f�[�^�ƃ��[�UID���}�[�W���CDB��insert����
         $data = $request->merge(['user_id' => Auth::user()->id])->all();
         $result = Tweet::create($data);
         /*��������return redirect�̑O�܂ŏ�������摜�p�X��/tmp/php����n�܂��ɂȂ��č�������A�A�b�v�f�[�g
         ���������\�ɒǉ������*/
         $tweet_id = Tweet::insertGetId([
          
           'user_id'=>$data['user_id'],
           'tweet'=>$data['tweet'], 
            'description'=>$data['description'], 
            'img_path'=> $path[1]
            
            
          ]);
        
        // ���[�e�B���O�utodo.index�v�Ƀ��N�G�X�g���M�i�ꗗ�y�[�W�Ɉړ��j
        return redirect()->route('tweet.index');
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //�ꌏ���̃f�[�^�����o��
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
          //�o���f�[�V����
        $validator = Validator::make($request->all(), [
            'tweet' => 'required | max:191',
            'description' => 'required',
        ]);
        //�o���f�[�V����:�G���[
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.edit', $id)
            ->withInput()
            ->withErrors($validator);
        }
        //�f�[�^�X�V����
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
    // User���f���ɒ�`���������[�V�������g�p���ăf�[�^���擾����D
     $tweets = User::query()
      ->find(Auth::user()->id)
      ->userTweets()
      ->orderBy('created_at','desc')
      ->get();
     return response()->view('tweet.index', compact('tweets'));
    }
    public function timeline()
    {
      // �t�H���[���Ă��郆�[�U���擾����
      $followings = User::find(Auth::id())->followings->pluck('id')->all();
      // �����ƃt�H���[���Ă���l�����e�����c�C�[�g���擾����
      $tweets = Tweet::query()
        ->where('user_id', Auth::id())
        ->orWhereIn('user_id', $followings)
        ->orderBy('updated_at', 'desc')
        ->get();
      return view('tweet.index', compact('tweets'));
    }

}
