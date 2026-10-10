@extends('layouts.admin')
@section('title', 'Galéria')
@section('content')
<h1>Galéria</h1>

<section class="panel">
  <h2>Képek feltöltése</h2>
  <form method="post" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="form form--inline">
    @csrf
    <label>Képek (JPG, PNG, WebP; egyszerre több is) <input type="file" name="images[]" accept="image/*" multiple required></label>
    <label>Magyar felirat <input name="caption_hu" placeholder="pl. Kert"></label>
    <label>Román felirat <input name="caption_ro" placeholder="ex. Grădina"></label>
    <button class="btn btn--primary" type="submit">Feltöltés</button>
  </form>
  <p class="muted">A rendszer automatikusan elkészíti a nagy (max. 1600 px) és a kicsi (480×320) változatot. A feliratot később képenként is módosíthatja.</p>
</section>

<form method="post" action="{{ route('admin.gallery.update') }}" id="gallery-form">
  @csrf @method('put')
  <div class="gal-grid">
    @foreach ($images as $img)
      <div class="gal-item">
        <img src="{{ asset($img->thumb) }}" alt="" loading="lazy">
        <label>Sorrend <input type="number" name="img[{{ $img->id }}][sort]" value="{{ $img->sort }}"></label>
        <label>Magyar felirat <input name="img[{{ $img->id }}][caption_hu]" value="{{ $img->caption_hu }}"></label>
        <label>Román felirat <input name="img[{{ $img->id }}][caption_ro]" value="{{ $img->caption_ro }}"></label>
        <label class="check"><input type="checkbox" name="img[{{ $img->id }}][in_hero]" value="1" @checked($img->in_hero)> Fejléc-képváltóban</label>
        <label class="check"><input type="checkbox" name="img[{{ $img->id }}][in_gallery]" value="1" @checked($img->in_gallery)> Galériában</label>
        <button class="btn btn--danger btn--small" type="submit" form="del-{{ $img->id }}">Törlés</button>
      </div>
    @endforeach
  </div>
  @if ($images->count())<p class="sticky-save"><button class="btn btn--primary" type="submit">Feliratok és sorrend mentése</button></p>@endif
</form>
@foreach ($images as $img)
  <form id="del-{{ $img->id }}" method="post" action="{{ route('admin.gallery.destroy', $img) }}" onsubmit="return confirm('Biztosan törli ezt a képet?')">@csrf @method('delete')</form>
@endforeach
@endsection
