@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
    <h3>Page de profil</H3>
    <p>Nom : {{$user['name']}}</p>
    <p>Email : {{$user['email']}}</p>
    <p><a href="#">Modifier le profil (TODO)</a></p>
    <p><a href="/creationrevue">Créer une nouvelle revue de presse</a></p>
    <p>Dernières revues de presse contribuées (TODO)</p>
@stop