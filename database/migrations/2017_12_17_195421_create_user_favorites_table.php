 <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFavoritesTable extends Migration
{
 public function up()
    {
        Schema::create('micropost_favorite', function (Blueprint $table) {  // micropost_favorite テーブルを作る
            $table->increments('id');   // favoriteのID
            $table->integer('micropost_id')->unsigned()->index();  // 投稿したpostのID
            $table->integer('user_id')->unsigned()->index();  // favoriteしたユーザのID
            $table->timestamps();

            // 外部キー設定
            $table->foreign('micropost_id')->references('id')->on('microposts')->onDelete('cascade');

            // micropost_idとuser_idの組み合わせの重複を許さない
            $table->unique(['micropost_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::drop('micropost_favorite');
    }
}