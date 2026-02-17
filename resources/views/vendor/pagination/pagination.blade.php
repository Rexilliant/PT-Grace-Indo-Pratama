@if ($paginator->hasPages())
    <div
        class="flex flex-col gap-2 sm:gap-3 md:flex-row md:items-center md:justify-between
               bg-gray-200 px-3 sm:px-4 md:px-5 py-3 sm:py-4 border-t border-gray-400">

        {{-- Showing text (hide on mobile, show on tablet+) --}}
        <div class="hidden sm:block text-xs sm:text-sm font-semibold text-gray-800">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>

        <div class="sm:hidden text-xs font-semibold text-gray-800">
            Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>
        {{-- Showing text (mobile only, placed below buttons) --}}

        {{-- Pagination buttons (mobile-friendly scroll) --}}
        <div class="w-full md:w-auto overflow-x-auto">
            <div class="inline-flex w-max rounded-lg border border-gray-400 overflow-hidden shadow-sm">

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span
                        class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                               bg-gray-200 text-gray-400 cursor-not-allowed border-r border-gray-400 whitespace-nowrap">
                        Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                               bg-gray-200 hover:bg-gray-300 border-r border-gray-400 whitespace-nowrap">
                        Previous
                    </a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($elements as $element)
                    {{-- "..." --}}
                    @if (is_string($element))
                        <span
                            class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                                   bg-gray-200 border-r border-gray-400 whitespace-nowrap">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span
                                    class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                                           bg-gray-300 border-r border-gray-400 whitespace-nowrap">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                                           bg-gray-200 hover:bg-gray-300 border-r border-gray-400 whitespace-nowrap">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                        class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                               bg-gray-200 hover:bg-gray-300 whitespace-nowrap">
                        Next
                    </a>
                @else
                    <span
                        class="px-2.5 sm:px-4 py-2 text-xs sm:text-sm font-semibold
                               bg-gray-200 text-gray-400 cursor-not-allowed whitespace-nowrap">
                        Next
                    </span>
                @endif
            </div>
        </div>


    </div>
@endif
