@extends('app')

@section('page_content_notfluid')
    <h3>Page de profil</H3>
    <p>Nom : {{$user['name']}}</p>
    <p>Email : {{$user['email']}}</p>
    <p><a href="#">Modifier le profil (TODO)</a></p>
    <p><a href="/creationrevue">Créer une nouvelle revue de presse</a></p>
    <h4>Dernières revues de presse crées :</h4>
    @if($user['createdReviews'] != '[]')
	    @foreach ($user['createdReviews'] as $i => $pressreview)
		    @if($i < 5)
		    <p><a href="revue-{{$pressreview['_id']}}">{{$pressreview['name']}}</a><br>
		    Description {{$pressreview['description']}}
		    </p>
		    @endif
	    @endforeach
    @endif
@stop