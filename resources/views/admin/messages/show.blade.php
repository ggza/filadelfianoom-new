@extends('layouts.admin')
@section('title', 'Üzenet')
@section('content')
<p><a href="{{ route('admin.messages.index') }}">← Üzenetek</a></p>
<h1>Üzenet – {{ $message->created_at->format('Y.m.d. H:i') }}</h1>
<dl class="dl">
  <dt>Név</dt><dd>{{ $message->name }}</dd>
  <dt>E-mail</dt><dd><a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Filadelfia Noom') }}">{{ $message->email }}</a></dd>
  <dt>Telefon</dt><dd>{{ $message->phone ?: '–' }}</dd>
  <dt>Üzenet</dt><dd>{{ $message->message }}</dd>
  <dt>Űrlap nyelve</dt><dd>{{ strtoupper($message->locale) }}</dd>
</dl>
<form method="post" action="{{ route('admin.messages.update', $message) }}" class="form panel">
  @csrf @method('put')
  <label>Állapot
    <select name="status">
      @foreach (\App\Models\ContactMessage::LABELS as $k => $v)
        <option value="{{ $k }}" @selected($message->status === $k)>{{ $v }}</option>
      @endforeach
    </select>
  </label>
  <label>Belső megjegyzés <textarea name="admin_note" rows="4">{{ $message->admin_note }}</textarea></label>
  <button class="btn btn--primary" type="submit">Mentés</button>
</form>
<form class="danger-zone" method="post" action="{{ route('admin.messages.destroy', $message) }}" onsubmit="return confirm('Biztosan véglegesen törli ezt az üzenetet?')">
  @csrf @method('delete')<button class="btn btn--danger">Üzenet törlése</button>
</form>
@endsection
