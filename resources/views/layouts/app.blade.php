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
                            <div class="space-y-4">
                                <div class="app-brand-mark">
                                    <div class="app-brand-logo" aria-hidden="true">
                                        <svg viewBox="0 0 64 64" fill="none">
                                            <rect x="6" y="6" width="52" height="52" rx="16" fill="url(#stockscanLogoGradient)" />
                                            <path d="M20 21h4v22h-4V21Zm7 0h2v22h-2V21Zm5 0h6v22h-6V21Zm9 0h2v22h-2V21Z" fill="#fff" />
                                            <path d="M18 47h28" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                                            <defs>
                                                <linearGradient id="stockscanLogoGradient" x1="6" y1="6" x2="58" y2="58" gradientUnits="userSpaceOnUse">
                                                    <stop stop-color="#0F6CBD" />
                                                    <stop offset="1" stop-color="#0F9CB5" />
                                                </linearGradient>
                                            </defs>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="eyebrow">StockScan</p>
                                        <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-950">Inventory Control</h1>
                                    </div>
                                </div>
                                <p class="text-sm leading-6 text-slate-500">Fast stock tracking built for scanning, counting, and day-to-day product control.</p>
                            </div>
                            <button type="button" class="menu-button lg:hidden" data-sidebar-close aria-label="Close menu">
                                <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 0 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <nav class="app-nav">
                        @php
                            $icon = function (string $name): string {
                                return match ($name) {
                                    'dashboard' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M3 10.75A1.75 1.75 0 0 1 4.75 9h2.5A1.75 1.75 0 0 1 9 10.75v4.5A1.75 1.75 0 0 1 7.25 17h-2.5A1.75 1.75 0 0 1 3 15.25v-4.5ZM11 4.75A1.75 1.75 0 0 1 12.75 3h2.5A1.75 1.75 0 0 1 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17h-2.5A1.75 1.75 0 0 1 11 15.25V4.75ZM4.75 4h2.5a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-.75.75h-2.5A.75.75 0 0 1 4 7.25v-2.5A.75.75 0 0 1 4.75 4Z" /></svg>',
                                    'scan' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M4 3.75A1.75 1.75 0 0 1 5.75 2h2.5a.75.75 0 0 1 0 1.5h-2.5a.25.25 0 0 0-.25.25v2.5a.75.75 0 0 1-1.5 0v-2.5ZM11 2.75A.75.75 0 0 1 11.75 2h2.5A1.75 1.75 0 0 1 16 3.75v2.5a.75.75 0 0 1-1.5 0v-2.5a.25.25 0 0 0-.25-.25h-2.5A.75.75 0 0 1 11 2.75ZM4.75 11a.75.75 0 0 1 .75.75v2.5a.25.25 0 0 0 .25.25h2.5a.75.75 0 0 1 0 1.5h-2.5A1.75 1.75 0 0 1 4 14.25v-2.5A.75.75 0 0 1 4.75 11Zm10.5 0a.75.75 0 0 1 .75.75v2.5A1.75 1.75 0 0 1 14.25 16h-2.5a.75.75 0 0 1 0-1.5h2.5a.25.25 0 0 0 .25-.25v-2.5a.75.75 0 0 1 .75-.75ZM7 7.25a.75.75 0 0 1 .75-.75h1a.75.75 0 0 1 0 1.5h-1A.75.75 0 0 1 7 7.25Zm0 2.75a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5h-4.5A.75.75 0 0 1 7 10Zm0 2.75A.75.75 0 0 1 7.75 12h4.5a.75.75 0 0 1 0 1.5h-4.5A.75.75 0 0 1 7 12.75Z" /></svg>',
                                    'products' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M4.5 3A1.5 1.5 0 0 0 3 4.5v11A1.5 1.5 0 0 0 4.5 17h11a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 15.5 3h-11Zm.75 3.25A.75.75 0 0 1 6 5.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1-.75-.75Zm0 4A.75.75 0 0 1 6 9.5h8a.75.75 0 0 1 0 1.5H6a.75.75 0 0 1-.75-.75Zm.75 3.25a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5h-4Z" /></svg>',
                                    'categories' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 3.25a1.75 1.75 0 0 0-2.474 0L3.25 8.276a1.75 1.75 0 0 0 0 2.474l5.026 5.026a1.75 1.75 0 0 0 2.474 0l5.026-5.026a1.75 1.75 0 0 0 0-2.474L10.75 3.25Zm-1.97 4.28a1.25 1.25 0 1 1-1.768 1.768A1.25 1.25 0 0 1 8.78 7.53Z" /></svg>',
                                    'movements' => '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.22 10.22a.75.75 0 0 1 1.06 0l2.5 2.5a.75.75 0 0 1-1.06 1.06L4.5 12.56V16a.75.75 0 0 1-1.5 0v-3.44l-1.22 1.22a.75.75 0 1 1-1.06-1.06l2.5-2.5Zm13.56-.44a.75.75 0 0 1 0 1.06l-2.5 2.5a.75.75 0 1 1-1.06-1.06L14.44 11H11a.75.75 0 0 1 0-1.5h3.44l-1.22-1.22a.75.75 0 0 1 1.06-1.06l2.5 2.5Z" clip-rule="evenodd" /></svg>',
                                    'alerts' => '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.58 9.92c.75 1.334-.213 2.981-1.742 2.981H4.419c-1.53 0-2.492-1.647-1.742-2.98l5.58-9.92ZM11 7a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0V7Zm-1 7a1.25 1.25 0 1 0 0-2.5A1.25 1.25 0 0 0 10 14Z" clip-rule="evenodd" /></svg>',
                                    'reports' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M4.75 3A1.75 1.75 0 0 0 3 4.75v10.5C3 16.216 3.784 17 4.75 17h10.5A1.75 1.75 0 0 0 17 15.25V4.75A1.75 1.75 0 0 0 15.25 3H4.75ZM6 13.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 6 13.25Zm3-3a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 9 10.25Zm3-3a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5A.75.75 0 0 1 12 7.25Z" /></svg>',
                                    'import' => '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2.75a.75.75 0 0 1 .75.75v7.69l1.72-1.72a.75.75 0 1 1 1.06 1.06l-3 3a.75.75 0 0 1-1.06 0l-3-3a.75.75 0 1 1 1.06-1.06l1.72 1.72V3.5A.75.75 0 0 1 10 2.75ZM4.5 13a.75.75 0 0 1 .75.75v1a.75.75 0 0 0 .75.75h8a.75.75 0 0 0 .75-.75v-1a.75.75 0 0 1 1.5 0v1A2.25 2.25 0 0 1 14 17h-8a2.25 2.25 0 0 1-2.25-2.25v-1A.75.75 0 0 1 4.5 13Z" clip-rule="evenodd" /></svg>',
                                    'activity' => '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 4a.75.75 0 0 1 .75.75v4.94l3.03 1.75a.75.75 0 1 1-.75 1.3l-3.4-1.96a.75.75 0 0 1-.38-.65V4.75A.75.75 0 0 1 10 4ZM10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z" clip-rule="evenodd" /></svg>',
                                    'users' => '<svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5 16.25A4.25 4.25 0 0 1 9.25 12h1.5A4.25 4.25 0 0 1 15 16.25a.75.75 0 0 1-.75.75h-8.5a.75.75 0 0 1-.75-.75Z" /></svg>',
                                    'settings' => '<svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.84 2.66A1.75 1.75 0 0 1 9.5 2h1a1.75 1.75 0 0 1 1.66.66l.39.52c.17.22.44.35.72.35h.6a1.75 1.75 0 0 1 1.65 1.17l.3.9c.09.27.3.48.57.57l.9.3A1.75 1.75 0 0 1 18 8.13v.6c0 .28.13.55.35.72l.52.39a1.75 1.75 0 0 1 .66 1.66v1a1.75 1.75 0 0 1-.66 1.66l-.52.39a.9.9 0 0 0-.35.72v.6a1.75 1.75 0 0 1-1.17 1.65l-.9.3a.9.9 0 0 0-.57.57l-.3.9A1.75 1.75 0 0 1 13.87 18h-.6a.9.9 0 0 0-.72.35l-.39.52A1.75 1.75 0 0 1 10.5 19h-1a1.75 1.75 0 0 1-1.66-.66l-.39-.52a.9.9 0 0 0-.72-.35h-.6a1.75 1.75 0 0 1-1.65-1.17l-.3-.9a.9.9 0 0 0-.57-.57l-.9-.3A1.75 1.75 0 0 1 2 13.87v-.6a.9.9 0 0 0-.35-.72l-.52-.39A1.75 1.75 0 0 1 .47 10.5v-1c0-.55.25-1.07.66-1.4l.52-.39A.9.9 0 0 0 2 7.13v-.6a1.75 1.75 0 0 1 1.17-1.65l.9-.3a.9.9 0 0 0 .57-.57l.3-.9A1.75 1.75 0 0 1 6.13 2h.6c.28 0 .55-.13.72-.35l.39-.52ZM10 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" /></svg>',
                                    default => '<svg viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="6" /></svg>',
                                };
                            };

                            $sections = [
                                [
                                    'label' => 'Daily Work',
                                    'links' => [
                                        ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard'],
                                        ['route' => 'scan.index', 'label' => 'Scan', 'icon' => 'scan'],
                                        ['route' => 'products.index', 'label' => 'Products', 'icon' => 'products'],
                                        ['route' => 'categories.index', 'label' => 'Categories', 'icon' => 'categories'],
                                    ],
                                ],
                                [
                                    'label' => 'Management',
                                    'links' => [
                                        ['route' => 'transactions.index', 'label' => 'Movements', 'icon' => 'movements'],
                                        ['route' => 'alerts.index', 'label' => 'Alerts', 'icon' => 'alerts'],
                                    ],
                                ],
                            ];

                            if (auth()->user()->isOwner() || auth()->user()->isSuperAdmin()) {
                                $sections[] = [
                                    'label' => 'Reports',
                                    'links' => [
                                        ['route' => 'reports.index', 'label' => 'Reports', 'icon' => 'reports'],
                                        ['route' => 'imports.products.show', 'label' => 'Import', 'icon' => 'import'],
                                        ['route' => 'activity.index', 'label' => 'Activity', 'icon' => 'activity'],
                                    ],
                                ];
                            }

                            // Settings available to all authenticated users
                            $sections[] = [
                                'label' => 'Settings',
                                'links' => [
                                    ['route' => 'settings.dashboard', 'label' => 'Settings', 'icon' => 'settings'],
                                ],
                            ];
                        @endphp

                        @foreach ($sections as $index => $section)
                            @php
                                $visibleLinks = collect($section['links'])->filter(fn (array $link) => empty($link['owner_only']) || auth()->user()->isOwner());
                                $hasActiveLink = $visibleLinks->contains(
                                    fn (array $link) => isset($link['route']) && (request()->routeIs($link['route']) || request()->routeIs($link['route'] . '*'))
                                );
                            @endphp

                            @if ($visibleLinks->isNotEmpty())
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
                                        @foreach ($visibleLinks as $link)
                                            <a href="{{ route($link['route']) }}" class="nav-link {{ request()->routeIs($link['route']) || request()->routeIs($link['route'] . '*') ? 'nav-link-active' : '' }}">
                                                <span class="nav-link-main">
                                                    <span class="nav-link-glyph" aria-hidden="true">{!! $icon($link['icon']) !!}</span>
                                                    <span>{{ $link['label'] }}</span>
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        @endforeach
                    </nav>

                    <div class="account-card">
                        <p class="text-sm font-semibold text-slate-950">{{ auth()->user()->name }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ '@' . auth()->user()->username }} · {{ ucfirst(auth()->user()->role) }}</p>
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
                        <div class="flex items-center gap-4">
                            @if (auth()->user()->isSuperAdmin() || auth()->user()->isAdmin())
                                <a href="{{ route('products.create') }}" class="btn btn-success">Add Product</a>
                            @endif
                            <a href="{{ route('scan.index') }}" class="btn btn-primary">Quick Scan</a>

                            <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                            <path fill-rule="evenodd" d="M3 4.75A2.75 2.75 0 0 1 5.75 2h8.5A2.75 2.75 0 0 1 17 4.75v10.5A2.75 2.75 0 0 1 14.25 18h-8.5A2.75 2.75 0 0 1 3 15.25V4.75Zm9.5 7a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" clip-rule="evenodd" />
                                        </svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
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
