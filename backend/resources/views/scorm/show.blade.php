@extends('layouts.app')

@section('title', 'SCORM Player')

@push('head')
    <style>
        html, body, #scormFrame { height: 100%; }
        #scormFrame { width: 100%; height: calc(100vh - 7rem); display: block; border: 0; }
    </style>
    <script>
        (function () {
            const mem = {};
            const ok = () => "true";
            const zero = () => "0";
            const empty = () => "";
            const getValue = name => (name in mem) ? String(mem[name]) : "";
            const setValue = (name, val) => { mem[name] = String(val); return "true"; };

            // SCORM 1.2
            window.API = {
                LMSInitialize: ok, LMSFinish: ok, LMSCommit: ok,
                LMSGetValue: getValue, LMSSetValue: setValue,
                LMSGetLastError: zero, LMSGetErrorString: empty, LMSGetDiagnostic: empty
            };
            // SCORM 2004
            window.API_1484_11 = {
                Initialize: ok, Terminate: ok, Commit: ok,
                GetValue: getValue, SetValue: setValue,
                GetLastError: zero, GetErrorString: empty, GetDiagnostic: empty
            };

            // Toggle logs if needed
            const LOG = false;
            if (LOG) {
                const wrap = (obj, name) => new Proxy(obj, {
                    get(t, p) {
                        const v = t[p];
                        if (typeof v === 'function') {
                            return (...args) => { console.log(`[${name}] ${String(p)}`, ...args); return v(...args); };
                        }
                        return v;
                    }
                });
                window.API = wrap(window.API, 'SCORM 1.2');
                window.API_1484_11 = wrap(window.API_1484_11, 'SCORM 2004');
            }
        })();
    </script>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto px-4">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-2xl font-semibold">SCORM Player</h1>
            <a href="{{ route('scorm.index') }}" class="text-sm text-zinc-500 hover:text-zinc-700">Back to list</a>
        </div>
    </div>

    <iframe id="scormFrame" src="{{ $url }}" allowfullscreen></iframe>
@endsection
