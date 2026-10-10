@extends('layouts.admin')
@section('title', 'Felhasználók')
@section('content')
<h1>Felhasználók</h1>
<table class="table">
  <thead><tr><th>Név</th><th>E-mail</th><th>Megadott lakó</th><th>Hozzárendelt lakók</th><th>Szerep</th><th>Állapot</th><th></th></tr></thead>
  <tbody>
  @foreach ($users as $u)
    <tr @if($u->status === 'pending') class="row--alert" @endif>
      <td><b>{{ $u->name }}</b><br><small class="muted">{{ $u->phone }}</small></td>
      <td>{{ $u->email }}</td>
      <td>{{ $u->requested_resident }} @if($u->requested_relation)<small class="muted">({{ $u->requested_relation }})</small>@endif</td>
      <td>{{ $u->residents->pluck('name')->join(', ') ?: '–' }}</td>
      <td>{{ $u->role === 'admin' ? 'Admin' : 'Hozzátartozó' }}</td>
      <td><span class="badge badge--{{ $u->status }}">{{ ['pending' => 'Jóváhagyásra vár', 'approved' => 'Jóváhagyva', 'blocked' => 'Letiltva'][$u->status] }}</span></td>
      <td><a class="btn" href="{{ route('admin.users.edit', $u) }}">Kezelés</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
@endsection
