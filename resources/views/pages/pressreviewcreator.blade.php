@extends('app')

@section('css_includes')
<link rel="stylesheet" href="<?= asset('css/app.css') ?>" type="text/css"> 
@stop

@section('page_content')
<div class="col-md-8 col-md-offset-1 col-lg-8 col-lg-offset-1">
<h3>Création de revue de presse</h3>
<form action="creationrevue" method="post">
  <div class="form-group">
    <label for="name">Nom de la revue</label>
    <input type="text" class="form-control" name="name" placeholder="Nom...">
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Description</label>
    <textarea class="form-control" rows="3" name="description" placeholder="Description..."></textarea>
  </div>
  <button type="submit" class="btn btn-default">Créer</button>
  <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
</form>
<br>
<p>Une revue de presse vide sera créee.</p>
</div>
@stop