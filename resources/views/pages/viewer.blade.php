@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css">
<link rel="stylesheet" href="<?= asset('css/viewer.css') ?>" type="text/css"> 
@stop

@section('page_content')
<div class="row" id="visionneuse">
	<div id="pageInfo" class="col-md-2">
	<h4>Informations sur le journal</h4>
	<hr>
	<strong>Titre :</strong> <?= $pages[0]['Title'] ?> <br>
	<strong>Date :</strong> <?= $pages[0]['Date'] ?> <br>
	<hr>
	<h4>Informations sur la page</h4>
	<hr>
	@foreach($pages[0]['Articles'] as $idx => $art)
	<p><strong>Article {{$idx+1}} :</strong> {{$art['Title']}}</p>
	@endforeach
	<div id="currentArticle" style="display:none;">
	<hr>
	<h4>Information sur l'article</h4>
	<hr>
	<strong>Titre :</strong> <span id="currentTitle"></span>
	</div>
	</div>
	<div class="col-md-10">
    <div id="toolbarDiv" class="toolbar">
        <span style='float:right;margin:10px 20px 0 0'>
            | <a id="zoom-in" href="#zoom-in">Zoom In</a> 
            | <a id="zoom-out" href="#zoom-out">Zoom Out</a>
            | <a id="home" href="#home">Home</a> 
            | <a id="full-page" href="#full-page">Full Page</a>
            | <button id="toggle-overlay">Désactiver les calques</button> 
            | <button id="zoomOnArticle">Zoomer sur l'article</button>
            | <input type="checkbox" name="dmc" onclick="activateZoom()" checked>Zoom auto
        </span>
        <span style='float:left;margin:10px 0 0 20px'>
        &lt;&nbsp;
            <a href="visionneuse?id={{$pages[0]['PreviousPage']}}" <?php if( !isset($pages[0]['PreviousPage'])) echo 'class="not-active"'; ?>>Page précédente</a> 
            | <a href="visionneuse?id={{$pages[0]['NextPage']}}" <?php if( !isset($pages[0]['NextPage'])) echo 'class="not-active"'; ?>>Page suivante</a>
            &nbsp;&gt;
        </span>
    </div>
     

    <div id="openseadragon1" class="openseadragon" style="height: 600px;" ></div>
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
				$("#currentArticle").show();
			}
			else{
				$("#currentArticle").hide();
			}
		}

			var zoom = true;
			var toggle = true;
			var filename = '<?php echo $filename ?>';

			var pages =  <?php echo $pages; ?> ;
			var page = pages[0];
			var articles = page.Articles;

			var overlays = [];

			if( filename != 'default.dzi'){
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


				var article =  <?php echo $article; ?> ;
				updateCurrentArticle(article);
				var nOverlay = 0;
				if( article != null ){
					article = article[0];
					updateCurrentArticle(article);
					for( var i = 0; i<article.Coord.length; i++){
						overlays.push({
							id: 'overlaySelected'+i,
					        px: article.Coord[i][0], 
					        py: article.Coord[i][1],
					        width: article.Coord[i][2] - article.Coord[i][0], 
					        height: article.Coord[i][3] - article.Coord[i][1],
					        className: 'overlayArt'
						});
						nOverlay++;
					}
				}

			}else{
				$('#toggle-overlay').remove();
				$('#zoomOnArticle').remove();
			}


			var viewer = OpenSeadragon({
				id: "openseadragon1",
				showRotationControl: true,
			    showNavigator:  true,
				prefixUrl: "/openseadragon/images/",
		        toolbar:        "visionneuse",
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
				overlays: overlays,
			});
	

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

					if( articleparam != null && zoom){
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

				for( var i=0; i<overlays.length;  i++){
					if(typeof overlays[i] != 'undefined')
						var name = overlays[i].id.substring(0, overlays[i].id.length - 1);
						if( name == 'overlaySelected'){
							delete overlays[i];
						}
				}

				for( var j=0; j<nOverlay; j++){
					viewer.removeOverlay('overlaySelected'+j);
				}

			}

			function addSelectedOverlays(articleparam){
				nOverlay = 0;
				for( var i = 0; i<articleparam.Coord.length; i++){

					var elt = {
						id: 'overlaySelected'+i,
				        px: articleparam.Coord[i][0], 
				        py: articleparam.Coord[i][1],
				        width: articleparam.Coord[i][2] - articleparam.Coord[i][0], 
				        height: articleparam.Coord[i][3] - articleparam.Coord[i][1],
				        className: 'overlayArt'
					};

					overlays.push(elt);

					if(toggle) viewer.addOverlay(elt);

					nOverlay++;
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

			if( articleId != null){

				$.get(

				    'changeArticle', // Le fichier cible côté serveur.

				    {
				    	article: articleId
				    },

				    function(data){

			   			article = data[0];
			   			updateCurrentArticle(article);
			   			zoomOnArticle(article);
			   			removeSelectedOverlays();
						addSelectedOverlays(article);

					}

				);



			}

		});



		$("#toggle-overlay").click(function() {
			if (toggle) {
				viewer.clearOverlays();
				$("#currentArticle").hide();
				$('#toggle-overlay').text('Activer les calques');
			} else {
				for(var i = 0; i<overlays.length; i++){
					viewer.addOverlay(overlays[i]);

				}
				if(article != null) $("#currentArticle").show();
				$('#toggle-overlay').text('Désactiver les calques');

			}
			toggle = !toggle;
		});

		$("#zoomOnArticle").click(function() {
			
			zoomOnArticle(article);

		});
	

		</script>

@stop

