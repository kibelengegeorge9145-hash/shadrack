<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full" data-page-layout="app">

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-navy-950/50 hidden lg:hidden" data-sidebar-overlay></div>

    <div class="flex h-full">

        {{-- === SIDEBAR === --}}
        <aside id="app-sidebar"
            class="fixed inset-y-0 left-0 z-40 w-72 flex flex-col -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300
                   bg-navy-950 text-navy-200 border-r border-navy-900"
            aria-label="Sidebar">

            <div class="flex items-center gap-3 px-6 h-18 py-5">
                <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gold-400 text-navy-950 font-display font-extrabold text-lg">E</span>
                <div class="leading-tight">
                    <p class="font-display font-bold text-white text-base tracking-tight">Ebenezer</p>
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gold-300">Secondary School</p>
                </div>
                <button data-sidebar-close class="ml-auto lg:hidden text-navy-400 hover:text-white">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 pb-4 space-y-4">
                @php
                    $menus = \App\Support\Navigation::menuFor(auth()->user());
                    $request = request();
                @endphp
                @foreach ($menus as $group => $items)
                    <div>
                        <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-navy-500">{{ $group }}</p>
                        <ul class="space-y-0.5">
                            @foreach ($items as $item)
                                @php $active = $request->routeIs($item['route']) || (str_contains($request->route()?->getName() ?? '', rtrim($item['route'], '.index')) && $request->routeIs(rtrim($item['route'], '.index').'.show')); @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}"
                                        @class(['group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                                                'bg-gold-400 text-navy-950 shadow-sm' => $active,
                                                'text-navy-200 hover:bg-navy-900 hover:text-white' => !$active])>
                                        <x-icon :name="$item['icon']" class="w-5 h-5 shrink-0" />
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            <div class="px-4 py-4 border-t border-navy-900">
                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-navy-300 hover:bg-navy-900 hover:text-white transition-colors">
                    <x-icon name="globe" class="w-5 h-5" />
                    <span>Public Website</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm text-navy-300 hover:bg-red-950/40 hover:text-red-300 transition-colors">
                        <x-icon name="logout" class="w-5 h-5" />
                        <span>Sign out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- === MAIN === --}}
        <div class="flex-1 flex flex-col min-w-0 min-h-0">
            {{-- Topbar --}}
            <header class="sticky top-0 z-20 flex items-center gap-3 px-4 sm:px-6 h-16 bg-white/85 dark:bg-navy-900/85 backdrop-blur border-b border-navy-100 dark:border-navy-700">
                <button data-sidebar-open class="lg:hidden text-navy-700 dark:text-navy-200">
                    <x-icon name="menu" class="w-6 h-6" />
                </button>

                <div class="hidden sm:block text-sm">
                    <span class="text-navy-400 dark:text-navy-500">{{ ucfirst(auth()->user()->role) }} ·</span>
                    <span class="font-medium text-navy-800 dark:text-navy-100">{{ auth()->user()->display_name }}</span>
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <button data-theme-toggle class="p-2.5 rounded-lg text-navy-600 dark:text-navy-200 hover:bg-navy-50 dark:hover:bg-navy-800 transition-colors" title="Toggle theme">
                        <x-icon name="moon" class="w-5 h-5 dark:hidden" />
                        <x-icon name="sun" class="w-5 h-5 hidden dark:block" />
                    </button>

                    <div class="relative" data-dropdown data-dropdown-auto>
                        <button data-dropdown-trigger class="p-2.5 rounded-lg text-navy-600 dark:text-navy-200 hover:bg-navy-50 dark:hover:bg-navy-800 relative">
                            <x-icon name="bell" class="w-5 h-5" />
                            @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                            @if ($unreadCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500"></span>
                            @endif
                        </button>
                        <div data-dropdown-menu class="hidden absolute right-0 z-50 mt-2 w-80 sm:w-96 rounded-xl border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-900 shadow-card-hover p-1.5">
                            <div class="flex items-center justify-between px-3 py-2">
                                <p class="font-semibold text-sm text-navy-800 dark:text-navy-100">Notifications</p>
                                <span class="text-xs text-navy-400">{{ $unreadCount }} unread</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse (auth()->user()->notifications()->limit(8)->get() as $notification)
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="flex gap-3 rounded-lg px-3 py-2.5 hover:bg-cream-50 dark:hover:bg-navy-800">
                                        <span class="mt-1.5 w-2 h-2 rounded-full {{ $notification->read_at ? 'bg-navy-200 dark:bg-navy-600' : 'bg-gold-400' }} shrink-0"></span>
                                        <span class="min-w-0">
                                            <span class="block text-sm text-navy-800 dark:text-navy-100 truncate">{{ $notification->data['title'] ?? 'Notification' }}</span>
                                            <span class="block text-xs text-navy-500 dark:text-navy-400 truncate">{{ $notification->data['body'] ?? '' }}</span>
                                        </span>
                                    </a>
                                @empty
                                    <p class="px-3 py-6 text-center text-sm text-navy-400">No notifications</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="relative" data-dropdown data-dropdown-auto>
                        <button data-dropdown-trigger class="flex items-center gap-2.5 rounded-full p-1 pr-1 sm:pr-3 hover:bg-navy-50 dark:hover:bg-navy-800 transition-colors">
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-navy-800 text-gold-300 text-sm font-bold">
                                {{ auth()->user()->initials }}
                            </span>
                            <span class="hidden sm:block text-left leading-tight">
                                <span class="block text-sm font-semibold text-navy-800 dark:text-navy-100">{{ auth()->user()->full_name }}</span>
                                <span class="block text-[11px] text-navy-500 capitalize">{{ auth()->user()->role }}</span>
                            </span>
                            <x-icon name="chevron-down" class="hidden sm:block w-4 h-4 text-navy-400" />
                        </button>
                        <div data-dropdown-menu class="hidden absolute right-0 z-50 mt-2 w-56 rounded-xl border border-navy-100 dark:border-navy-700 bg-white dark:bg-navy-900 shadow-card-hover p-1.5">
                            <div class="px-3 py-2.5 border-b border-navy-100 dark:border-navy-700 mb-1">
                                <p class="text-sm font-semibold text-navy-800 dark:text-navy-100">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-navy-500">{{ auth()->user()->email ?? auth()->user()->phone }}</p>
                            </div>
                            <a href="{{ route('account.profile') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800"><x-icon name="user" class="w-4 h-4" /> Profile</a>
                            <a href="{{ route('account.password.change') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-navy-700 dark:text-navy-200 hover:bg-cream-50 dark:hover:bg-navy-800"><x-icon name="lock" class="w-4 h-4" /> Change Password</a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1 pt-1 border-t border-navy-100 dark:border-navy-700">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40"><x-icon name="logout" class="w-4 h-4" /> Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
