@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?php echo asset('css/cover.css')?>" type="text/css"> 
@stop

@section('page_content')
<h1>Articles</h1>

@foreach ($articles as $article)
	<article>
	<h2>{{$article['titre']}}</h2>
	<div><p>Auteur : {{$article['auteur']}}</p>
	<p>Journal : {{$article['titrejournal']}}</p>
	<p>Date : {{$article['date']}}</p></div>
	</article>
@endforeach

@stop