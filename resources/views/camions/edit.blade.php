@extends('layout')
@section('title', 'Modifier camion')
@section('content')
<h3 class="mb-3">Modifier : {{ $camion->matricule }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('camions.update', $camion) }}">
    @csrf @method('PUT')
    @include('camions._form')
  </form>
</div>
@endsection
