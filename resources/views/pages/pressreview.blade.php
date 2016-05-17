@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
@if($pressreview)
	<p>Nom : {{$pressreview['name']}}
	<br>Description : {{$pressreview['description']}}</p>
	@if(!empty($pressreview['articles']))
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
@else
    <p>Aucun résultat pour cette revue de presse.</p>
@endif
@stop