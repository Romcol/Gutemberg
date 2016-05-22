@extends('app')

@section('page_content')

      <div class="row">
         <div class="col-md-2 col-lg-2 searchRow" >
          <h3>Critères de recherche</h3>
          <form class="form-vertical" action="recherche">
            <div class="form-group">
              <input type="hidden" name="text" value="">
              <input type="hidden" name="type" value="newspaper">
            </div>
            <h4>Filtres :</h4>
            <h5>Date :</h5>
            <div class="form-group">
              De <input name="dateMin" class="form-control" id="dateMin_input" placeholder="{{$defaultMin}}" value={{$dateMin}}>
            </div>
            <div class="form-group">
              à <input name="dateMax" class="form-control" id="dateMax_input" placeholder="{{$defaultMax}}" value={{$dateMax}}>
            </div>
            <hr>
            <h5>Journaux : </h5>
            <div class="form-group">
                <div class="input-group">
                  <select id="news">
                    <option value="null" disabled selected>Journaux</option>
                  </select>
                  <span class="input-group-btn"><button type="button" onclick="newNews()" id="tag_button" class="btn btn-default btn-sm" style="height:34px;">+</button></span>
                </div>
            </div>
            <div id="newsForm">
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
  		        <h3>Résultats de la recherche parmi les journaux</h3>
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
               <a href="<?= url('/visionneuse/page/'.$onePage['Id']); ?>"> <h3 class="panel-title">{{$onePage['Title']}}, {{$onePage['Date']}}</h3></a>
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
      	  @if(count($pages)==21)
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

    function resetNews(){
      $("#newsForm").text('');
      newsNumber = 0;
      for(var i=0; i<news.length ; i++){
        $("#newsForm").append('<input type="hidden" name="news['+newsNumber+']" value="'+news[i]+'"> <span class="btn btn-default" onmouseenter="tagMouseEnter(this, true)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+news[i]+'</span></span>');
        newsNumber++;
      }
    }

    var savedNews = <?php echo $savedNewsPaper; ?> ;
    var news = <?php echo $news; ?> ;
    var newsNumber = 0;

    for(var i = 0; i<savedNews.length; i++){
      $('#news').append('<option value="'+savedNews[i]+'">'+savedNews[i]+'</option>');
    }


    function newNews(entry = 'undefined'){

      if( entry = 'undefined') entry = $('#news').val();

      if( !news.includes(entry) ){

        $("#newsForm").append('<input type="hidden" name="news['+newsNumber+']" value="'+entry+'"> <span class="btn btn-default" onmouseenter="tagMouseEnter(this, true)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+entry+'</span></span>');
        news[newsNumber] = entry;
        newsNumber++;

      }

      $('#news').val("null");
    }

    function closeNews(elemnt){
        var removedNew = $(elemnt).parent().children("span").text();
        
        var index = news.indexOf(removedNew);
        if (index > -1) {
            news.splice(index, 1);
        }

        resetNews();

      }

      function tagMouseEnter(elemnt, news){
        if( news) {
          $(elemnt).append(' <a onclick="closeNews(this)">X</a>');
        }else{
          $(elemnt).append(' <a onclick="closeTag(this)">X</a>');
        }
      }

      function tagMouseLeave(elemnt){
        $(elemnt).find("a").remove();
      }

  </script>
@stop