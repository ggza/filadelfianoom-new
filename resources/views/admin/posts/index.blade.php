@extends('layouts.admin')
@section('title', 'Bejegyzések')
@section('content')
<h1>Bejegyzések <a class="btn btn--primary" href="{{ route('admin.posts.create') }}">+ Új bejegyzés</a></h1>
<p class="muted">A közzétett bejegyzések a nyilvános oldal „Hírek / Noutăți” részében jelennek meg, azon a nyelven, amelyen van címük vagy szövegük.</p>
<table class="table">
  <thead><tr><th>Cím</th><th>Nyelv</th><th>Állapot</th><th></th></tr></thead>
  <tbody>
  @forelse ($posts as $p)
    <tr>
      <td><b>{{ $p->title_hu ?: $p->title_ro ?: '(cím nélkül)' }}</b></td>
      <td>{{ $p->hasLang('ro') ? 'RO' : '' }} {{ $p->hasLang('hu') ? 'HU' : '' }}</td>
      <td>{{ $p->published_at ? ($p->published_at->isFuture() ? 'Időzítve: ' : 'Közzétéve: ') . $p->published_at->format('Y.m.d. H:i') : 'Piszkozat' }}</td>
      <td class="actions">
        <a class="btn" href="{{ route('admin.posts.edit', $p) }}">Szerkesztés</a>
        <form method="post" action="{{ route('admin.posts.destroy', $p) }}" onsubmit="return confirm('Biztosan törli?')">@csrf @method('delete')<button class="btn btn--danger">Törlés</button></form>
      </td>
    </tr>
  @empty
    <tr><td colspan="4" class="muted">Még nincs bejegyzés.</td></tr>
  @endforelse
  </tbody>
</table>
@endsection
