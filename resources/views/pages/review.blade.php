@extends('app')

@section('page_content')

      <div class="row">
         <div class="col-md-2 col-lg-2 searchRow" >
          <h3>Critères de recherche</h3>
          <hr>
          <form class="form-vertical" action="recherche">
            <div class="form-group">
              <input type="text" name="text" class="form-control" id="search_input" placeholder="Rechercher" value="{{$text}}" required>
              <div class="checkbox"><label><input type="checkbox" name="regexp" value="true" <?php if( $regexp ) echo 'checked';?> > <small>Accepter les expressions régulières</small></label></div>
            </div>
            <div class="form-group">
              <select name="type" class="form-control">
                <option value="articles" <?= ($type=='articles')?'selected':'' ?>>Contenus des articles</option>
                <option value="newspaper" <?= ($type=='newspaper')?'selected':'' ?>>Journaux</option>
                <option value="titles" <?= ($type=='titles')?'selected':'' ?>>Titres des articles</option>
                <option value="review" <?= ($type=='review')?'selected':'' ?>>Revues de presse</option>
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
  		        <p>{{$result->total()}} occurrences trouvées ({{$result->took()}} ms)</p>
              <hr>
    		    </div>
    		  </div>
  	    <!-- /.row -->
      	@if(!$result->isEmpty())
      	@foreach ($result as $index => $onePage)
      	@if($index < 20)
      		<page>
            <div class="panel panel-default">
              <div class="panel-heading">
               <a href="revue/{{$onePage['_id']}}"> <h3 class="panel-title">{{$onePage['name']}}</h3></a>
              </div>
              <div class="panel-body">
                <p>{{$onePage['description']}}</p>
                <p>{{count($onePage['articles'])}} article(s)</p>
              </div>
            </div>
      		</page>
      	  <nav>
      	@endif
      	@endforeach
      	@if((count($result)==21 && $page==1) || ($page>1))
      	   <ul class="pager">
      	  @if($page>1)
      	     <li class="previous"><a href="<?= $builturl.($page-1) ?>">Précédent</a></li>
      	  @endif
      	  @if(count($result)==11)
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