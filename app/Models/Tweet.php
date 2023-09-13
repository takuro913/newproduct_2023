<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;
 //����ɓ���Ă͂����Ȃ��J�����ꗗ
  protected $guarded = [
    'id',
    'created_at',
    'updated_at',
  ];

 //�X�V�����ɑS���f�[�^�����֐�
  public static function getAllOrderByUpdated_at()
  {
    return self::orderBy('updated_at', 'desc')->get();
  }
   public function user()
  {
    return $this->belongsTo(User::class);
  }
   public function users()
  {
    return $this->belongsToMany(User::class)->withTimestamps();
  }

}
