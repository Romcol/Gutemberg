@extends('app')

@section('page_content_notfluid')
	@if(Auth::user())
    <h3>Votre profil</h3>
    <hr>
    <p><strong>Nom :</strong> {{$user['name']}}</p>
    <p><strong>Email :</strong> {{$user['email']}}</p>
    <p><a class="btn btn-default" href="#">Modifier le profil (TODO)</a></p>
    <p><a class="btn btn-default" href="{{ url('/revue/create') }}">Créer une nouvelle revue de presse</a></p>
    @if($user['createdReviews'])
    <div class="createdpressreviews">
    <h4>Dernières revues de presse crées</h4>
    <hr>
    <div class="row">
    <div class="col-lg-6 col-md-6">
	    @foreach ($user['createdReviews'] as $i => $pressreview)
		    @if($i < 5)
		   	<div class="panel panel-default">
		              <div class="panel-heading">
		               <a href="<?= url('revue/'.$pressreview['_id']) ?>"> <h4 class="panel-title">{{$pressreview['name']}}</h4></a>
		              </div>
		              <div class="panel-body">
		                {{$pressreview['description']}}
		              </div>
		    </div>
		    @endif
	    @endforeach
	</div>
	</div>
	</div>
    @endif
    @else
    <p>Erreur. Vous n'êtes pas authentifié.</p>
    @endif
    @if(session('message') && session('status'))
    	<div class="row">
    	<div class="col-lg-6 col-md-6">
	    @if(session('status') == 'success')
	    <div class="alert alert-success">
	        {{ session('message') }}
	    </div>
	    @else
	    <div class="alert alert-danger">
	        {{ session('message') }}
	    </div>
	    @endif
	    </div>
	    </div>
    @endif
@stop