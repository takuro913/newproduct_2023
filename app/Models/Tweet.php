<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;
 //勝手に入れてはいけないカラム一覧
  protected $guarded = [
    'id',
    'created_at',
    'updated_at',
  ];
  /*protected $fillable = [
        'img_path',
  ];*/
   

 //更新日順に全件データを取る関数
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
