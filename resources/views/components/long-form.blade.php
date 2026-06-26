@props(['text'])

@php
    $sections = preg_split('/\R{2,}/', trim($text));
    $isStructured = count($sections) > 1 && str_contains($sections[0], "\n");
@endphp

@if($isStructured)
    <div class="long-form">
        @foreach($sections as $section)
            @php
                [$heading, $body] = array_pad(explode("\n", trim($section), 2), 2, '');
            @endphp
            <section class="history-section">
                <h2>{{ Str::title(Str::lower($heading)) }}</h2>
                <p>{{ trim($body) }}</p>
            </section>
        @endforeach
    </div>
@else
    <p class="long-copy">{{ $text }}</p>
@endif
