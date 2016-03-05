@extends('newapp')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_row')

<form class="form-inline" action="recherche">
      <div class="form-group">
        <input type="text" name="text" class="form-control" id="search_input" placeholder="Rechercher" value={{$text}} required>
      </div>
      <div class="form-group">
          <select name="type" class="form-control">
          <option value="articles">Contenu des articles</option>
          <!-- <option value="revues">Revues de presse</option> -->
          <option value="titles">Titres des articles</option>
          </select>
      </div>

      <hr>
      <h4>Filtres :</h4>
        <h5>Date :</h5>
        <div class="form-group">
          De <input type="date" name="dateMin" class="form-control" id="dateMin_input" placeholder={{$dMin}} value={{$dateMin}}>
        </div>
        <div class="form-group">
          à <input type="date" name="dateMax" class="form-control" id="dateMax_input" placeholder={{$dMax}} value={{$dateMax}}>
        </div>
      <hr>
      <h4>Trier :</h4>
      <div class="form-group">
          <select name="sort" class="form-control" selected="selectDsc">
          <!--<option value="title">de A à Z</option>
          <option value="newspapaer">par journaux</option>-->           <!-- Nécessite une configuration spéciale dans elasticsearch -->
          <option value="dateAsc" <?php if( isset($_GET['sort']) && 'dateAsc'==$_GET['sort']) echo 'selected'; ?> >du - au + récent</option>
          <option value="dateDsc" <?php if( isset($_GET['sort']) && 'dateDsc'==$_GET['sort']) echo 'selected'; ?> >du + au - récent</option>
          </select>
      </div>
      <hr>
    <button type="submit" class="btn btn-default">Recherche</button>
    </form>
@stop

@section('page_content')

	    <!-- Title -->
	    <div class="row">
		    <div class="col-lg-12">
		        <h3>Résultats de la recherche de "{{$text}}"</h3>
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
    <hr>
		</article>
	@endforeach
	@else
	    <p>Aucun résultat pour cette recherche.</p>
	@endif

@stop