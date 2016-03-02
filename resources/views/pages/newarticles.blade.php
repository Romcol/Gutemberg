@extends('newapp')

@section('page_content')
    <!-- Title -->
    <div class="row">
	    <div class="col-lg-12">
	        <h3>Resultats de la recherche</h3>
	    </div>
	</div>
    <!-- /.row -->
@if(!$articles->isEmpty())
@foreach ($articles as $article)
	<article>
	<h2>{{$article['Title']}}</h2>
	<div>
	<p>Journal : {{$article['TitleNewsPaper']}}</p>
	<p>Date : {{$article['Date']}}</p></div>
	</article>
@endforeach
@else
    <p>Aucun résultat pour cette recherche.</p>
@endif


@stop