@extends('app')

@section('page_content_notfluid')
@if($pressreview)
	<h3>{{$pressreview['name']}}</h3>
	<p><em>{{$pressreview['description']}}</em></p>
	<hr>
	@if($pressreview['articles'])
		<div class="row">
		<div class="col-md-12 col-lg-12 pressreviewarticles">
		@foreach ($pressreview['articles'] as $ind => $article)
				<div class="row">
				<div class="col-md-1 col-lg-1">
				<button type="button" class="btn btn-default btn-block upbtn">&uarr;</button>
				<button type="button" class="btn btn-default btn-block deletebtn">X</button>
				<button type="button" class="btn btn-default btn-block downbtn">&darr;</button>
				</div>
				<div id="zone-{{$ind}}" class="col-md-11 col-lg-11">
			      	<article draggable="true" id="article-{{$article['id']}}">
		            <div class="panel panel-default">
		              <div class="panel-heading">
		               <h3 class="panel-title"><a href="<?= url('/visionneuse/page/'.$article['IdPage'].'/article/'.$article['id']); ?>" >{{$article['TitleNewsPaper']}}, {{$article['date']}}</a></h3>
		              </div>
		              <div class="panel-body">
		                <B class="title">{{$article['Title']}}</B>
		                <p style="margin-top:20px">{!! $article['description'] !!}</p>
		              </div>
		            </div>
		      		</article>
	      		</div>
	      		</div>
		@endforeach
		</div>
		</div>
	@else
	<p>Revue de presse vide.<p>
	@endif
	<form id="saveform" style="display:none;" action="<?= url('/revue/'.$pressreview['_id'].'/update') ?>">
	<input type="hidden" name="data" value=""/>
	<input type="submit" class="btn btn-default" value="Enregistrer"/>
	</form>
	<a href="<?= url('/revue/'.$pressreview['_id'].'/delete') ?>" class="btn btn-default" role="button">Supprimer la revue de presse</a>
@else
    <p>Aucun résultat pour cette revue de presse.</p>
@endif
@stop

@section('scripts')
<script>

$(document).ready(function() {

	var pressreviewarticles = [];
	@foreach ($pressreview['articles'] as $ind => $article)
	pressreviewarticles[{{$ind}}] = "{{$article['id']}}";
	@endforeach

	var currentid, dropid;
	var artregex = /article-(.+)/;

	function disableButtons()
	{
		$('.pressreviewarticles .upbtn').first().removeClass('disabled');
		$('.pressreviewarticles .downbtn').first().removeClass('disabled');
		$('.pressreviewarticles .upbtn').first().addClass('disabled');
		$('.pressreviewarticles .downbtn').last().addClass('disabled');
	}

	function showSave()
 	{
 		disableButtons();
 		$("#saveform input[type='hidden']").prop('value', pressreviewarticles.toString());
 		$('#saveform').show();
 	}

	function remove_article(a)
	{
		$('#'+a).closest(".row").remove();
 		var rmid = artregex.exec(a)[1];
 		var pressreviewarticlesnew = [];
 		var j = 0;
		for(var i=0; i < pressreviewarticles.length; i++)
 		{
 			if(rmid != pressreviewarticles[i]){
 				pressreviewarticlesnew[j] = pressreviewarticles[i];
 				j++;
 			} 
 		}

 		pressreviewarticles = pressreviewarticlesnew;

 		showSave();
	}

 	function invert(a,b)
 	{
 		var parent1id = $('#'+a).parent().prop('id');
 		var parent2id = $('#'+b).parent().prop('id');
 		var first = $('#'+parent1id).html();
 		var second = $('#'+parent2id).html();

 		$('#'+parent1id).html(second);
 		$('#'+parent2id).html(first);

 		var id1 = artregex.exec(a)[1];
 		var id2 = artregex.exec(b)[1];

 		var ind1,ind2;

 		for(var i=0; i < pressreviewarticles.length; i++)
 		{
 			if(id1 == pressreviewarticles[i]) ind1 = i;
 			if(id2 == pressreviewarticles[i]) ind2 = i;

 			if(ind1 && ind2) break;
 		}
 		var temp = pressreviewarticles[ind1];
 		pressreviewarticles[ind1] = pressreviewarticles[ind2];
 		pressreviewarticles[ind2] = temp;

 		addEventPressReview();
 		showSave();
 	}

 	$(".deletebtn").click(function()
 	{
 		var removeid = $(this).closest(".row").find("article").prop("id");
 		remove_article(removeid);
 	});

 	$(".upbtn").click(function()
 	{
 		var ida = $(this).closest(".row").find("article").prop("id");
 		var idb = $(this).closest(".row").prev(".row").find("article").prop("id");
 		invert(ida, idb);
 	});

 	$(".downbtn").click(function()
 	{
 		var ida = $(this).closest(".row").find("article").prop("id");
 		var idb = $(this).closest(".row").next(".row").find("article").prop("id");
 		invert(ida, idb);
 	});

 	function addEventPressReview()
 	{
	    $('.pressreviewarticles article').on({
	        // on commence le drag
	        dragstart: function(e) {
	            $(this).css('opacity', '0.5');
	            currentid = $(this).prop('id');
	            // on garde le texte en mémoire (A, B, C ou D)
	            //e.dataTransfer.setData('text', $this.text());
	        },
	        // on passe sur un élément draggable
	        dragenter: function(e) {
	            // on augmente la taille pour montrer le draggable
	            $(this).find(".panel").css('border', '3px dotted #dbf3d0');
	            e.preventDefault();
	        },
	        // on quitte un élément draggable
	        dragleave: function() {
	        	$(this).find(".panel").css('border', 'none');
	        },
	        // déclenché tant qu on a pas lâché l élément
	        dragover: function(e) {
	            e.preventDefault();
	        },
	        // on lâche l élément
	        drop: function(e) {
	        	console.log('ok');
	        	dropid = $(this).prop('id');
	        	if(dropid != currentid)
	        	{
	        		invert(currentid, dropid);
	        	}
	        	$('.pressreviewarticles article').css('opacity', '1');
		        $('.pressreviewarticles article .panel').css('border', 'none');
	    	},
	    	dragend: function(){
	    		console.log('dragend');
	    		$('.pressreviewarticles article').css('opacity', '1');
		        $('.pressreviewarticles article .panel').css('border', 'none');
	    	}
	    });
	}

	addEventPressReview();
	disableButtons();
});
</script>
@stop