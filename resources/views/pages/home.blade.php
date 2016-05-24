@extends('app')

@section('page_out')
        <div class="hero-image-container">
        <div>
        <img src="<?= asset('/hero.jpg') ?>" alt="hero"/>
        <h1></h1>
        </div>
        </div>
@stop

@section('page_content')
        <div class="row text-center">

         <form class="form-inline" action="recherche">
		  <div class="form-group">
		    <input type="text" name="text" class="form-control input-lg" id="search_input" placeholder="Rechercher">
		  </div>
		  <div class="form-group">

		      <select name="type" class="form-control input-lg">
					<option value="articles">Contenus des articles</option>
					<option value="titles">Titres des articles</option>
                    <option value="review">Revues de presse</option>
  				</select>
		  </div>
		  <button type="submit" class="btn btn-lg btn-default">Recherche</button> ou <a href="recherche?text=&type=newspaper" id="browseNewspapers">Parcourir les journaux</a>
		</form>
		</div>

        <hr>

        <div class="row text-center">

            <div class="col-md-4 col-sm-6 hero-feature">
			<!-- Title -->
				<h3 style="margin-bottom:30px">Articles les plus vus</h3>
				@foreach ($articles as $index => $article)
		      		<article>
		            <div class="panel panel-default">
		              <div class="panel-heading">
		               <a href="<?= url('visionneuse/page/'.$article['IdPage'].'/article/'.$article['id']); ?>"> <h4 class="panel-title">{{$article['TitleNewsPaper']}}, {{$article['Date']}}</h4></a>
		              </div>
		              <div class="panel-body">
		                {{$article['Title']}}
		              </div>
		            </div>
		      		</article>
			     @endforeach
            </div>
            <div class="col-md-4 col-sm-6 hero-feature">
            <!-- Title -->
                <h3 style="margin-bottom:30px">Revues récemment créées</h3>
                @foreach ($reviews as $index => $review)
                    <article>
                    <div class="panel panel-default">
                      <div class="panel-heading">
                       <a href="<?= url('revue/'.$review['_id']); ?>"><h4 class="panel-title">{{$review['name']}}</h4></a>
                      </div>
                      <div class="panel-body">
                        {{$review['description']}}
                      </div>
                    </div>
                    </article>
                 @endforeach
            </div>

        </div>
@stop