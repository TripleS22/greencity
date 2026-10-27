@if ($paginator->hasPages())
<div class="pagination-bar" style="display:flex; align-items:center; justify-content:space-between; margin-top:16px; flex-wrap:wrap; gap:8px;">
    <div style="font-size:13px; color:#666;">
        Menampilkan <strong>{{ $paginator->firstItem() }}</strong>–<strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
    </div>
    <div style="display:flex; gap:4px; flex-wrap:wrap;">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span style="padding:5px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; color:#aaa; cursor:not-allowed; background:#f9f9f9;">
                &laquo;
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" style="padding:5px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; color:#333; text-decoration:none; background:#fff; transition:all .15s;" onmouseover="this.style.background='#3a7d34';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#333'">
                &laquo;
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:5px 10px; font-size:13px; color:#aaa;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span style="padding:5px 12px; border:1px solid #3a7d34; border-radius:6px; font-size:13px; background:#3a7d34; color:#fff; font-weight:600;">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}" style="padding:5px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; color:#333; text-decoration:none; background:#fff; transition:all .15s;" onmouseover="this.style.background='#3a7d34';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#333'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" style="padding:5px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; color:#333; text-decoration:none; background:#fff; transition:all .15s;" onmouseover="this.style.background='#3a7d34';this.style.color='#fff'" onmouseout="this.style.background='#fff';this.style.color='#333'">
                &raquo;
            </a>
        @else
            <span style="padding:5px 12px; border:1px solid #ddd; border-radius:6px; font-size:13px; color:#aaa; cursor:not-allowed; background:#f9f9f9;">
                &raquo;
            </span>
        @endif
    </div>
</div>
@endif
