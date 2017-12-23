@extends('layouts.app')

@section('content')
    <div class="row">
        <aside class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $user->name }}</h3>
                </div>
                <div class="panel-body">
                    <img class="media-object img-rounded img-responsive" src="{{ Gravatar::src($user->email, 500) }}" alt="">
                </div>
            </div>
            @include('micropost_favorite.favorite_button', ['user' => $micropost])
        </aside>
        <div class="col-xs-8">
            <ul class="nav nav-tabs nav-justified">
                <li role="presentation" class="{{ Request::is('microposts/' . $micropost->id) ? 'active' : '' }}"><a href="{{ route('users.show', ['id' => $micropost->id]) }}">Microposts <span class="badge">{{ $count_microposts }}</span></a></li>
                <li role="presentation" class="{{ Request::is('microposts/*/favorings') ? 'active' : '' }}"><a href="{{ route('users.favorings', ['id' => $micropost->id]) }}">Favorings <span class="badge">{{ $count_favorings }}</span></a></li>
                <li role="presentation" class="{{ Request::is('microposts/*/favorites') ? 'active' : '' }}"><a href="{{ route('users.favorites', ['id' => $micropost->id]) }}">Favorites <span class="badge">{{ $count_favorites }}</span></a></li>
            </ul>
            @include('microposts.favorites', ['microposts' => $microposts])
        </div>
    </div>
@endsection