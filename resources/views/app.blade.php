<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gutemberg</title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-7s5uDGW3AHqw6xtJmNNtr+OBRJUlgkNJEo78P4b0yRw= sha512-nNo+yCHEyn0smMxSswnf/OnX6/KwJuZTlNZBjauKhTK0c+zT+q5JOCx0UFhXQ6rJR9jg6Es8gPuD2uZcYDLqSw==" crossorigin="anonymous">
	@yield('css_includes')
</head>

<body>
<div class="site-wrapper">

  <div class="site-wrapper-inner">

    <div class="cover-container">

      <div class="masthead clearfix">
        <div class="inner">
          <h3 class="masthead-brand">Gutemberg</h3>
          <nav>
            <ul class="nav masthead-nav">
              <li class="active"><a href="<?= url("/"); ?>">Accueil</a></li>
              <li><a href="#">S'inscrire</a></li>
              <li><a href="#">Se connecter</a></li>
              <li><a href="#">Aide</a></li>
            </ul>
          </nav>
        </div>
      </div>
      <div class="inner cover page_content">
	@yield('page_content')
	</div>
	      <div class="mastfoot">
        <div class="inner">
          <p>© 2016 - Gutemberg</p>
        </div>
      </div>

    </div>

  </div>

</div>
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>