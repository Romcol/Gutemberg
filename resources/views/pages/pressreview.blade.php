@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
@if($pressreview)
	<p>Nom : {{$pressreview['name']}}
	<br>Description : {{$pressreview['description']}}</p>
@else
    <p>Aucun résultat pour cette revue de presse.</p>
@endif
@stop