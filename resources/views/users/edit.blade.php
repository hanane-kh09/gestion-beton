@extends('layout')
@section('title', 'Modifier utilisateur')
@section('content')
<h3 class="mb-3">Modifier : {{ $user->name }}</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('users.update', $user) }}">
    @csrf @method('PUT')
    @include('users._form')
  </form>
</div>
@endsection
