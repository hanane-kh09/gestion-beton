@extends('layout')
@section('title', 'Modifier chauffeur')
@section('content')
<h3 class="mb-3">Modifier : {{ $chauffeur->nomComplet() }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('chauffeurs.update', $chauffeur) }}">
    @csrf @method('PUT')
    @include('chauffeurs._form')
  </form>
</div>
@endsection
