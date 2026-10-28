@extends('layout')
@section('title', 'Modifier commande')
@section('content')
<h3 class="mb-3">Modifier : CMD-{{ $commande->id }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('commandes.update', $commande) }}">
    @csrf @method('PUT')
    @include('commandes._form')
  </form>
</div>
@endsection
