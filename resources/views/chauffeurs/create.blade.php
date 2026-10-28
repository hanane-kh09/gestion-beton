@extends('layout')
@section('title', 'Nouveau chauffeur')
@section('content')
<h3 class="mb-3">Nouveau chauffeur</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('chauffeurs.store') }}">
    @csrf
    @include('chauffeurs._form')
  </form>
</div>
@endsection
