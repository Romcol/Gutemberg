@extends('newapp')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

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
	<h3>{{$article['Title']}}</h3>
	<div>
	<p>Journal : {{$article['TitleNewsPaper']}}</p>
	<p>Date : {{$article['Date']}}</p>
	<p>{{$article['Words']}}</p></div>
	</article>
@endforeach
@else
    <p>Aucun résultat pour cette recherche.</p>
@endif


@stop