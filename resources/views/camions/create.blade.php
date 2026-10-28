@extends('layout')
@section('title', 'Nouveau camion')
@section('content')
<h3 class="mb-3">Nouveau camion toupie</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('camions.store') }}">
    @csrf
    @include('camions._form')
  </form>
</div>
@endsection
