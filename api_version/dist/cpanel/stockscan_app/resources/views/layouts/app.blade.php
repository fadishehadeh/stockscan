<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'StockScan' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen text-slate-900">
    @auth
        <div class="app-shell">
            <div class="app-sidebar-overlay" data-sidebar-overlay></div>

            <aside class="app-sidebar" data-sidebar>
                <div class="app-sidebar-inner">
                    <div class="app-brand">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="eyebrow">StockScan</p>
                                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Inventory Control</h1>
                                <p class="mt-2 text-sm leading-6 text-slate-500">Fast stock tracking built for scanning, counting, and day-to-day product control.</p>
                            </div>
                            <button type="button" class="menu-button lg:hidden" data-sidebar-close aria-label="Close menu">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <nav class="app-nav">
                        @php
                            $sections = [
                                [
                                    'label' => 'Daily Work',
                                    'links' => [
                                        ['route' => 'dashboard', 'label' => 'Dashboard'],
                                        ['route' => 'scan.index', 'label' => 'Scan'],
                                        ['route' => 'alerts.index', 'label' => 'Alerts'],
                                        ['route' => 'products.index', 'label' => 'Products'],
                                        ['route' => 'transactions.index', 'label' => 'Movements'],
                                    ],
                                ],
                            ];

                            if (auth()->user()->isOwner()) {
                                $sections[] = [
                                    'label' => 'Management',
                                    'links' => [
                                        ['route' => 'categories.index', 'label' => 'Categories'],
                                        ['route' => 'reports.index', 'label' => 'Reports'],
                                        ['route' => 'imports.products.show', 'label' => 'Import'],
                                        ['route' => 'activity.index', 'label' => 'Activity'],
                                    ],
                                ];

                                $sections[] = [
                                    'label' => 'Administration',
                                    'links' => [
                                        ['route' => 'users.index', 'label' => 'Users'],
                                        ['route' => 'settings.edit', 'label' => 'Settings'],
                                    ],
                                ];
                            }
                        @endphp

                        @foreach ($sections as $index => $section)
                            @php
                                $hasActiveLink = collect($section['links'])->contains(
                                    fn (array $link) => request()->routeIs($link['route']) || request()->routeIs($link['route'] . '*')
                                );
                            @endphp

                            <details class="sidebar-section" {{ $hasActiveLink || $index === 0 ? 'open' : '' }}>
                                <summary class="sidebar-section-toggle">
                                    <span class="sidebar-section-title">{{ $section['label'] }}</span>
                                    <span class="sidebar-section-icon" aria-hidden="true">
                                        <svg viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </summary>

                                <div class="sidebar-section-body">
                                    @foreach ($section['links'] as $link)
                                        <a href="{{ route($link['route']) }}" class="nav-link {{ request()->routeIs($link['route']) || request()->routeIs($link['route'] . '*') ? 'nav-link-active' : '' }}">
                                            <span>{{ $link['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </nav>

                    <div class="account-card">
                        <p class="text-sm font-semibold text-slate-950">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ '@' . auth()->user()->username }} · {{ ucfirst(auth()->user()->role) }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button class="btn btn-secondary w-full">Logout</button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="app-main">
                <div class="app-topbar">
                    <div class="topbar-inner">
                        <div class="flex items-center gap-3">
                            <button type="button" class="menu-button" data-sidebar-open aria-label="Open menu">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path d="M3 5.75A.75.75 0 0 1 3.75 5h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 5.75Zm0 4.25a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 10Zm.75 3.5a.75.75 0 0 0 0 1.5h8.5a.75.75 0 0 0 0-1.5h-8.5Z"/>
                                </svg>
                            </button>
                            <div>
                                <p class="page-kicker">{{ now()->format('l, d M Y') }}</p>
                                <h2 class="page-title">{{ $heading ?? 'Overview' }}</h2>
                                <p class="page-subtitle">Operational inventory controls built for speed and daily visibility.</p>
                            </div>
                        </div>
                        <a href="{{ route('scan.index') }}" class="btn btn-primary">Quick Scan</a>
                    </div>
                </div>

                <div class="content-wrap">
                    @include('partials.flash')
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        <main class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,_rgba(14,165,233,0.12),_transparent_35%),linear-gradient(180deg,_#f8fafc,_#eef2ff)] px-4">
            @yield('content')
        </main>
    @endauth
</body>
</html>
