@extends('layout')
@section('title', 'Nouvel utilisateur')
@section('content')
<h3 class="mb-3">Nouvel utilisateur</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('users.store') }}">
    @csrf
    @include('users._form')
  </form>
</div>
@endsection
