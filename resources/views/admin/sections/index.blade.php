@extends('layouts.admin')
@section('title', 'Szövegek')
@section('content')
<h1>Szövegek</h1>
<p class="muted">A nyilvános oldal szakaszai. A régi oldal szövegei ide kerültek, változatlanul.</p>
<table class="table">
  <thead><tr><th>Szakasz</th><th>Román cím</th><th>Magyar cím</th><th>Látható</th><th></th></tr></thead>
  <tbody>
  @foreach ($sections as $sec)
    <tr>
      <td><b>{{ $sec->admin_label }}</b></td>
      <td>{{ $sec->title_ro }}</td>
      <td>{{ $sec->title_hu }}</td>
      <td>{{ $sec->show_ro && $sec->body_ro ? 'RO' : '–' }} / {{ $sec->show_hu && $sec->body_hu ? 'HU' : '–' }}</td>
      <td><a class="btn" href="{{ route('admin.sections.edit', $sec) }}">Szerkesztés</a></td>
    </tr>
  @endforeach
  </tbody>
</table>
@endsection
