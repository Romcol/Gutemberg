@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
<p>Revue de presse vide créee.</p>
<p>Infos :<br>
Nom : {{$name}}<br>
Description : {{$description}}<br>
</p>
@stop