<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
                background-color: lightgray;
                color: boldblack;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
                color: purple;
                font-weight: bold;
            }
        </style>
    </head>
     <div id="openseadragon1" style="width: 800px; height: 600px; border: 1px solid black;background-color: grey;" >
		<script src="<?= asset('/openseadragon/openseadragon.min.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selection.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionoverlay.js') ?>"></script>
		<script src="<?= asset('/openseadragon/selectionrect.js') ?>"></script>
		<script src="<?= asset('/openseadragon/openseadragonselection.js') ?>"></script>
		<script src="<?= asset('/deepzoom-master/src/DeepzoomFactory.php') ?>"></script>
		<script type="text/javascript">
		//$deep = new ImageCreator(new File(),new Descriptor(new File()),new Imagick());

//deep.create(realpath('/openseadragon/images/presse.jpg'), '/openseadragon/images/presse.dzi');
		  // Setup Deepzoom
			 // $deepzoom = DeepzoomFactory::create([
			     // 'path' => 'images', // Export path for tiles
			     // 'driver' => 'imagick', // Choose between gd and imagick support.
			     // 'format' => 'jpg',
			  //]);
			  // folder, file are optional and will default to filename
			  //$response = $deepzoom=>makeTiles('presse.jpg', 'file', 'folder');
			var viewer = OpenSeadragon({
				id: "openseadragon1",
				showRotationControl: true,
			    showNavigator:  true,
				prefixUrl: "/openseadragon/images/",
				tileSources:  {
					//type: 'image',
					//url: '/openseadragon/images/presse2.jpg',
					type: 'image',
					url: '/openseadragon/images/presse.jpg'	
				},
				sequenceMode: true,   
				//showReferenceStrip: true,
				//referenceStripScroll: 'vertical',
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
			selection.enable();
			//selection.toggleState();
		</script>
	</div>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">Visionneuse Gutemberg</div>
            </div>
        </div>
    </body>
</html>