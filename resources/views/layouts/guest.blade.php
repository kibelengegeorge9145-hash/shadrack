<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-white dark:bg-navy-950">

    <header class="sticky top-0 z-30 bg-white/90 dark:bg-navy-950/90 backdrop-blur border-b border-navy-100 dark:border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-18 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-navy-950 text-gold-300 font-display font-extrabold text-lg">E</span>
                    <span class="leading-tight">
                        <span class="block font-display font-bold text-navy-950 dark:text-white text-lg tracking-tight">Ebenezer</span>
                        <span class="block text-[10px] uppercase tracking-[0.18em] text-gold-600 dark:text-gold-300">Secondary School</span>
                    </span>
                </a>

                <nav data-nav class="absolute left-0 right-0 top-full bg-white dark:bg-navy-950 border-b border-navy-100 dark:border-navy-800 hidden lg:static lg:flex lg:border-0 lg:bg-transparent">
                    <ul class="flex flex-col lg:flex-row lg:items-center gap-1 p-4 lg:p-0">
                        <li><a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Home</a></li>
                        <li><a href="{{ route('about') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">About</a></li>
                        <li><a href="{{ route('academics') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Academics</a></li>
                        <li><a href="{{ route('olevel') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">O-Level</a></li>
                        <li><a href="{{ route('alevel') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">A-Level</a></li>
                        <li><a href="{{ route('admissions') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Admissions</a></li>
                        <li><a href="{{ route('announcements') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Announcements</a></li>
                        <li><a href="{{ route('news.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">News</a></li>
                        <li><a href="{{ route('events.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Events</a></li>
                        <li><a href="{{ route('contact') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-navy-800 dark:text-navy-100 hover:text-gold-600 dark:hover:text-gold-300">Contact</a></li>
                    </ul>
                </nav>

                <div class="flex items-center gap-2">
                    <button data-theme-toggle class="p-2 rounded-lg text-navy-600 dark:text-navy-200 hover:bg-navy-50 dark:hover:bg-navy-800">
                        <x-icon name="moon" class="w-5 h-5 dark:hidden" />
                        <x-icon name="sun" class="w-5 h-5 hidden dark:block" />
                    </button>
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-2 rounded-lg bg-navy-950 text-white px-4 py-2 text-sm font-semibold hover:bg-navy-800 dark:bg-gold-400 dark:text-navy-950 dark:hover:bg-gold-300 transition-colors">
                        <x-icon name="log-in" class="w-4 h-4" /> Login
                    </a>
                    <button data-nav-toggle class="lg:hidden p-2 rounded-lg text-navy-700 dark:text-navy-200 hover:bg-navy-50 dark:hover:bg-navy-800">
                        <x-icon name="menu" class="w-6 h-6" />
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-navy-950 text-navy-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-14">
            <div class="grid md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gold-400 text-navy-950 font-display font-extrabold text-lg">E</span>
                        <span class="leading-tight">
                            <span class="block font-display font-bold text-white text-lg">Ebenezer</span>
                            <span class="block text-[10px] uppercase tracking-[0.18em] text-gold-300">Secondary School</span>
                        </span>
                    </div>
                    <p class="text-sm text-navy-400 max-w-sm">A centre of academic excellence and character formation, nurturing the next generation of leaders in Tanzania.</p>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Quick Links</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('academics') }}" class="hover:text-gold-300">Academics</a></li>
                        <li><a href="{{ route('admissions') }}" class="hover:text-gold-300">Admissions</a></li>
                        <li><a href="{{ route('admissions.status-form') }}" class="hover:text-gold-300">Application Status</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-gold-300">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-gold-300">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold text-white mb-3">Reach Us</p>
                    <ul class="space-y-2 text-sm text-navy-400">
                        <li class="flex items-center gap-2"><x-icon name="map-pin" class="w-4 h-4 text-gold-300" /> East Region, Tanzania</li>
                        <li class="flex items-center gap-2"><x-icon name="phone" class="w-4 h-4 text-gold-300" /> +255 000 000 000</li>
                        <li class="flex items-center gap-2"><x-icon name="mail" class="w-4 h-4 text-gold-300" /> info@ebenezer.edu.tz</li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-6 border-t border-navy-900 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-navy-500">© {{ date('Y') }} Ebenezer Secondary School. All rights reserved.</p>
                <div class="flex gap-5 text-xs text-navy-500">
                    <a href="#" class="hover:text-gold-300">Privacy</a>
                    <a href="#" class="hover:text-gold-300">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
