@extends('backend.layouts.app')

@section('title', app_name() . ' | ' . $article->article_title )


@section('content')

<publishing-item-show :id="{{$article->id}}"></publishing-item-show>
 
@endsection
