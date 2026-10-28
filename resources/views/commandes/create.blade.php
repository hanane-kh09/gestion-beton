@extends('layout')
@section('title', 'Nouvelle commande')
@section('content')
<h3 class="mb-3">Nouvelle commande</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('commandes.store') }}">
    @csrf
    @include('commandes._form')
  </form>
</div>
@endsection
