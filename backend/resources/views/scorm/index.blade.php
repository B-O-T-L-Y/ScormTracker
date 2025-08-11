@extends('layouts.app')

@section('title', 'SCORM Packages')

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">SCORM Packages</h1>
        </div>

        @if($packages->isEmpty())
            <div class="bg-amber-50 text-amber-800 border border-amber-200 rounded-lg p-6">
                No packages uploaded yet.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($packages as $scorm)
                    @php $my = $scorm->stats->first(); @endphp

                    <div class="rounded-xl border border-zinc-200 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <h3 class="text-lg font-medium line-clamp-2">{{ $scorm->title }}</h3>

                            <div class="mt-3 space-y-1 text-sm text-zinc-600">
                                <div>
                                    Uploaded:
                                    <span class="font-medium text-zinc-800">
                                        {{ $scorm->created_at?->format('d.m.Y H:i') }}
                                    </span>
                                </div>
                                <div>
                                    Views: <span class="font-medium">{{ $my->views_count ?? 0 }}</span>
                                </div>
                                <div>
                                    Last viewed:
                                    <span class="font-medium">
                                        {{ optional($my?->last_viewed_at)->diffForHumans() ?? '—' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center gap-2">
                                <a href="{{ route('scorm.show', $scorm) }}"
                                   class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg bg-amber-500 text-white hover:bg-amber-600">
                                    Start
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $packages->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
