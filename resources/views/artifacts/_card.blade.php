<article class="card">
    @if($artifact->image)
        <img class="card-image" src="{{ asset('storage/' . $artifact->image) }}" alt="{{ $artifact->title }}">
    @else
        <div class="image-placeholder">Artifact</div>
    @endif
    <div class="card-body">
        <span class="badge">{{ $artifact->category->name }}</span>
        <h3><a href="{{ route('artifacts.show', $artifact) }}">{{ $artifact->title }}</a></h3>
        <p>{{ Str::limit($artifact->description, 115) }}</p>
        @if($artifact->period)<small>{{ $artifact->period }}</small>@endif
    </div>
</article>
