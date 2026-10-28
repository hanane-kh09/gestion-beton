@extends('layout')
@section('title', 'Nouveau client')
@section('content')
<h3 class="mb-3">Nouveau client</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('clients.store') }}">
    @csrf
    @include('clients._form')
  </form>
</div>
@endsection
