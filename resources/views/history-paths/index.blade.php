@extends('layouts.app')
@section('title', 'History Paths — GeoArchive')
@section('content')
    <header class="path-intro">
        <p class="eyebrow">Connected history</p>
        <h1>History paths through Georgia</h1>
        <p>Artifacts do not stand alone. Choose a path to follow objects, monuments, people, and turning points as one connected story. Every stop opens its complete archive entry.</p>
    </header>

    <div class="history-path-list">
        @foreach($paths as $path)
            <section class="history-path" aria-labelledby="path-{{ $loop->iteration }}">
                <div class="history-path-heading">
                    <span class="path-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <h2 id="path-{{ $loop->iteration }}">{{ $path['title'] }}</h2>
                        <p>{{ $path['summary'] }}</p>
                    </div>
                </div>

                <ol class="path-nodes">
                    @foreach($path['nodes'] as $node)
                        @php
                            $record = $node['record'];
                            $isArtifact = $node['kind'] === 'artifact';
                            $href = $isArtifact ? route('artifacts.show', $record) : route('events.show', $record);
                            $label = $isArtifact ? $record->category->name : 'Historical event';
                            $period = $isArtifact ? $record->period : $record->date_or_period;
                        @endphp
                        <li>
                            <a class="path-node" href="{{ $href }}">
                                <img src="{{ asset('storage/' . $record->image) }}" alt="">
                                <span class="path-node-copy">
                                    <small>{{ $label }}</small>
                                    <strong>{{ $record->title }}</strong>
                                    <span>{{ $period }}</span>
                                </span>
                                <span class="path-arrow" aria-hidden="true">→</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endforeach
    </div>
@endsection
