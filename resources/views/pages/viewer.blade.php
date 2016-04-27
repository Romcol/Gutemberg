@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css">
<link rel="stylesheet" href="<?= asset('css/viewer.css') ?>" type="text/css"> 
@stop

@section('page_content')
<div class="row">
	<div id="pageInfo" class="col-lg-2">
		<div class="text-right">
			<button id="hideInfo" type="button" class="btn btn-default btn-sm" aria-label="Hide" title="Masquer infos">
		  	<span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span>
			</button>
		</div>
		<h4>Informations sur le journal</h4>
		<hr>
		<strong>Titre :</strong> <?= $pages[0]['Title'] ?> <br>
		<strong>Date :</strong> <?= $pages[0]['Date'] ?> <br>
		<strong>Page :</strong> <?= $pages[0]['NumberPage'] ?> <br>
		<hr>
		<h4>Informations sur la page</h4>
		<hr>
		<div id="pageArticlesList">
		@foreach($pages[0]['Articles'] as $idx => $art)
		<p id="articleList" onclick="selectArticle('{{$art['IdArticle']}}', true)"><strong>Article {{$idx+1}} :</strong> {{$art['Title']}}</p>
		@endforeach
		</div>
		<div id="currentArticle" style="display:none;">
		<hr>
		<h4>Informations sur l'article</h4>
		<hr>
		<strong>Titre :</strong> <span id="currentTitle"></span>
		<strong>Vues :</strong> <span id="currentViews"></span>
		</div>
	</div>
	<div id="infoHidden">
			<button id="showInfo" type="button" class="btn btn-default btn-sm" aria-label="Show" title="Afficher infos">
		  	<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
			</button>
	</div>
	<div id="viewer" class="col-lg-8">
	    <div id="toolbarDiv" class="toolbar">
	        <span style='float:right;margin:10px 20px 0 0'>
	        <a href="visionneuse?id={{$pages[0]['PreviousPage']}}" <?php if( !isset($pages[0]['PreviousPage'])) echo 'class="not-active"'; ?>><img src="<?= asset('resources/viewer/arrow_left.svg') ?>" alt="Flèche gauche" class="viewer-icon"/></a> 
	            | <a href="visionneuse?id={{$pages[0]['NextPage']}}" <?php if( !isset($pages[0]['NextPage'])) echo 'class="not-active"'; ?>><img src="<?= asset('resources/viewer/arrow_right.svg') ?>" alt="Flèche droite" class="viewer-icon"/></a>
	            | <a id="zoom-in" href="#zoom-in"><img src="<?= asset('resources/viewer/zoom_in.svg') ?>" alt="Zoom plus" class="viewer-icon"/></a> 
	            | <a id="zoom-out" href="#zoom-out"><img src="<?= asset('resources/viewer/zoom_out.svg') ?>" alt="Zoom moins" class="viewer-icon"/></a>
	            | <a id="home" href="#home"><img src="<?= asset('resources/viewer/home.svg') ?>" alt="Accueil" class="viewer-icon"/></a> 
	            | <a id="full-page" href="#full-page"><img src="<?= asset('resources/viewer/fullscreen.svg') ?>" alt="Plein ecran" class="viewer-icon"/></a>
	            | <button id="toggle-overlay">Désactiver les calques</button> 
	            | <button id="zoomOnArticle">Zoomer sur l'article</button>
	            | <input type="checkbox" name="dmc" onclick="activateZoom()" checked>Zoom auto
	          	| <div class="form-group" style="display:inline-block;">
				    <input id="search_input" onchange="newSearch()">
				    <button type="button" id="search_button">Recherche</button>   <span id="occurrence"></span> occurrence(s)
				  </div>
	        </span>
	    </div>
	    <div id="openseadragon1" class="openseadragon" style="height: 600px; margin-bottom: 80px" ></div>
    </div>
    <div id="pageGuide" class="col-lg-2">
		<div class="text-left">
			<button id="hideGuide" type="button" class="btn btn-default btn-sm" aria-label="Hide" title="Masquer guidage">
		  	<span class="glyphicon glyphicon-menu-right" aria-hidden="true"></span>
			</button>
		</div>
		<h4>Articles proches</h4>
		<hr>
		<div id="closeArticlesList">

		</div>
	</div>
	<div id="guideHidden">
			<button id="showGuide" type="button" class="btn btn-default btn-sm" aria-label="Show" title="Afficher guidage">
		  	<span class="glyphicon glyphicon-menu-left" aria-hidden="true"></span>
			</button>
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
				$("#currentArticle").show();
			}
			else{
				$("#currentArticle").hide();
			}
		}

			var zoom = true;
			var toggle = true;
			var filename = '<?php echo $filename ?>';
			var image = filename != 'default.dzi';

			var keywords = '<?php echo $keywords; ?>' ;

			$('#search_input').val(keywords);

			var pages =  <?php echo $pages; ?> ;
			var page = pages[0];
			var articles = page.Articles;

			var overlays = [];

			if(image){
				for(var i = 0; i< articles.length; i++) {
					var number  = Math.floor(Math.random()*10);
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
				var article =  <?php echo $article; ?> ;
				updateCurrentArticle(article);
				if( article != null ){
					article = article[0];
					updateCurrentArticle(article);
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
                        $('#closeArticlesList').append('<p id="articleList" class="closeArticle" onClick=\'location.href="visionneuse?id='+article.Close[j].IdPage+'&article='+article.Close[j]._id+'"\'><strong>'+article.Close[j].Title+', </strong>'+article.Close[j].TitleNewsPaper+', '+article.Close[j].Date+'</p>');
                    }
				}

				var search = <?php echo $searchedKeywords; ?>;
				var overlaysKwd = [];

				function newImage() {
			        var img = document.createElement("img");
			        img.src = "http://upload.wikimedia.org/wikipedia/commons/7/7a/Red_Arrow_Right.svg";
			        img.width = 20;
			        return img;
			    }

				if( search.length != 0){
					for( var i=0; i< search.length ; i++){
						var elt = {
							id: newImage(),
					        px: search[i][0], 
					        py: (search[i][3] - search[i][1])/2 + search[i][1],
					        placement: 'RIGHT'
						};
						overlaysKwd.push(elt);
					}
				}
				$('#occurrence').text(search.length);

			}else{
				$('#toggle-overlay').remove();
				$('#zoomOnArticle').remove();
				$('#search_form').remove();
			}


			var viewer = OpenSeadragon({
				id: "openseadragon1",
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
				tileSources:"images/"+filename,
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

				for( var j=0; j<overlaysSlt.length; j++){
					viewer.removeOverlay('overlaySelected'+j);
				}

				overlaysSlt = [];

			}

			function addSelectedOverlays(articleparam){

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
				var allOverlays = overlays.concat(overlaysSlt);

				for(var i = 0; i<allOverlays.length; i++){
					viewer.addOverlay(allOverlays[i]);
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

					if(toggle) viewer.addOverlay(elt);
				}
			}

			function newSearch(){
				var keywd = $('#search_input').val();

				removeKeywordOverlays();

				if( keywd != ''){

					$.get(

					    'newSearch', // Le fichier cible côté serveur.

					    {
					    	id: page._id,
					    	search: keywd
					    },

					    function(data){

				   			search = data;
				   			console.log(search);
							addKeywordOverlays(search);
							$('#occurrence').text(search.length);

						}

					);

				}
			}

			function updateCloseArticles(param){

                $('.closeArticle').remove();

                for( var j=0; j<param.Close.length; j++){
                    $('#closeArticlesList').append('<p id="articleList" class="closeArticle" onClick=\'location.href="visionneuse?id='+article.Close[j].IdPage+'&article='+article.Close[j]._id+'"\'><strong>'+article.Close[j].Title+', </strong>'+article.Close[j].TitleNewsPaper+', '+article.Close[j].Date+'</p>');
                }
            }

			function selectArticle(idArticle, zoomBool){

				if( idArticle != null){

					$.get(

					    'changeArticle', // Le fichier cible côté serveur.

					    {
					    	article: idArticle
					    },

					    function(data){

				   			article = data[0];
				   			updateCurrentArticle(article);
				   			updateCloseArticles(article);
				   			if(zoomBool) zoomOnArticle(article);
				   			removeSelectedOverlays();
							addSelectedOverlays(article);

						}

					);

				}
			}

			function enlargeViewer(){
				if( $('#viewer').attr('class') == 'col-lg-8'){
					$('#viewer').removeClass('col-lg-8').addClass('col-lg-9');
					$('#viewer').css('width', '80%');
				}else{
					$('#viewer').removeClass('col-lg-9').addClass('col-lg-10');
					$('#viewer').css('width', '94%');
				}
			}

			function reduceViewer(){
				if( $('#viewer').attr('class') == 'col-lg-10'){
					$('#viewer').removeClass('col-lg-10').addClass('col-lg-9');
					$('#viewer').css('width', '80%');
				}else{
					$('#viewer').removeClass('col-lg-9').addClass('col-lg-8');
					$('#viewer').css('width', '');
				}
			}


		</script>
		<!-- End of functions definition -->



		<!-- Scripts for change on the fly -->
		<script type="text/javascript">

		$(window).load(function() {

			 zoomOnArticle(article);

		});

		viewer.addHandler('canvas-click', function(event) {
			var viewportPoint = viewer.viewport.pointFromPixel(event.position);
			var imagePoint = viewer.viewport.viewportToImageCoordinates(viewportPoint.x, viewportPoint.y);
			
			var clickx = imagePoint.x;
			var clicky = imagePoint.y;

			var articleId = CoordToNewArticleId(clickx, clicky);

			selectArticle(articleId, false);

		});



		$("#toggle-overlay").click(function() {
			if (toggle) {
				viewer.clearOverlays();
				//$("#currentArticle").hide();
				$('#toggle-overlay').text('Activer les calques');
			} else {
				var allOverlays = overlays.concat(overlaysSlt, overlaysKwd);

				for(var i = 0; i<allOverlays.length; i++){
					viewer.addOverlay(allOverlays[i]);
				}
				//if(article != null) $("#currentArticle").show();
				$('#toggle-overlay').text('Désactiver les calques');

			}
			toggle = !toggle;
		});

		$("#zoomOnArticle").click(function() {
			
			zoomOnArticle(article);

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
    		enlargeViewer();
		});

		$("#showInfo").click(function(){
			$("#infoHidden").hide();
    		$("#pageInfo").show();
    		reduceViewer();
		});

		$("#hideGuide").click(function(){
    		$("#pageGuide").hide();
    		$("#guideHidden").show();
    		enlargeViewer();
		});

		$("#showGuide").click(function(){
			$("#guideHidden").hide();
    		$("#pageGuide").show();
    		reduceViewer();
		});
	

		</script>

@stop

