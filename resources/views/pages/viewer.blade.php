@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
<div>
    <div id="toolbarDiv" class="toolbar">
        <span style='float:right;margin:10px 20px 0 0'>
            | <a id="zoom-in" href="#zoom-in">Zoom In</a> 
            | <a id="zoom-out" href="#zoom-out">Zoom Out</a>
            | <a id="home" href="#home">Home</a> 
            | <button id="toggle-overlay">Désactiver les calques</button> 
        </span>
        <span style='float:left;margin:10px 0 0 20px'>
        &lt;&nbsp;
            <a id="previous" href="#previous-page">Previous</a> 
            | <a id="next" href="#next-page">Next</a> 
            &nbsp;&gt;
        </span>
    </div>

    <div id="openseadragon1" 
         class="openseadragon" style="width: 100%; height: 600px;"></div>
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

			var pages =  <?php echo $pages; ?> ;
			var page = pages[0];
			var articles = page.Articles;

			var overlays = [];

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
			article = article[0];
			for( var i = 0; i<article.Coord.length; i++){
				overlays.push({
					id: 'overlaySelected'+i,
			        px: article.Coord[i][0], 
			        py: article.Coord[i][1],
			        width: article.Coord[i][2] - article.Coord[i][0], 
			        height: article.Coord[i][3] - article.Coord[i][1],
			        className: 'overlayArt'
				});
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
				tileSources:"images/{{$pages[0]['Picture']}}.dzi",
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

		<!-- Scripts for change on the fly -->
		<script type="text/javascript">

		var toggle = true;
		$("#toggle-overlay").click(function() {
			if (toggle) {
				viewer.clearOverlays();
				$('#toggle-overlay').text('Activer les calques');
			} else {
				for(var i = 0; i<overlays.length; i++){
					viewer.addOverlay(overlays[i]);
				}
				$('#toggle-overlay').text('Désactiver les calques');
			}
			toggle = !toggle;
		});

		</script>

@stop

