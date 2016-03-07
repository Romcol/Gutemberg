@extends('app')

@section('page_content')
        <!-- Jumbotron Header -->
        <header class="jumbotron hero-spacer">
            <h2>Bienvenue sur Gutemberg !</h2>
            <!-- <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsa, ipsam, eligendi, in quo sunt possimus non incidunt odit vero aliquid similique quaerat nam nobis illo aspernatur vitae fugiat numquam repellat.</p> -->
            <!--<p><a class="btn btn-primary btn-large">Call to action!</a>
            </p>-->
        </header>

        <div class="row text-center">

         <form class="form-inline" action="recherche">
		  <div class="form-group">
		    <input type="text" name="text" class="form-control input-lg" id="search_input" placeholder="Rechercher" required>
		  </div>
		  <div class="form-group">

		      <select name="type" class="form-control input-lg">
					<option value="articles">Contenu des articles</option>
					<!-- <option value="revues">Revues de presse</option> -->
					<option value="titles">Titres des articles</option>
  				</select>
		  </div>
		  <button type="submit" class="btn btn-lg btn-primary">Recherche</button>
		</form>
		</div>

        <hr>

@stop