@extends('app')

@section('page_content_notfluid')
<p>Revue de presse vide créee.</p>
<p>Infos :<br>
<a href="revue-{{$pressreview['_id']}}">{{$pressreview['name']}}</a><br>
Description : {{$pressreview['description']}}<br>
</p>
@stop