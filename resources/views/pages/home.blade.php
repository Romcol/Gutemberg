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
        <!-- Jumbotron Header -->
       <!-- <header class="jumbotron hero-spacer">
            <center>
	            <h1>Bienvenue sur Gutemberg !</h1>
	            <h3 style="margin-top:20px">Plateforme web de consultation de journaux anciens</h3>
        	</center>
        </header>-->
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

        <!-- PHP code for the next articles list and initiate the searchUri value-->
        <?php
        use App\Article;
        use App\PressReview;

        session_start();
        $_SESSION['searchUri'] = null;



        $params = [
          'sort' => [
            'Views' => [
              'order' => 'desc'
            ]
          ],
          'size' => 5
        ];

        $articles = Article::search($params);

        $params2 = [
          'sort' => [
            'created' => [
              'order' => 'desc'
            ]
          ],
          'size' => 5
        ];

        $reviews = PressReview::search($params2);
        ?>
        <!-- /PHP -->

        <!-- /.row -->
        <!-- Page Features -->
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
                       <a href="<?= url('revue/'.$review['_id']); ?>">{{$review['name']}}</a>
                      </div>
                    </div>
                    </article>
                 @endforeach
            </div>
            <!--
            <!--
            <div class="col-md-4 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <img src="http://placehold.it/800x500" alt="">
                    <div class="caption">
                        <h3>Feature Label</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        <p>
                            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="#" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 hero-feature">
                <div class="thumbnail">
                    <img src="http://placehold.it/800x500" alt="">
                    <div class="caption">
                        <h3>Feature Label</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                        <p>
                            <a href="#" class="btn btn-primary">Buy Now!</a> <a href="#" class="btn btn-default">More Info</a>
                        </p>
                    </div>
                </div>
            </div>
        -->

        </div>
@stop