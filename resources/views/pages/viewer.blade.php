@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/viewer.css') ?>" type="text/css"> 
@stop

@section('page_content')

<a id="backSearch" href="{{$searchUri}}" <?php if( $searchUri == null ) echo 'style="display: none"';?> class="btn btn-default btn-sm"><img src="<?= asset('resources/viewer/back-search.svg') ?>" class="viewer-icon"/> <strong>Retour à la recherche</strong></a>

<div class="row">
	<div id="pageInfo">
		<div>
			<div id="hideInfo">
				<img src="<?= asset('resources/viewer/back.svg') ?>" /> Masquer
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="section">
			<h4>Page</h4>
			<hr>
			<strong>Titre :</strong> <?= $page['Title'] ?> <br>
			<strong>Date :</strong> <?= $page['Date'] ?> <br>
			<strong>Page :</strong> <?= $page['NumberPage'] ?> <br>

		</div>
		<div class="section">
			<h4>Articles de la page</h4>
			<hr>
			<div id="pageArticlesList">
				@foreach($page['Articles'] as $idx => $art)
				<p id="articleList" onclick="selectArticle('{{$art['IdArticle']}}', true)"><strong>Article {{$idx+1}} :</strong> <?php if( strlen($art['Title']) > 90) echo substr($art['Title'], 0, 89).'...' ; else echo $art['Title']; ?> </p>
				<p id="{{$art['IdArticle']}}" class="occurrenceNumber"></p>
				@if( count($art['PictureKeys']) != 0)
				<p class="pictureKeys">
					<strong>Légendes :</strong>
					<ul>
						@foreach($art['PictureKeys'] as $pkeys)
				  		<li>{{$pkeys}}</li>
						@endforeach
					</ul> 
				</p>
				@endif
				@endforeach
			</div>
		</div>
		<div id="currentArticle" style="display:none;" class="section">
			@if(Auth::user())
			<img src="<?= asset("resources/viewer/empty-star.svg") ?>" id="favorite" onClick="addFavorite()" style="float: right; width: 30px; cursor: pointer;"/>
			@endif
			<h4>Article</h4>
			<hr>
			<div id="infoCurrentArticle">
				<strong>Titre :</strong> <span id="currentTitle"></span><br>
				<strong>Vues :</strong> <span id="currentViews"></span><br>
				<p><strong>Tags :</strong><br>
					@if( !Auth::guest() )
		            <div class="form-group">
		                <div class="input-group">
		                <input class="form-control" id="tags" placeholder="Ajouter un tag"> <span class="input-group-btn"><button type="button" onclick="newTag()" id="tag_button" class="btn btn-default btn-sm" style="height:34px;">+</button></span>
		                </div>
		            </div>
					@else
					<div> <center style="color: grey; ">Connectez-vous pour pouvoir ajouter ou supprimer des tags</center> </div>
					@endif
					<span id="currentTags"></span>
				</p>
				@if( !Auth::guest() )
				<div id="addpressreview">
					<button type="button" id="showAddReview" onclick="displayAddReview()" class="btn btn-default btn-sm">Ajouter à une revue de presse</button>
					<div id="reviewPart">
						<div id="choice">
						    <form role="form">
						    <div class="form-group">
						    <div class="input-group">
								      	<select id="listMyReview" class="form-control">
								      		<option selected disabled >Vide</option>
									  	</select>
									  	<span class="input-group-btn"><button type="button" id="addCreated" onclick="selectCreated()" class="btn btn-default btn-sm disabled">+</button></span>
							</div>
							</div>
							 </form>
						</div>
						<div id="ReviewList"></div>
					</div>
				</div>
				@endif
				<div class="form-group">
					<strong>URL : </strong><input id="currentUrl" />
				</div>
			</div>
		</div>
	</div>
	<div id="infoHidden">
		<img src="<?= asset('resources/viewer/next.svg'); ?>"/>
	</div>
	<div id="viewer" class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	    <div id="toolbarDiv" class="toolbar">
		    <form class="form-inline" style='float:left;margin:10px 0 10px 20px'>
		   			<a id="home" href="#home"><img src="<?= asset('resources/viewer/home.svg') ?>" alt="Accueil" class="viewer-icon"/></a> 
		            | <a id="full-page" href="#full-page"><img src="<?= asset('resources/viewer/fullscreen.svg') ?>" alt="Plein ecran" class="viewer-icon"/></a>
		            |<a id="zoom-in" href="#zoom-in"><img src="<?= asset('resources/viewer/zoom_in.svg') ?>" alt="Zoom plus" class="viewer-icon"/></a> 
		            | <a id="zoom-out" href="#zoom-out"><img src="<?= asset('resources/viewer/zoom_out.svg') ?>" alt="Zoom moins" class="viewer-icon"/></a>
		            | <button id="zoomOnRead" type="button" class="btn btn-default btn-sm">Mode lecture</button>
		            | <button id="zoomOnArticle" class="btn btn-default btn-sm">Zoomer sur l'article</button>
		            | <button id="toggle-overlay" class="btn btn-default btn-sm">Désactiver les calques</button> 
		    </form>
		    <form class="form-inline" style='float:right;margin:10px 20px 10px 0'>
		          	<div class="form-group" style="display:inline-block;">
					    <input id="search_input" onchange="newSearch()">
					    <button type="button" id="search_button" class="btn btn-default btn-sm"><img src="<?= asset("resources/viewer/file.png") ?>" alt="Occurrence" class="viewer-icon"/> Recherche</button>   <span id="occurrence"></span>
					  </div>
					<a id="otherPage" onclick="previousKeyword()" ><img src="<?= asset('resources/viewer/back.png') ?>" alt="Flèche gauche" class="viewer-icon"/></a> 
		            <a id="otherPage" onclick="nextKeyword()" ><img src="<?= asset('resources/viewer/next.png') ?>" alt="Flèche droite" class="viewer-icon"/></a>
		    </form>
	    </div>
	    <div id="ourOpenseadragon" class="openseadragon"></div>
	    <div>
		    <ul class="pager">
				<li class="previous">
					<a id="otherPage"  onclick="previousPage()" <?php if( !isset($page['PreviousPage'])) echo 'class="btn btn-default btn-xs disabled"' ; else echo 'class="btn btn-default btn-xs"'; ?>><img src="<?= asset('resources/viewer/back_pager.png') ?>" class="viewer-icon" alt="Flèche gauche" /> <strong>Page précédente</strong></a>
				</li>
				<li class="next">
					<a id="otherPage" onclick="nextPage()" <?php if( !isset($page['NextPage'])) echo 'class="btn btn-default btn-xs disabled"' ; else echo 'class="btn btn-default btn-xs"'; ?>><strong>Page suivante</strong> <img src="<?= asset('resources/viewer/next_pager.png') ?>" class="viewer-icon" alt="Flèche droite"/></a>
				</li>
			</ul>
	    </div>
    </div>
    <div id="pageGuide">
    	<div>
			<div id="hideGuide">
				Masquer <img src="<?= asset('resources/viewer/next.svg') ?>" />
			</div>
			<div style="clear:both"></div>
		</div>
		<div class="section">
			<h4>Articles proches</h4>
			<hr>
			<div id="closeArticlesList">

			</div>
		</div>
	</div>
	<div id="guideHidden">
		<img src="<?= asset('resources/viewer/back.svg'); ?>"/>
	</div>
