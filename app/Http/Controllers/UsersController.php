<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        
        return view('users.index', [
            'users' => $users,
        ]);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        $microposts = $user->microposts()->orderBy('created_at', 'desc')->paginate(10);
        
        $data = [
            'user' => $user,
            'microposts' => $microposts,
        ];
        
        $data += $this->counts($user);
        
        return view('users.show', $data);
    }
    
    //フォロー用追加
    public function followings($id)
    {
        $user = User::find($id);
        $followings = $user->followings()->paginate(10);
        
        $data = [
            'user' => $user,
            'users' => $followings,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followings', $data);
    }
    
    public function followers($id)
    {
        $user = User::find($id);
        $followers = $user->followers()->paginate(10);
        
        $data = [
            'user' => $user,
            'users' => $followers,
        ];
        
        $data += $this->counts($user);
        
        return view('users.followers', $data);
    }
    
    //----ここからfavorite
     public function favoring($micropost)
   {
       $micropost = Micropost::find($micropost);
       $favoring = $user->favoring()->paginate(10);
       
       $data = [
           'user' => $micropost,
           'microposts' => $favoring,
       ];
       
       $data += $this->counts($micropost);
       
       return view('micropost.favorite', $data);
   }
   
   public function favorites($id)  // 何を受け取っている？？？？ $id
   {
       $user = User::find($id);     // ユーザモデルを取ってきてる。
                                  //モデルはTableの一行なので nameもidも全部持ってる
                                  // $user->name, $user->id
       $favorites = $user->favorites()->paginate(10);
       
       $data = [
           'user' =>  $user, //userには 数字（自分のユーザID) //$micropost,
           'microposts' => $favorites,
       ];
       
       $data += $this->counts($user); //ページタブのカウント
       
       return view('users.favorites', $data);
   }
}
