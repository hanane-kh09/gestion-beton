@extends('layout')
@section('title', 'Modifier produit')
@section('content')
<h3 class="mb-3">Modifier : {{ $produit->code }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('produits.update', $produit) }}">
    @csrf @method('PUT')
    @include('produits._form')
  </form>
</div>
@endsection