</div>
@stop

@section('scripts')

		<script src="<?= asset('/openseadragon/openseadragon.min.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selection.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionoverlay.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionrect.js') ?>"></script>
		<script src="<?= asset('/openseadragon/openseadragonselection.js') ?>"></script>

		<!-- Initialization script -->
		<script type="text/javascript">

			function updateCurrentArticle(article){
				if(article != null)
				{
					$("#currentTitle").text(article.Title);
					$("#currentViews").text(article.Views);
					$("#currentTags").text('');
					if( article.Tags != undefined){
						for(var i = 0; i < article.Tags.length; i++){
							$("#currentTags").append('<span class="btn btn-default" onmouseenter="tagMouseEnter(this)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+article.Tags[i]+'</span></span>');
						}
					}
					var url = "<?= url('/visionneuse'); ?>";
					$("#currentUrl").attr('value', url+'/page/'+article.IdPage+'/article/'+article._id);
					$("#currentArticle").show();

					$("#favorite").attr('src', '<?= asset("resources/viewer/empty-star.svg") ?>');
					$("#favorite").attr('onClick', 'addFavorite()');
					if(auth){
						for(var i=0; i<favorites.length; i++){
							if( favorites[i].id == article._id) {
								$("#favorite").attr('src', '<?= asset("resources/viewer/star.svg") ?>');
								$("#favorite").attr('onClick', 'removeFavorite()');
							}
						}
					}
				}
				else{
					$("#currentArticle").hide();
				}
			}

			$('#reviewPart').hide();

			var savedTags = <?php echo $savedTags; ?> ;

			var auth = <?php if(Auth::guest()) echo 'false'; else echo 'true';?> ;

			<?php
			if(Auth::user())
			{
			?>
				if(auth){
					var createdReviews = <?php echo json_encode(Auth::user()->createdReviews); ?> ;
					var contributedReviews = <?php echo json_encode(Auth::user()->contribReviews); ?> ;
					var favorites = <?php echo json_encode(Auth::user()->favoriteArticles); ?> ;
				}
			<?php
			}
			?>

			var zoom = true;
			var toggle = true;
			var filename = '<?php echo $filename ?>';
			var image = filename != 'default.dzi';

			var keywords = '<?php echo $keywords; ?>' ;

			$('#search_input').val(keywords);
			var page = <?= $page; ?>;
			var articles = page.Articles;

			var overlays = [];

			if(image){
				for(var i = 0; i< articles.length; i++) {
					var number  = Math.floor(Math.random()*10);

					if( articles[i].TitleCoord != null){
						overlays.push({
								id: 'i:'+i+' '+'Title',
						        px: articles[i].TitleCoord[0], 
						        py: articles[i].TitleCoord[1],
						        width: articles[i].TitleCoord[2] - articles[i].TitleCoord[0], 
						        height: articles[i].TitleCoord[3] - articles[i].TitleCoord[1],
						        className: 'overlay'+number
						});
					}

					for(var j = 0; j< articles[i].Coord.length; j++){
						overlays.push({
							id: 'i:'+i+' '+'j:'+j,
					        px: articles[i].Coord[j][0], 
					        py: articles[i].Coord[j][1],
					        width: articles[i].Coord[j][2] - articles[i].Coord[j][0], 
					        height: articles[i].Coord[j][3] - articles[i].Coord[j][1],
					        className: 'overlay'+number
						});
					}
				}

				var overlaysSlt = [];
				@if($article)
				var article =  <?= $article; ?> ;
				@else
				var article = null;
				@endif
				updateCurrentArticle(article);
				if( article != null ){
					updateCurrentArticle(article);

					if( article.TitleCoord.length != 0){
						overlaysSlt.push({
								id: 'overlaySelectedTitle',
						        px: article.TitleCoord[0], 
						        py: article.TitleCoord[1],
						        width: article.TitleCoord[2] - article.TitleCoord[0], 
						        height: article.TitleCoord[3] - article.TitleCoord[1],
						        className: 'overlayArt'
						});
					}

					for( var i = 0; i<article.Coord.length; i++){
						overlaysSlt.push({
							id: 'overlaySelected'+i,
					        px: article.Coord[i][0], 
					        py: article.Coord[i][1],
					        width: article.Coord[i][2] - article.Coord[i][0], 
					        height: article.Coord[i][3] - article.Coord[i][1],
					        className: 'overlayArt'
						});
					}

					for( var j=0; j<article.Close.length; j++){
						var shortTitle = article.Close[j].Title;
                		if( shortTitle.length > 90 ) shortTitle = shortTitle.substring(0, 89)+"...";
                        $('#closeArticlesList').append('<p id="articleList" class="closeArticle" onClick=\'closeArticle("'+article.Close[j].IdPage+'","'+article.Close[j]._id+'")\'><strong>'+shortTitle+', </strong>'+article.Close[j].TitleNewsPaper+', '+article.Close[j].Date+'</p>');
                    }
				}

				var searchArray = <?php echo $searchedKeywords; ?>;
				var search = searchArray[0];
				var occurrence = searchArray[1];
				var overlaysKwd = [];
				var iterator = 0;

				function newImage() {
			        var img = document.createElement("img");
			        img.src = "<?= asset('resources/viewer/Red_Arrow_Right.svg') ?>";
			        img.width = 20;
			        return img;
			    }

				if( search != undefined && search.length != 0){
					for( var i=0; i< search.length ; i++){
						var elt = {
							id: newImage(),
					        px: search[i][0], 
					        py: (search[i][3] - search[i][1])/2 + search[i][1],
					        placement: 'RIGHT'
						};
						overlaysKwd.push(elt);
					}

					//Display occurrence number in articles list
					$.each(occurrence, function (index, value) {
					    if( value != 0 ){
					    	$('#'+index).text(+value+' occurrence(s) de "'+$('#search_input').val()+'"');
					    	$('#'+index).prepend('<img src="<?= asset("resources/viewer/file.png") ?>" alt="Occurrence" height="20px"/> ');
					    } 
					});
					if( search.length > 1 ){
						$('#occurrence').text(search.length+' occurrences');
					}else{
						$('#occurrence').text(search.length+' occurrence');
					}
				}


			}else{
				$('#toggle-overlay').remove();
				$('#zoomOnArticle').remove();
				$('#zoomOnRead').remove();
				$('#search_form').remove();
			}

			var typeImage = <?php if($typeImage) echo 'true'; else echo 'false'; ?> ;
			if( typeImage){
				var tileSource = {
			        type: 'image',
			        url:  '<?= parse_url(url('images'))['path'].'/'; ?>'+filename,
			        buildPyramid: false
				}
			}else{
				var tileSource = '<?= parse_url(url('images'))['path'].'/'; ?>'+filename;
			}

			var viewer = OpenSeadragon({
				id: "ourOpenseadragon",
				showRotationControl: true,
			    showNavigator:  true,
				prefixUrl: "/openseadragon/images/",
		        toolbar:        "toolbarDiv",
		        zoomInButton:   "zoom-in",
		        zoomOutButton:  "zoom-out",
		        homeButton:     "home",
		        fullPageButton: "full-page",
		        nextButton:     "next",
		        previousButton: "previous",
		        showNavigator:  true,
		        sequenceMode: true,
		        visibilityRatio: 1.0,
		        constrainDuringPan: true,
				tileSources:tileSource,
				//showReferenceStrip: true,
				//referenceStripScroll: 'vertical',
				overlays: overlays.concat(overlaysKwd, overlaysSlt),

			});

			viewer.gestureSettingsMouse.clickToZoom = false;
			viewer.gestureSettingsMouse.dblClickToZoom = true;

			var selection = viewer.selection({
				element:                 null, 
				showSelectionControl:    true, 
				toggleButton:            null, 
				showConfirmDenyButtons:  true,
				styleConfirmDenyButtons: true,
				returnPixelCoordinates:  true,
				keyboardShortcut:        'c',
				rect:                    null, 
				startRotated:            false, 
				startRotatedHeight:      0.1, 
				restrictToImage:         false, 
				onSelection:             function(rect) {},
				prefixUrl:               null, 
				navImages:               { 
					selection: {
						REST:   '/selection_rest.png',
						GROUP:  '/selection_grouphover.png',
						HOVER:  '/selection_hover.png',
						DOWN:   '/selection_pressed.png'
					},
					selectionConfirm: {
						REST:   '/selection_confirm_rest.png',
						GROUP:  '/selection_confirm_grouphover.png',
						HOVER:  '/selection_confirm_hover.png',
						DOWN:   '/selection_confirm_pressed.png'
					},
					selectionCancel: {
						REST:   '/selection_cancel_rest.png',
						GROUP:  '/selection_cancel_grouphover.png',
						HOVER:  '/selection_cancel_hover.png',
						DOWN:   '/selection_cancel_pressed.png'
					},
				}
			});
			//selection.enable();
			//selection.toggleState();

		</script>
		<!-- End of initialization script -->



		<!-- Functions definition -->
		<script type="text/javascript">

			function zoomOnArticle(articleparam){

				if( articleparam != null && zoom  && image){
					if( articleparam.TitleCoord.length != 0){
						var px = articleparam.TitleCoord[0];
						var py = articleparam.TitleCoord[1];
						var ppx = articleparam.TitleCoord[2];
						var ppy = articleparam.TitleCoord[3];
					}else{
						var px = 10000;
						var py = 10000;
						var ppx = 0;
						var ppy = 0;
					}

					for( var i=0; i<articleparam.Coord.length; i++){
						px = Math.min(px, articleparam.Coord[i][0]);
						py = Math.min(py, articleparam.Coord[i][1]);
						ppx = Math.max(ppx, articleparam.Coord[i][2]);
						ppy = Math.max(ppy, articleparam.Coord[i][3]);
					}

					var pxr = px - 100;
					var pyr = py - 100;
					var ppxr = ppx - pxr + 100;
					var ppyr = ppy - pyr + 100;


					var point = new OpenSeadragon.Rect(pxr, pyr, ppxr, ppyr);
					point = viewer.viewport.imageToViewportRectangle(point);
					viewer.viewport.fitBounds(point, false);
				}
			}

			function zoomOnRead(articleparam){

				if( articleparam != null && image){
					var px = articleparam.Coord[0][0];
					var py = articleparam.Coord[0][1];
					var ppx = articleparam.Coord[0][2];
					var ppy = articleparam.Coord[0][3];

					for(var i = 1; i<article.Coord.length; i++){
						if( (articleparam.Coord[i][0] <= px && articleparam.Coord[i][1] <= py) ||
							(articleparam.Coord[i][0] <= px && Math.abs(articleparam.Coord[i][1] - py) < 150) ||
							( Math.abs(articleparam.Coord[i][0] - px) < 150 && articleparam.Coord[i][1] <= py) )
						{
							var px = articleparam.Coord[i][0];
							var py = articleparam.Coord[i][1];
							var ppx = articleparam.Coord[i][2];
							var ppy = articleparam.Coord[i][3];
						}
					}

					var pxr = px - 300;
					var pyr = py - 50;
					var ppxr = ppx - pxr + 300;
					var ppyr = 600;


					var point = new OpenSeadragon.Rect(pxr, pyr, ppxr, ppyr);
					point = viewer.viewport.imageToViewportRectangle(point);
					viewer.viewport.fitBounds(point, false);
				}
			}

			function CoordToNewArticleId(px, py){
				for(var i = 0; i< articles.length; i++) {
					for(var j = 0; j< articles[i].Coord.length; j++){
						if( px >= articles[i].Coord[j][0] && px <= articles[i].Coord[j][2]){
							if( py >= articles[i].Coord[j][1] && py <= articles[i].Coord[j][3]){
								if( article == null || articles[i].IdArticle != article._id){
									return articles[i].IdArticle;
								}else{
									return null;
								}
							}
						}
					}
				}

				return null;
			}

			function activateZoom(){
				zoom = !zoom;
			}

			function removeSelectedOverlays(){

				viewer.removeOverlay('overlaySelectedTitle');
				for( var j=0; j<overlaysSlt.length; j++){
					viewer.removeOverlay('overlaySelected'+j);
				}

				overlaysSlt = [];

			}

			function addSelectedOverlays(articleparam){

				if( articleparam.TitleCoord.length != 0){
					var title = {
							id: 'overlaySelectedTitle',
					        px: articleparam.TitleCoord[0], 
					        py: articleparam.TitleCoord[1],
					        width: articleparam.TitleCoord[2] - articleparam.TitleCoord[0], 
					        height: articleparam.TitleCoord[3] - articleparam.TitleCoord[1],
					        className: 'overlayArt'
					};

					overlaysSlt.push(title);

					if(toggle) viewer.addOverlay(title);
				}

				for( var i = 0; i<articleparam.Coord.length; i++){
					var elt = {
						id: 'overlaySelected'+i,
				        px: articleparam.Coord[i][0], 
				        py: articleparam.Coord[i][1],
				        width: articleparam.Coord[i][2] - articleparam.Coord[i][0], 
				        height: articleparam.Coord[i][3] - articleparam.Coord[i][1],
				        className: 'overlayArt'
					};

					overlaysSlt.push(elt);

					if(toggle) viewer.addOverlay(elt);
				}
			}

			function removeKeywordOverlays(){

				viewer.clearOverlays();
				if (toggle) {
					var allOverlays = overlays.concat(overlaysSlt);

					for(var i = 0; i<allOverlays.length; i++){
						viewer.addOverlay(allOverlays[i]);
					}
				}

				overlaysKwd = [];

			}

			function addKeywordOverlays(searchparam){
				for( var i = 0; i<searchparam.length; i++){

					var elt = {
						id: newImage(),
				        px: search[i][0], 
				        py: (search[i][3] - search[i][1])/2 + search[i][1],
				        placement: 'RIGHT'
					};

					overlaysKwd.push(elt);

					viewer.addOverlay(elt);
				}
			}

			//Display occurrence number in articles list
			function displayOccurrenceArticle(occurrenceParam){

				$.each(occurrence, function (index, value) {
				    if( value != 0 ){
				    	$('#'+index).text(+value+' occurrence(s) de "'+$('#search_input').val()+'"');
				    	$('#'+index).prepend('<img src="<?= asset("resources/viewer/file.png") ?>" alt="Occurrence" height="20px"/> ');
				    }
				});
			}

			function newSearch(){
				var keywd = $('#search_input').val();

				removeKeywordOverlays();
				$('.occurrenceNumber').text('');

				if( keywd != ''){

					$.get(

					    '<?= url('/').'/'; ?>'+'newSearch', // Le fichier cible côté serveur.

					    {
					    	id: page._id,
					    	search: keywd
					    },

					    function(data){

				   			search = data[0];
				   			occurrence = data[1];
							addKeywordOverlays(search);
							if( search.length > 1 ){
								$('#occurrence').text(search.length+' occurrences');
							}else{
								$('#occurrence').text(search.length+' occurrence');
							}
							iterator = 0;
							displayOccurrenceArticle(occurrence);

						}

					);


				}
			}

			function updateCloseArticles(param){

                $('.closeArticle').remove();

                for( var j=0; j<param.Close.length; j++){
                	var shortTitle = article.Close[j].Title;
                	if( shortTitle.length > 90 ) shortTitle = shortTitle.substring(0, 89)+"...";
                    $('#closeArticlesList').append('<p id="articleList" class="closeArticle" onClick=\'closeArticle("'+article.Close[j].IdPage+'","'+article.Close[j]._id+'")\'><strong>'+shortTitle+', </strong>'+article.Close[j].TitleNewsPaper+', '+article.Close[j].Date+'</p>');
                }
            }

			function selectArticle(idArticle, zoomBool){

				if( idArticle != null){
					$.get(

					    '<?= url('/').'/'; ?>'+'changeArticle', // Le fichier cible côté serveur.

					    {
					    	article: idArticle
					    },

					    function(data){
				   			article = data;
				   			updateCurrentArticle(article);
				   			updateCloseArticles(article);
				   			if(zoomBool) zoomOnArticle(article);
				   			removeSelectedOverlays();
							addSelectedOverlays(article);

						}

					);

				}
			}

			function previousPage(){
				var idPage = page.PreviousPage;
				var keywds = $('#search_input').val();
				var link = '<?= url('visionneuse').'/page/'; ?>'+idPage;

				if(keywds != '')
				{
					link += "/search/"+keywds;
				}

				window.location.href = link;
			}

			function nextPage(){
				var idPage = page.NextPage;
				var keywds = $('#search_input').val();
				var link = '<?= url('visionneuse').'/page/'; ?>'+idPage;

				if(keywds != '')
				{
					link += "/search/"+keywds;
				}

				window.location.href = link;
			}

			function closeArticle(parPage, parArticle){
				var keywds = $('#search_input').val();
				var link = "visionneuse/page/"+parPage+"/article/"+parArticle;

				if(keywds != '')
				{
					link += "&search="+keywds;
				}

				window.location.href = link;
			}

			function previousKeyword(){

				if( overlaysKwd.length != 0 ){
					var it = 0;
					if( iterator <= 1){
						it = overlaysKwd.length - 2 + iterator;
					}else{
						it = iterator - 2;
					}
					var coordX = overlaysKwd[it].px;
					var coordY = overlaysKwd[it].py;


					//zoom on position
					var rect = new OpenSeadragon.Rect(coordX - 600, coordY - 300, 1800, 600);
					rect = viewer.viewport.imageToViewportRectangle(rect);
					viewer.viewport.fitBounds(rect, false);

					iterator-= 1 ;
					if( iterator < 0) iterator = overlaysKwd.length-1;
				}

			}

			function nextKeyword(){
				if( overlaysKwd.length != 0 ){
					var coordX = overlaysKwd[iterator].px;
					var coordY = overlaysKwd[iterator].py;

					//zoom on position
					var rect = new OpenSeadragon.Rect(coordX - 600, coordY - 300, 1800, 600);
					rect = viewer.viewport.imageToViewportRectangle(rect);
					viewer.viewport.fitBounds(rect, false);

					iterator+= 1 ;
					if( iterator >= overlaysKwd.length) iterator = 0;
				}

			}


			function newTag(tag = 'undefined'){
				if( tag = 'undefined') tag = $('#tags').val();

				if( tag != '' && !article.Tags.includes(tag)){

					$("#currentTags").append('<span class="btn btn-default" onmouseenter="tagMouseEnter(this)" onmouseleave="tagMouseLeave(this)" class="tag"><span>'+tag+'</span></span>');

					if( !savedTags.includes(tag)) savedTags.push(tag);

					$.get(

					    '<?= url('/').'/'; ?>'+'newTag', // Le fichier cible côté serveur.

					    {
					    	article: article._id,
					    	tag: tag
					    },

					    function(data){

						}

					);

				}

				$('#tags').val('');
			}

			function removeTag(remTag){

				if( remTag != '' && article.Tags.includes(remTag)){

					$.get(

					    '<?= url('/').'/'; ?>'+'removeTag', // Le fichier cible côté serveur.

					    {
					    	article: article._id,
					    	tag: remTag
					    },

					    function(data){

						}

					);

					var index = article.Tags.indexOf(remTag);
					if (index > -1) {
					    article.Tags.splice(index, 1);
					}

				}

			}

			function closeTag(elemnt){
				var removedTag = $(elemnt).parent().find("span").text();
				removeTag(removedTag);
				$(elemnt).parent().remove();
			}

			function tagMouseEnter(elemnt){
				if(auth){
					$(elemnt).append(' <a onclick="closeTag(this)">X</a>');
				}
			}

			function tagMouseLeave(elemnt){
				$(elemnt).find("a").remove();
			}

			function existInReviews(idReview){
				for(var i=0; i<article.Reviews.length; i++){
					if( idReview == article.Reviews[i]._id) return true;
				}	

				return false;
			}

			function existInCreatedReviews(idReview){
				for(var i=0; i<createdReviews.length; i++){
					if( idReview == createdReviews[i]._id) return true;
				}	

				return false;
			}



			function displayAddReview(){

				$('#showAddReview').hide();
				$("#reviewPart").show();

				if( createdReviews.length != 0 && contributedReviews.length != 0) $('#listMyReview').text('');

				if( createdReviews.length != 0){
					$('#addCreated').removeClass('disabled');

					for(var i=0; i<createdReviews.length; i++){
						if( !existInReviews(createdReviews[i]._id) ){
							$('#listMyReview').append('<option type="create" value='+i+'>'+createdReviews[i].name+'</option>');
						}else{
							$('#listMyReview').append('<option type="create" value='+i+'>'+createdReviews[i].name+' (déjà présent)</option>');
						}
					}
				}

				if( contributedReviews.length != 0){

					for(var i=0; i<contributedReviews.length; i++){
						if( !existInCreatedReviews(contributedReviews[i]._id) ){
							if( !existInReviews(contributedReviews[i]._id) ){
								$('#listMyReview').append('<option type="contrib" value='+i+'>'+contributedReviews[i].name+'</option>');
							}else{
								$('#listMyReview').append('<option type="contrib" value='+i+'>'+contributedReviews[i].name+' (déjà présent)</option>');
							}
						}
					}
				}

			}


			function selectCreated(){

				var number = $('#listMyReview').find(':selected').attr('value');
				var type = $('#listMyReview').find(':selected').attr('type');
				var tab = (type == 'create')? createdReviews : contributedReviews;

				var description = "";
				for(var i=0; i<Math.min(10, article.Words.length); i++){
					description+=article.Words[i].Word;
				}  
				description+="...";

				$.get(

				    '<?= url('/').'/'; ?>'+'addArticle', // Le fichier cible côté serveur.

				    {
				    	idArticle: article._id,
				    	idPage: article.IdPage,
				    	date: article.Date,
				    	newspaper: article.TitleNewsPaper,
				    	title: article.Title,
				    	description: description,
				    	idReview: tab[number]._id,
				    	nameReview: tab[number].name
				    },

				    function(data){

					}

				);

				$("#reviewPart").hide();
				$("#addpressreview").text('Article ajouté');

			}

			function addFavorite(){

				var description = "";
				for(var i=0; i<Math.min(10, article.Words.length); i++){
					description+=article.Words[i].Word;
				}  
				description+="...";

				$.get(

				    '<?= url('/').'/'; ?>'+'addFavorite', // Le fichier cible côté serveur.

				    {
				    	idArticle: article._id,
				    	idPage: article.IdPage,
				    	date: article.Date,
				    	newspaper: article.TitleNewsPaper,
				    	title: article.Title,
				    	description: description
				    },

				    function(data){
				    	favorites.push({ "id" : article._id});
					}

				);

				$("#favorite").attr('src', '<?= asset("resources/viewer/star.svg") ?>');
				$("#favorite").attr('onClick', 'removeFavorite()');
			}

			function removeFavorite(){

				$.get(

				    '<?= url('/').'/'; ?>'+'removeFavorite', // Le fichier cible côté serveur.

				    {
				    	idArticle: article._id,
				    },

				    function(data){
				    	for(var i=0; i<favorites.length; i++){
				    		if( favorites[i].id == article._id) favorites.splice(i, 1);
				    	}
					}

				);

				$("#favorite").attr('src', '<?= asset("resources/viewer/empty-star.svg") ?>');
				$("#favorite").attr('onClick', 'addFavorite()');
			}

			function selectSearchReview(page){

				var text = $('#searchReview').val();

				$("#ReviewList").text('');

				$.get(

				    '<?= url('/').'/'; ?>'+'searchReview', // Le fichier cible côté serveur.

				    {
				    	text: text,
				    	size: 5,
				    	page: page
				    },

				    function(data){
				    	var searchReviewResult = data;
				    	writeResults(data, text, page);
					}

				);

			}

			function selectOther(elemnt, idRev, nameRev){


				var description = "";
				for(var i=0; i<Math.min(10, article.Words.length); i++){
					description+=article.Words[i].Word;
				}  
				description+="...";

				$.get(

				    '<?= url('/').'/'; ?>'+'addArticleToOther', // Le fichier cible côté serveur.

				    {
				    	idArticle: article._id,
				    	idPage: article.IdPage,
				    	date: article.Date,
				    	newspaper: article.TitleNewsPaper,
				    	title: article.Title,
				    	description: description,
				    	idReview: idRev,
				    	nameReview: nameRev,
				    	descriptionRev: $('#'+elemnt).text()
				    },

				    function(data){

					}

				);

				$("#searchReview").val('');
				$("#reviewPart").hide();
			}

			function selectNew(){

				var name = $('#newReviewName').val();
				var descr = $('#newReviewDescr').val();

				var description = "";
				for(var i=0; i<Math.min(10, article.Words.length); i++){
					description+=article.Words[i].Word;
				}  
				description+="...";

				$.get(

				    '<?= url('/').'/'; ?>'+'newReview', // Le fichier cible côté serveur.

				    {
				    	idArticle: article._id,
				    	idPage: article.IdPage,
				    	date: article.Date,
				    	newspaper: article.TitleNewsPaper,
				    	title: article.Title,
				    	description: description,
				    	nameReview: name,
				    	descriptionRev: descr
				    },

				    function(data){

					}

				);
				$('#newReviewName').val('');
				$('#newReviewDescr').val('');
				$("#reviewPart").hide();
			}



		</script>
		<!-- End of functions definition -->



		<!-- Scripts for change on the fly -->
		<script type="text/javascript">

		$(window).load(function() {

			 zoomOnArticle(article);

			var availableTags = savedTags;
			$("#tags").autocomplete({
			    source: availableTags,
			    minLength: 0,
			    select: function( event, ui ) { 
			    	newTag(ui.item.value);
			    }
			});

		});

		viewer.addHandler('canvas-click', function(event) {
			var viewportPoint = viewer.viewport.pointFromPixel(event.position);
			var imagePoint = viewer.viewport.viewportToImageCoordinates(viewportPoint.x, viewportPoint.y);
			
			var clickx = imagePoint.x;
			var clicky = imagePoint.y;

			var articleId = CoordToNewArticleId(clickx, clicky);

			selectArticle(articleId.$id, false);

		});



		$("#toggle-overlay").click(function() {
			if (toggle) {
				viewer.clearOverlays();

				for(var i = 0; i<overlaysKwd.length; i++){
					viewer.addOverlay(overlaysKwd[i]);
				}

				$('#toggle-overlay').text('Activer les calques');
			} else {
				var allOverlays = overlays.concat(overlaysSlt);

				for(var i = 0; i<allOverlays.length; i++){
					viewer.addOverlay(allOverlays[i]);
				}

				$('#toggle-overlay').text('Désactiver les calques');

			}
			toggle = !toggle;
			return false;
		});

		$("#zoomOnArticle").click(function() {
			
			zoomOnArticle(article);
			return false;

		});

		$("#zoomOnRead").click(function() {
			
			zoomOnRead(article);
			//return false;

		});

		$('#search_input').keydown(function(e) {
		  if (e.which == 13) {
		    if (e.preventDefault) e.preventDefault();
		    newSearch();
		  }
		});

		$("#hideInfo").click(function(){
    		$("#pageInfo").hide();
    		$("#infoHidden").show();
		});

		$("#infoHidden").click(function(){
			$("#infoHidden").hide();
    		$("#pageInfo").show();
		});

		$("#hideGuide").click(function(){
    		$("#pageGuide").hide();
    		$("#guideHidden").show();
		});

		$("#guideHidden").click(function(){
			$("#guideHidden").hide();
    		$("#pageGuide").show();
		});


		</script>

@stop

