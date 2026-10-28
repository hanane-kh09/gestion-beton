@extends('layout')
@section('title', 'Utilisateurs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3><i class="bi bi-person-gear me-2"></i>Utilisateurs</h3>
  <a href="{{ route('users.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg me-1"></i>Nouvel utilisateur</a>
</div>
<div class="card p-3">
  <table class="table align-middle">
    <thead><tr><th>Nom</th><th>E-mail</th><th>Rôle</th><th></th></tr></thead>
    <tbody>
    @foreach($users as $u)
      <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td><span class="badge bg-{{ ['admin' => 'danger', 'commercial' => 'primary', 'logistique' => 'warning', 'caissier' => 'success'][$u->role] }}">{{ ucfirst($u->role) }}</span></td>
        <td class="text-end">
          <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
          <form class="d-inline" method="POST" action="{{ route('users.destroy', $u) }}" onsubmit="return confirm('Supprimer cet utilisateur ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  {{ $users->links('pagination::bootstrap-5') }}
</div>
@endsection
