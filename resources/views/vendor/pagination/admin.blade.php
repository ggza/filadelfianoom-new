@if ($paginator->hasPages())
<nav class="pager" role="navigation" aria-label="Lapozás">
  <p class="pager__info">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} / {{ $paginator->total() }}</p>
  <ul class="pager__list">
    <li>
      @if ($paginator->onFirstPage())
        <span class="pager__item is-disabled" aria-disabled="true">‹ Előző</span>
      @else
        <a class="pager__item" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Előző</a>
      @endif
    </li>
    @foreach ($elements as $element)
      @if (is_string($element))
        <li><span class="pager__item is-gap">{{ $element }}</span></li>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          <li>
            @if ($page == $paginator->currentPage())
              <span class="pager__item is-current" aria-current="page">{{ $page }}</span>
            @else
              <a class="pager__item" href="{{ $url }}" aria-label="{{ $page }}. oldal">{{ $page }}</a>
            @endif
          </li>
        @endforeach
      @endif
    @endforeach
    <li>
      @if ($paginator->hasMorePages())
        <a class="pager__item" href="{{ $paginator->nextPageUrl() }}" rel="next">Következő ›</a>
      @else
        <span class="pager__item is-disabled" aria-disabled="true">Következő ›</span>
      @endif
    </li>
  </ul>
</nav>
@endif
