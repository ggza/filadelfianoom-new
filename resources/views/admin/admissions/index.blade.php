@extends('layouts.admin')
@section('title', 'Jelentkezések')
@section('content')
<h1>Jelentkezések</h1>
<p class="filters">
  <a href="{{ route('admin.admissions.index') }}" @if(!request('status')) aria-current="page" @endif>Mind</a>
  @foreach (['new' => 'Új', 'in_progress' => 'Folyamatban', 'closed' => 'Lezárt'] as $k => $v)
    <a href="{{ route('admin.admissions.index', ['status' => $k]) }}" @if(request('status') === $k) aria-current="page" @endif>{{ $v }}</a>
  @endforeach
</p>
<table class="table">
  <thead><tr><th>Beérkezett</th><th>Beköltöző</th><th>Telefon</th><th>Nyelv</th><th>Állapot</th><th></th></tr></thead>
  <tbody>
  @forelse ($admissions as $a)
    <tr>
      <td>{{ $a->created_at->format('Y.m.d. H:i') }}@if($a->source === 'excel')<br><small class="muted">régi űrlap, import · #{{ $a->legacy_serial }}</small>@endif</td>
      <td>{{ \Illuminate\Support\Str::limit($a->resident, 60) }}</td>
      <td>{{ $a->phone }}</td>
      <td>{{ strtoupper($a->locale) }}</td>
      <td><span class="badge badge--{{ $a->status }}">{{ ['new' => 'Új', 'in_progress' => 'Folyamatban', 'closed' => 'Lezárt'][$a->status] ?? $a->status }}</span></td>
      <td><a class="btn" href="{{ route('admin.admissions.show', $a) }}">Megnyitás</a></td>
    </tr>
  @empty
    <tr><td colspan="6" class="muted">Nincs jelentkezés.</td></tr>
  @endforelse
  </tbody>
</table>
{{ $admissions->links() }}
@endsection
