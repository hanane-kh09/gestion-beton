@extends('layout')
@section('title', 'Nouveau produit')
@section('content')
<h3 class="mb-3">Nouveau produit</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('produits.store') }}">
    @csrf
    @include('produits._form')
  </form>
</div>
@endsection
