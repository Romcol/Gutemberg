@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')

      <div class="row">
             <div class="col-md-3">
                        <form class="form-vertical" action="recherche">
      <div class="form-group">
        <input type="text" name="text" class="form-control" id="search_input" placeholder="Rechercher" value={{$text}} required>
      </div>
      <div class="form-group">
          <select name="type" class="form-control">
          <option value="articles" <?= ($type=='articles')?'selected':'' ?>>Contenu des articles</option>
          <!-- <option value="revues">Revues de presse</option> -->
          <option value="titles" <?= ($type=='titles')?'selected':'' ?>>Titres des articles</option>
          </select>
      </div>

      <hr>
      <h4>Filtres :</h4>
        <h5>Date :</h5>
        <div class="form-group">
          De <input type="date" name="dateMin" class="form-control" id="dateMin_input" placeholder="1845" value={{$dateMin}}>
        </div>
        <div class="form-group">
          à <input type="date" name="dateMax" class="form-control" id="dateMax_input" placeholder="1945" value={{$dateMax}}>
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
    <button type="submit" class="btn btn-primary">Recherche</button>
    </form>
            </div>
            
            <div class="col-md-9">
                
	    <!-- Title -->
	    <div class="row">
		    <div class="col-lg-12">
		        <h3>Résultats de la recherche pour "{{$text}}"</h3>
		    </div>
		</div>
	    <!-- /.row -->
	@if(!$articles->isEmpty())
	@foreach ($articles as $index => $article)
	@if($index < 10)
		<article>
		<h3>{{$article['Title']}}</h3>
		<div>
		<p>Journal : {{$article['TitleNewsPaper']}}</p>
		<p>Date : {{$article['Date']}}</p>
		<p>{{$article['Words']}}</p></div>
    <hr>
		</article>
	<nav>
	@endif
	@endforeach
	@if((count($articles)==11 && $page==1) || ($page>1))
	<ul class="pager">
	  @if($page>1)
	  <li class="previous"><a href="<?= $builturl.($page-1) ?>">Précédent</a></li>
	  @endif
	  @if(count($articles)==11)
	  <li class="next"><a href="<?= $builturl.($page+1) ?>">Suivant</a></li>
	  @endif
	</ul>
	@endif
</nav>
	@else
	    <p>Aucun résultat pour cette recherche.</p>
	@endif
            </div>
        </div>

@stop