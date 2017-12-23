<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    //追加
     public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    //多対多の関係を記述
     public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
        /*
        「Favoriteを考える」
            $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')
                              // -----------   -----------    -------    ---------
                              //     ①             ②            ③          ④
        
        $this は自分自身（Userクラス）
        ① どのクラスでできた配列結果を返して欲しいの？ →　User::class  / 　Microposts::class
        ② どの中間テーブルを見るの？　→　'user_follow'   / micropost_favorite  
        ③ ②のテーブルの中であなた自身はどのカラム？　→　'user_id'   / user_id
        ④ ②のテーブルの中でどのカラムを ①のクラスのidとすればいいの？　→　'follow_id'    / micropost_id
            ④のカラムのIDを使って①のデータを結果として返す
        */
    }
    
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId) {
    // 既にフォローしているかの確認 
    $exist = $this->is_following($userId);
    
    // 自分自身ではないかの確認
    $its_me = $this->id == $userId;
    
        if ($exist || $its_me) {
    
            // 既にフォローしていれば何もしない
    
            return false;
    
        } else {
    
            // 未フォローであればフォローする
    
            $this->followings()->attach($userId);
    
            return true;
    
        }
    }
    
    //フォロー、アンフォローできるよう、モデルにメソッドを定義
    public function unfollow($userId)
    {
        // 既にフォローしているかの確認
        $exist = $this->is_following($userId);
        // 自分自身ではないかの確認
        $its_me = $this->id == $userId;
    
        if ($exist && !$its_me) {
            // 既にフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    //タイムラインにマイクロポストを取得するためのメソッド
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()->lists('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    //ここからfavorite--------------
    
     //多対多の関係を記述
     public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'micropost_favorite', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    public function favorite($micropostId) 
    {
    // 既にフェイバリットしているかの確認 
    $exist = $this->is_favoring($micropostId);  
    
    // 同じものではないかの確認--->不要
    // $its_me = $this->id == $userId;
    
        if ($exist) {
    
            // 既にフェイバリットしていれば何もしない
    
            return false;
    
        } else {
    
            // 未フェイバリットであればフェイバリットする
    
            $this->favorites()->attach($micropostId);
    
            return true;
    
        }
    }
    
    //フェイバリット、アンフェイバリットできるよう、モデルにメソッドを定義
    public function unfavorite($micropostId)
    {
        // 既にフェイバリットしているかの確認
        $exist = $this->is_favoring($micropostId);
        
        // 自分自身ではないかの確認-->不要
        // $its_me = $this->id == $userId;
    
        if ($exist) {
            // 既にフェイバリットしていればフェイバリットを外す
            $this->favorites()->detach($micropostId);
            return true;
        } else {
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    public function is_favoring($micropostId) {
        return $this->favorites()->where('micropost_id', $micropostId)->exists();
    }

}


