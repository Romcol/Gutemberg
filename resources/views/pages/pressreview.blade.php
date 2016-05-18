@extends('app')

@section('page_content_notfluid')
@if($pressreview)
	<p>Nom : {{$pressreview['name']}}
	<br>Description : {{$pressreview['description']}}</p>
	@if($pressreview['articles'] != '[]')
		@foreach ($pressreview['articles'] as $article)
		      	<article>
	            <div class="panel panel-default">
	              <div class="panel-heading">
	               <a href="visionneuse?id={{$article['IdPage']}}&article={{$article['_id']}}"> <h3 class="panel-title">{{$article['TitleNewsPaper']}}, {{$article['date']}}</h3></a>
	              </div>
	              <div class="panel-body">
	                <B class="title">{{$article['Title']}}</B>
	                <p style="margin-top:20px">{!! $article['description'] !!}</p>
	              </div>
	            </div>
	      		</article>
		@endforeach
	@else
	<p>Revue de presse vide.<p>
	@endif
	<a href="<?= url('/revue/'.$pressreview['_id'].'/delete') ?>" class="btn btn-primary" role="button">Supprimer la revue de presse</a>
@else
    <p>Aucun résultat pour cette revue de presse.</p>
@endif
@stop