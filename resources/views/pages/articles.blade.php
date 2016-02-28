@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?php echo asset('css/cover.css')?>" type="text/css"> 
@stop

@section('page_content')
<h1>Articles</h1>

@foreach ($articles as $article)
	<article>
	<h2>{{$article['Title']}}</h2>
	<div>
	<p>Journal : {{$article['TitleNewsPaper']}}</p>
	<p>Date : {{$article['Date']}}</p></div>
	</article>
@endforeach

@stop