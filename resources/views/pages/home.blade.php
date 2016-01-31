@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?php echo asset('css/cover.css')?>" type="text/css"> 
@stop

@section('page_content')
<div class="site-wrapper">

  <div class="site-wrapper-inner">

    <div class="cover-container">

      <div class="masthead clearfix">
        <div class="inner">
          <h3 class="masthead-brand">Gutemberg</h3>
          <nav>
            <ul class="nav masthead-nav">
              <li class="active"><a href="#">Accueil</a></li>
              <li><a href="#">S'inscrire</a></li>
              <li><a href="#">Se connecter</a></li>
              <li><a href="#">Aide</a></li>
            </ul>
          </nav>
        </div>
      </div>

      <div class="inner cover">
        <!--<h1 class="cover-heading">Cover your page.</h1>
        <p class="lead">Cover is a one-page template for building simple and beautiful home pages. Download, edit the text, and add your own fullscreen background photo to make it your own.</p>
        <p class="lead">
          <a href="#" class="btn btn-lg btn-default">Learn more</a>
        </p>-->
        <p class="lead">Bienvenue sur Gutemberg
        <form class="form-inline" action="/recherche">
		  <div class="form-group">
		    <input type="text" name="texte" class="form-control" id="search_input" placeholder="Rechercher">
		  </div>
		  <div class="form-group">
		      <select name="type" class="form-control">
					<option value="articles">Articles</option>
					<option value="revues">Revues de presse</option>
					<option value="journeaux">Journaux</option>
  				</select>
		  </div>
		  <button type="submit" class="btn btn-default">Recherche</button>
		</form>
        </p>
      </div>

      <div class="mastfoot">
        <div class="inner">
          <p>Footer</p>
        </div>
      </div>

    </div>

  </div>

</div>
@stop