@extends('app')

@section('page_content')

      <div class="row">
         <div class="col-md-2 col-lg-2 searchRow" >
          <h3>Critères de recherche</h3>
          <hr>
          <form class="form-vertical" action="recherche">
            <div class="form-group">
              <input type="text" name="text" class="form-control" id="search_input" placeholder="Rechercher" value="{{$text}}" required>
            </div>
            <div class="form-group">
              <select name="type" class="form-control">
                <option value="articles" <?= ($type=='articles')?'selected':'' ?>>Contenus des articles</option>
                <option value="newspaper" <?= ($type=='newspaper')?'selected':'' ?>>Journaux</option>
                <option value="titles" <?= ($type=='titles')?'selected':'' ?>>Titres des articles</option>
              </select>
            </div>

            <hr>
            <h4>Filtres :</h4>
            <h5>Date :</h5>
            <div class="form-group">
              De <input name="dateMin" class="form-control" id="dateMin_input" placeholder="{{$defaultMin}}" value={{$dateMin}}>
            </div>
            <div class="form-group">
              à <input name="dateMax" class="form-control" id="dateMax_input" placeholder="{{$defaultMax}}" value={{$dateMax}}>
            </div>
            <hr>
            <h4>Trier :</h4>
            <div class="form-group">
              <select name="sort" class="form-control" selected="selectDsc">
                <option <?php if( !isset($_GET['sort'])) echo 'selected'; ?> disabled >Choisir un critère de tri</option>
                <option value="dateAsc" <?php if( isset($_GET['sort']) && 'dateAsc'==$_GET['sort']) echo 'selected'; ?> >du - au + récent</option>
                <option value="dateDsc" <?php if( isset($_GET['sort']) && 'dateDsc'==$_GET['sort']) echo 'selected'; ?> >du + au - récent</option>
              </select>
            </div>
            <hr>
            <button type="submit" class="btn btn-default">Recherche</button>
          </form>
        </div>
            
        <div class="col-md-8 col-md-offset-1 col-lg-8 col-lg-offset-1">
                  
    	    <!-- Title -->
    	    <div class="row">
  		      <div class="col-lg-12">
  		        <h3>Résultats de la recherche pour "{{$text}}"</h3>
  		        <p>{{$pages->total()}} occurrences trouvées ({{$pages->took()}} ms)</p>
              <hr>
    		    </div>
    		  </div>
  	    <!-- /.row -->
      	@if(!$pages->isEmpty())
      	@foreach ($pages as $index => $onePage)
      	@if($index < 20)
      		<page>
            <div class="panel panel-default">
              <div class="panel-heading">
               <a href="visionneuse?id={{$onePage['Id']}}"> <h3 class="panel-title">{{$onePage['Title']}}, {{$onePage['Date']}}</h3></a>
              </div>
            </div>
      		</page>
      	  <nav>
      	@endif
      	@endforeach
      	@if((count($pages)==21 && $page==1) || ($page>1))
      	   <ul class="pager">
      	  @if($page>1)
      	     <li class="previous"><a href="<?= $builturl.($page-1) ?>">Précédent</a></li>
      	  @endif
      	  @if(count($pages)==11)
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