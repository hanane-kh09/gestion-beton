@extends('layout')
@section('title', 'Modifier client')
@section('content')
<h3 class="mb-3">Modifier : {{ $client->nom }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('clients.update', $client) }}">
    @csrf @method('PUT')
    @include('clients._form')
  </form>
</div>
@endsection
