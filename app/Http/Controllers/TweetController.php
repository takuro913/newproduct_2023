<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tweet;

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
        ]);
        // �o���f�[�V����:�G���[
        if ($validator->fails()) {
            return redirect()
            ->route('tweet.create')
            ->withInput()
            ->withErrors($validator);
        }
        // create()�͍ŏ�����p�ӂ���Ă���֐�
        // �߂�l�͑}�����ꂽ���R�[�h�̏��
        $result = Tweet::create($request->all());
        // ���[�e�B���O�utodo.index�v�Ƀ��N�G�X�g���M�i�ꗗ�y�[�W�Ɉړ��j
        return redirect()->route('tweet.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
