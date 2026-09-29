@props([
    'activeNav' => 'dashboard',
    'connected' => true,
    'showHeader' => null,
])

@php
    $showHeader = $showHeader ?? $connected;
@endphp

@php
    $navItems = [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'monitor.dashboard'],
        'connection' => ['label' => 'Connection', 'icon' => 'link-2', 'route' => 'monitor.connect'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Carport Monitor' }}</title>

        <x-theme-init />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-screen overflow-hidden bg-background font-sans text-foreground">
        <div class="flex h-screen">
            <aside class="flex w-60 shrink-0 flex-col border-r border-border bg-sidebar">
                <div class="border-b border-border px-6 py-7">
                    <a
                        href="{{ route('monitor.dashboard') }}"
                        class="flex items-center gap-3 transition-colors duration-150 hover:opacity-90"
                        aria-label="Carport Monitor dashboard"
                    >
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary/15 text-primary">
                            <x-monitor.icon name="car" :size="17" />
                        </div>
                        <div>
                            <p class="font-display text-lg leading-none font-extrabold tracking-wide text-foreground uppercase">
                                Carport
                            </p>
                            <p class="font-display text-lg leading-none font-extrabold tracking-wide text-muted-foreground uppercase">
                                Monitor
                            </p>
                        </div>
                    </a>
                </div>

                <nav class="flex-1 space-y-1 px-3 py-4" aria-label="Main">
                    @foreach ($navItems as $id => $item)
                        @php
                            $isActive = $activeNav === $id;
                            $isConnection = $id === 'connection';
                        @endphp
                        <a
                            href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                            @class([
                                'flex w-full items-center gap-3 rounded-lg px-3 py-2.5 transition-colors duration-150',
                                'bg-primary/10 text-primary' => $isActive,
                                'text-muted-foreground hover:bg-secondary hover:text-foreground' => ! $isActive,
                            ])
                            @if ($isActive) aria-current="page" @endif
                        >
                            <x-monitor.icon :name="$item['icon']" :size="16" />
                            <span class="flex-1 font-mono text-[10px] tracking-widest uppercase">
                                {{ $item['label'] }}
                            </span>
                            @if ($isConnection)
                                <span
                                    @class([
                                        'h-2 w-2 shrink-0 rounded-full',
                                        'bg-success' => $connected,
                                        'bg-muted-foreground' => ! $connected,
                                    ])
                                    aria-hidden="true"
                                ></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-border px-6 py-5">
                    @auth
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-secondary text-primary">
                                <x-monitor.icon name="user" :size="14" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-sans text-[12px] font-medium text-foreground">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="truncate font-mono text-[9px] tracking-widest text-muted-foreground uppercase">
                                    Signed in
                                </p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center gap-2 rounded-lg px-2 py-2 font-mono text-[9px] tracking-widest text-muted-foreground uppercase transition-colors duration-150 hover:bg-secondary hover:text-foreground"
                            >
                                <x-monitor.icon name="log-out" :size="14" />
                                Sign out
                            </button>
                        </form>
                    @endauth

                    <x-monitor.theme-picker />

                    <p @class(['font-mono text-[9px] tracking-widest text-sidebar-muted uppercase', 'mt-4' => auth()->check()])>
                        V{{ config('app.version') }} · Read-only
                    </p>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                @if ($showHeader)
                    <header class="flex h-14 shrink-0 items-center justify-end border-b border-border px-8">
                        <p class="font-mono text-[9px] tracking-widest text-muted-foreground/50 uppercase">
                            Read-only · No edits available
                        </p>
                    </header>
                @endif

                <main class="flex-1 overflow-hidden">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
