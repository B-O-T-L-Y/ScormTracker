@foreach($packages as $scorm)
    <div>
        <strong>{{ $scorm->title }}</strong>
        <span>{{ $scorm->$scorm->format('d.m.Y') }}</span>
        <span>Views: {{ $scorm->stats->firstWhere('user_id', auth()->id())?->views_count ?? 0 }}</span>
        <a href="{{ route('scorm.show', $scorm) }}">Go</a>
    </div>
@endforeach
