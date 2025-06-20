@props(['rating' => 0, 'size' => 'default'])

@php
    $roundedRating = round($rating ?? 0);
    $starClass = $size === 'small' ? 'w-4 h-4' : 'w-5 h-5';
@endphp

<div class="flex items-center">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $starClass }} {{ $i <= $roundedRating ? 'text-yellow-400' : 'text-gray-500' }}" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.447a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.447a1 1 0 00-1.176 0l-3.368 2.447c-.784.57-1.838-.197-1.54-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.34 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"/>
        </svg>
    @endfor
    @if(isset($rating) && $rating > 0)
    <span class="text-gray-400 text-xs ml-1">({{ number_format($rating, 1) }})</span>
    @endif
</div>
