@extends('layout')
@section('title', 'Nouvelle matière première')
@section('content')
<h3 class="mb-3"><i class="bi bi-boxes me-2"></i>Nouvelle matière première</h3>
<div class="card p-4">
  <form method="POST" action="{{ route('stock.store') }}">
    @csrf
    @include('stock._form')
  </form>
</div>
@endsection
