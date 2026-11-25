<article class="card">
    <div class="cover"><span class="tag">{{ $event->category->name }}</span></div>
    <div class="body">
        <h3><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h3>
        <div class="meta">{{ $event->starts_at->translatedFormat('j F, H:i') }} · {{ $event->location }}</div>
        <div class="progress {{ $event->free_seats === 0 ? 'full' : '' }}"><i style="width: {{ $event->fill_percent }}%"></i></div>
        <div class="foot">
            <span class="meta">{{ $event->free_seats > 0 ? 'Свободно мест: '.$event->free_seats.' из '.$event->capacity : 'Мест нет' }}</span>
            <span class="tag plain">{{ $event->points }} б.</span>
        </div>
    </div>
</article>
