@extends('layouts.admin')
@section('title', 'Üzenetek')
@section('content')
<h1>Üzenetek</h1>
<p class="filters">
  <a href="{{ route('admin.messages.index') }}" @if(!request('status')) aria-current="page" @endif>Mind</a>
  @foreach (\App\Models\ContactMessage::LABELS as $k => $v)
    <a href="{{ route('admin.messages.index', ['status' => $k]) }}" @if(request('status') === $k) aria-current="page" @endif>{{ $v }}</a>
  @endforeach
</p>
<table class="table">
  <thead><tr><th>Beérkezett</th><th>Név</th><th>Üzenet</th><th>Nyelv</th><th>Állapot</th><th></th></tr></thead>
  <tbody>
  @forelse ($messages as $m)
    <tr @if($m->status === 'new') class="row--alert" @endif>
      <td>{{ $m->created_at->format('Y.m.d. H:i') }}</td>
      <td>{{ $m->name }}</td>
      <td>{{ \Illuminate\Support\Str::limit($m->message, 70) }}</td>
      <td>{{ strtoupper($m->locale) }}</td>
      <td><span class="badge badge--{{ $m->status === 'answered' ? 'in_progress' : $m->status }}">{{ \App\Models\ContactMessage::LABELS[$m->status] ?? $m->status }}</span></td>
      <td><a class="btn" href="{{ route('admin.messages.show', $m) }}">Megnyitás</a></td>
    </tr>
  @empty
    <tr><td colspan="6" class="muted">Nincs üzenet.</td></tr>
  @endforelse
  </tbody>
</table>
{{ $messages->links() }}
@endsection
