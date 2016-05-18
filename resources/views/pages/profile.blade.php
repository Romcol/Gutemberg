@extends('app')

@section('page_content_notfluid')
    <h3>Page de profil</H3>
    <p>Nom : {{$user['name']}}</p>
    <p>Email : {{$user['email']}}</p>
    <p><a href="#">Modifier le profil (TODO)</a></p>
    <p><a href="<?= url('/revue/create') ?>">Créer une nouvelle revue de presse</a></p>
    @if($user['createdReviews'] != '[]')
    <h4>Dernières revues de presse crées :</h4>
	    @foreach ($user['createdReviews'] as $i => $pressreview)
		    @if($i < 5)
		    <p><a href="<?= url('revue/'.$pressreview['_id']) ?>">{{$pressreview['name']}}</a><br>
		    Description {{$pressreview['description']}}
		    </p>
		    @endif
	    @endforeach
    @endif
    @if(session('message') && session('status'))
	    @if(session('status') == 'success')
	    <div class="alert alert-success">
	        {{ session('message') }}
	    </div>
	    @else
	    <div class="alert alert-danger">
	        {{ session('message') }}
	    </div>
	    @endif
    @endif
@stop