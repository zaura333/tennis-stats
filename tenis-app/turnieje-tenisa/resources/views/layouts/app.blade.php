<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Turnieje Tenisowe')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slate: {
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .gradient-card { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: #1e293b; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; border-radius: 2px; }
    </style>
</head>
<body class="bg-slate-950 text-gray-100 min-h-screen flex" style="background-color:#020617">

    {{-- Sidebar --}}
    <aside class="w-64 flex-shrink-0 flex flex-col min-h-screen border-r border-slate-800" style="background-color:#0f172a">

        {{-- Logo --}}
        <div class="p-6 border-b border-slate-800">
            <a href="{{ route('turnieje.index') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg,#2563eb,#1d4ed8)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-sm leading-tight">Tenis Archiwum</p>
                    <p class="text-xs text-slate-500">Archiwum turniejów</p>
                </div>
            </a>
        </div>

        {{-- Nawigacja --}}
        <nav class="flex-1 p-4 space-y-1">
            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider px-3 mb-3">Menu</p>

            <a href="{{ route('turnieje.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ request()->routeIs('turnieje.*') || request()->routeIs('edycje.*') || request()->routeIs('mecze.*')
                         ? 'bg-blue-600/20 text-blue-400 border border-blue-600/30'
                         : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                </svg>
                Turnieje
            </a>

            <a href="{{ route('zawodnicy.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                      {{ request()->routeIs('zawodnicy.*')
                         ? 'bg-blue-600/20 text-blue-400 border border-blue-600/30'
                         : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 flex-shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                Zawodnicy
            </a>
        </nav>

        {{-- Stopka sidebara --}}
        <div class="p-4 border-t border-slate-800">
            <a href="{{ route('turnieje.create') }}"
               class="flex items-center justify-center gap-2 w-full px-3 py-2.5 rounded-xl text-sm font-medium
                      bg-blue-600 hover:bg-blue-500 text-white transition-colors">
                <span>＋</span> Dodaj turniej
            </a>
        </div>
    </aside>

    {{-- Główna część strony --}}
    <div class="flex-1 flex flex-col min-h-screen overflow-hidden">

        {{-- Topbar --}}
        <header class="border-b border-slate-800 px-8 py-4 flex items-center justify-between flex-shrink-0"
                style="background-color:#0f172a">
            <div>
                <h1 class="text-xl font-bold text-white">@yield('heading', 'Panel główny')</h1>
                @hasSection('subheading')
                    <p class="text-sm text-slate-400 mt-0.5">@yield('subheading')</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('header-actions')
            </div>
        </header>

        {{-- Zawartość strony --}}
        <main class="flex-1 p-8 overflow-auto scrollbar-thin">

            {{-- Flash: sukces --}}
            @if(session('success'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-900/40 border border-emerald-700/50
                            text-emerald-300 px-4 py-3 rounded-xl text-sm">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            {{-- Flash: błędy walidacji --}}
            @if($errors->any())
                <div class="mb-6 bg-red-900/40 border border-red-700/50 text-red-300 px-4 py-3 rounded-xl text-sm">
                    <p class="font-semibold mb-1">Popraw błędy:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
