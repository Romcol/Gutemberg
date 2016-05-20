@extends('app')

@section('page_content')

      <div class="row">
         <div class="col-md-2 col-lg-2 searchRow" >
          <h3>Critères de recherche</h3>
          <hr>
          <form class="form-vertical" action="recherche">
            <div class="form-group">
              <input type="text" name="text" class="form-control" id="search_input" placeholder="Rechercher" value="{{$text}}">
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
            <h4>Filtres :</h4>
            <h5>Date :</h5>
            <div class="form-group">
              De <input name="dateMin" class="form-control" id="dateMin_input" placeholder="{{$defaultMin}}" value={{$dateMin}}>
            </div>
            <div class="form-group">
              à <input name="dateMax" class="form-control" id="dateMax_input" placeholder="{{$defaultMax}}" value={{$dateMax}}>
            </div>
            <h5>Tags : </h5>
            <div class="form-group">
                <div class="input-group">
                <input class="form-control" id="tags" placeholder="Ajouter un tag"> <span class="input-group-btn"><button type="button" onclick="newTag()" id="tag_button" class="btn btn-default btn-sm" style="height:34px;">+</button></span>
                </div>
            </div>
            <div id="tagForm">
            </div>
            <hr>
            <h4>Trier :</h4>
            <div class="form-group">
              <select name="sort" class="form-control" selected="selectDsc">
                <option <?php if( !isset($_GET['sort'])) echo 'selected'; ?> disabled >Choisir un critère de tri</option>
                <option value="dateAsc" <?php if( isset($_GET['sort']) && 'dateAsc'==$_GET['sort']) echo 'selected'; ?> >du - au + récent</option>
                <option value="dateDsc" <?php if( isset($_GET['sort']) && 'dateDsc'==$_GET['sort']) echo 'selected'; ?> >du + au - récent</option>
                <option value="viewsDsc" <?php if( isset($_GET['sort']) && 'viewsDsc'==$_GET['sort']) echo 'selected'; ?> >les + vus</option>
              </select>
            </div>
            <hr>
            <button type="submit" class="btn btn-default">Recherche</button>
          </form>
        </div>
            
        <div class="col-md-8 col-md-offset-1 col-lg-8 col-lg-offset-1">
                  
    	    <!-- Title -->
    	    <div class="row">
  		      <div>
  		        <h3>Résultats de la recherche pour <?php if( $text == '') echo 'tous les articles'; else echo '"'.$text.'"'; ?> </h3>
  		        <p>{{$articles->total()}} occurrences trouvées ({{$articles->took()}} ms)</p>
              <hr>
    		    </div>
    		  </div>
  	    <!-- /.row -->
      	@if(!$articles->isEmpty())
      	@foreach ($articles as $index => $article)
      	@if($index < 10)
      		<article>
            <div class="panel panel-default">
              <div class="panel-heading">
               <a href="visionneuse?id={{$article['IdPage']}}&article={{$article['id']}}&search={{$text}}"> <h3 class="panel-title">{{$article['TitleNewsPaper']}}, {{$article['Date']}}</h3></a>
              </div>
              <div class="panel-body">
                <B class="title">@if($article->highlight('Title')) {!! $article->highlight('Title') !!} @else {{$article['Title']}} @endif</B>
                <p style="margin-top:20px">{!! $article['Words'] !!}</p>
                <p>
                @foreach ($article['Tags'] as $tag)
                <span class="tag"> {{$tag}}</span>
                @endforeach
                </p>
              </div>
            </div>
      		</article>
      	@endif
      	@endforeach
        <nav>
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

@section('scripts')
  <script type="text/javascript">

    function resetTag(){
      $("#tagForm").text('');
      tagNumber = 0;
      for(var i=0; i<tags.length ; i++){
        $("#tagForm").append('<input type="hidden" name="tags['+tagNumber+']" value="'+tags[i]+'"> <span class="btn btn-default" onmouseenter="tagMouseEnter(this)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+tags[i]+'</span></span>');
        tagNumber++;
      }
    }

    var savedTags = <?php echo $savedTags; ?> ;
    var tags = <?php echo $tags; ?> ;
    var tagNumber = 0;

    resetTag();

    function newTag(tag = 'undefined'){

      if( tag = 'undefined') tag = $('#tags').val();

      if( tag != '' && savedTags.includes(tag) && !tags.includes(tag)){

        $("#tagForm").append('<input type="hidden" name="tags['+tagNumber+']" value="'+tag+'"> <span class="btn btn-default" onmouseenter="tagMouseEnter(this)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+tag+'</span></span>');
        tags[tagNumber] = tag;
        tagNumber++;

      }

      $('#tags').val('');
    }

    function closeTag(elemnt){
        var removedTag = $(elemnt).parent().children("span").text();
        
        var index = tags.indexOf(removedTag);
        if (index > -1) {
            tags.splice(index, 1);
        }

        resetTag();

      }

      function tagMouseEnter(elemnt){
        $(elemnt).append(' <a onclick="closeTag(this)">X</a>');
      }

      function tagMouseLeave(elemnt){
        $(elemnt).find("a").remove();
      }

      $(window).load(function() {

      var availableTags = savedTags;
      $("#tags").autocomplete({
          source: availableTags,
          minLength: 0,
          select: function( event, ui ) { 
            newTag(ui.item.value);
          }
      });

      });
  </script>
@stop