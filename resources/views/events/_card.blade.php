<article class="card">
    @if($event->image)
        <img class="card-image" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
    @else
        <div class="image-placeholder event">Event</div>
    @endif
    <div class="card-body">
        @if($event->date_or_period)<span class="badge gold">{{ $event->date_or_period }}</span>@endif
        <h3><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h3>
        <p>{{ Str::limit($event->description, 115) }}</p>
        @if($event->location)<small>{{ $event->location }}</small>@endif
    </div>
</article>
