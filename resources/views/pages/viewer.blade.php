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
            | <a id="full-page" href="#full-page">Full Page</a> 
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

		<script src="<?= asset('/openseadragon/openseadragon.min.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selection.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionoverlay.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionrect.js') ?>"></script>
		<script src="<?= asset('/openseadragon/openseadragonselection.js') ?>"></script>
		<script type="text/javascript">
		
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
				//sequenceMode: true,   
				//showReferenceStrip: true,
				//referenceStripScroll: 'vertical',
			});

			var elt = document.createElement("div");
	      	@foreach ($articles as $index => $pages[0]['Articles'])
				viewer.addOverlay({
					element: elt,
					location: new OpenSeadragon.Rect({{$articles['Coord'][0]}},
														{{$articles['Coord'][1]}},
														{{$articles['Coord'][2]}},
														{{$articles['Coord'][3]}}) 
				})
	      	@endforeach

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

@stop