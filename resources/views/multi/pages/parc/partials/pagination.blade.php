{{-- Pagination compacte. Variable : $paginator --}}
@if ($paginator->hasPages())
    <div class="d-flex flex-wrap align-items-center justify-content-between mt-1 px-1">
        <p class="small text-muted mb-50">
            De <b>{{ $paginator->firstItem() }}</b> à <b>{{ $paginator->lastItem() }}</b> sur <b>{{ $paginator->total() }}</b> résultats
        </p>
        {{ $paginator->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif
