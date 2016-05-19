@extends('app')

@section('page_content_notfluid')
<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
  <div class="panel-heading">Création de revue de presse</div>
  <div class="panel-body">
    <form action="<?= url('/revue/create') ?>" method="post">
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
  </div>
</div>
</div>
@stop