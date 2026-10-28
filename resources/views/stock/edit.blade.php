@extends('layout')
@section('title', 'Modifier '.$matiere->nom)
@section('content')
<h3 class="mb-3"><i class="bi bi-boxes me-2"></i>Modifier : {{ $matiere->nom }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('stock.update', $matiere) }}">
    @csrf @method('PUT')
    @include('stock._form')
  </form>
</div>
@endsection
