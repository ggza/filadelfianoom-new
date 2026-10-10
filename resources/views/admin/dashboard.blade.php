@extends('layouts.admin')
@section('title', 'Áttekintés')
@section('content')
<h1>Áttekintés</h1>
<div class="tiles">
  <a class="tile @if($pendingUsers) tile--alert @endif" href="{{ route('admin.users.index') }}"><strong>{{ $pendingUsers }}</strong><span>jóváhagyásra váró hozzátartozó</span></a>
  <a class="tile @if($newAdmissions) tile--alert @endif" href="{{ route('admin.admissions.index', ['status' => 'new']) }}"><strong>{{ $newAdmissions }}</strong><span>új jelentkezés</span></a>
  <a class="tile @if($newMessages) tile--alert @endif" href="{{ route('admin.messages.index', ['status' => 'new']) }}"><strong>{{ $newMessages }}</strong><span>új üzenet</span></a>
  <a class="tile" href="{{ route('admin.residents.index') }}"><strong>{{ $residents }}</strong><span>aktív lakó</span></a>
  <a class="tile" href="{{ route('admin.gallery.index') }}"><strong>{{ $images }}</strong><span>kép a galériában</span></a>
  <a class="tile" href="{{ route('admin.posts.index') }}"><strong>{{ $posts }}</strong><span>bejegyzés</span></a>
</div>
<section class="panel">
  <h2>Gyors útmutató</h2>
  <ul>
    <li><b>Szövegek:</b> a nyilvános oldal szakaszai, nyelvenként szerkeszthetők és elrejthetők.</li>
    <li><b>Galéria:</b> képfeltöltés (a kicsi és a nagy változat automatikusan készül), feliratok két nyelven, sorrend, hogy a fejléc-képváltóban és/vagy a galériában jelenjen meg.</li>
    <li><b>Bejegyzések:</b> hírek, a nyilvános oldal „Hírek / Noutăți” részében jelennek meg.</li>
    <li><b>Lakók:</b> itt tölthet fel fotót és hírt egy lakóhoz; ezt csak a hozzá rendelt, jóváhagyott hozzátartozók látják.</li>
    <li><b>Üzenetek:</b> a nyilvános oldal kapcsolatfelvételi űrlapjáról érkezett üzenetek; a válasz az e-mail címre kattintva írható meg.</li>
    <li><b>Felhasználók:</b> a regisztrált hozzátartozók jóváhagyása és lakóhoz rendelése.</li>
  </ul>
</section>
@endsection
