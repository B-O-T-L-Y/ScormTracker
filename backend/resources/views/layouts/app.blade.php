<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SCORM')</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @stack('head')
</head>
<body class="min-h-full bg-zinc-50 text-zinc-900 antialiased">
<header class="bg-white border-b border-zinc-200">
    <div class="max-w-6xl mx-auto px-4 h-14 flex items-center justify-between">
        <a href="{{ route('scorm.index') }}" class="font-semibold">SCORM Demo</a>

        @php($user = Auth::user())
        @if($user)
            <details class="relative group">
                <summary class="list-none flex items-center gap-3 cursor-pointer select-none">
                    <span class="hidden sm:block text-sm text-zinc-700">
                        {{ $user->name }}
                        @if(method_exists($user, 'getAttribute') && $user->getAttribute('is_admin'))
                            <span class="ml-1 rounded bg-amber-100 text-amber-800 px-1.5 py-0.5 text-[10px]">admin</span>
                        @endif
                    </span>
                    <span
                        class="inline-flex size-8 items-center justify-center rounded-full bg-zinc-100 text-xs font-medium text-zinc-700">
                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                    </span>
                </summary>

                <div
                    class="absolute right-0 mt-2 w-40 rounded-md border border-zinc-200 bg-white shadow-md py-1 text-sm">
                    <a href="{{ route('scorm.index') }}" class="block px-3 py-1.5 hover:bg-zinc-50">My courses</a>

                    @if(Route::has('admin') || Route::has('filament.admin.pages.dashboard'))
                        <a href="{{ route('filament.admin.pages.dashboard') ?? route('admin') }}"
                           class="block px-3 py-1.5 hover:bg-zinc-50">Admin panel</a>
                    @endif

                    @if(Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-zinc-50">
                                Sign out
                            </button>
                        </form>
                    @elseif(Route::has('filament.admin.auth.logout'))
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-zinc-50">
                                Sign out
                            </button>
                        </form>
                    @endif
                </div>
            </details>
        @else
            <div class="flex items-center gap-3">
                @if(Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="text-sm text-zinc-600 hover:text-zinc-900">Sign in</a>
                @elseif(Route::has('filament.admin.auth.login'))
                    <a href="{{ route('filament.admin.auth.login') }}"
                       class="text-sm text-zinc-600 hover:text-zinc-900">Sign in</a>
                @else
                    <span class="text-sm text-zinc-500">Guest</span>
                @endif
            </div>
        @endif
    </div>
</header>

<main class="py-8">
    @yield('content')
</main>

<footer class="mt-8 py-6 text-center text-xs text-zinc-500">
    &copy; {{ date('Y') }} SCORM Demo
</footer>

@stack('scripts')
</body>
</html>
