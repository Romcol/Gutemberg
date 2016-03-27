@extends('app')

@section('page_content')
        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <center>
	            <h1>Bienvenue sur Gutemberg !</h1>
	            <h3 style="margin-top:20px">Plateforme web de consultation de journaux anciens</h3>
	            <!--<p><a class="btn btn-primary btn-large">Call to action!</a>
	            </p>-->
        	</center>
        </header>

        <div class="row text-center">

         <form class="form-inline" action="recherche">
		  <div class="form-group">
		    <input type="text" name="text" class="form-control input-lg" id="search_input" placeholder="Rechercher" required>
		  </div>
		  <div class="form-group">

		      <select name="type" class="form-control input-lg">
					<option value="articles">Contenus des articles</option>
					<option value="newspaper">Journaux</option>
					<option value="titles">Titres des articles</option>
  				</select>
		  </div>
		  <button type="submit" class="btn btn-lg btn-primary">Recherche</button>
		</form>
		</div>

        <hr>

@stop