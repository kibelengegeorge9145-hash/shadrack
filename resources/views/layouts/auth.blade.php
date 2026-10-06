<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Authentication') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-cream-50 dark:bg-navy-950">
    <div class="min-h-full flex flex-col lg:flex-row">

        {{-- Brand panel --}}
        <div class="relative order-2 lg:order-1 lg:w-[42%] lg:flex flex-col justify-between bg-navy-950 text-white p-8 lg:p-12 overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background: radial-gradient(circle at 20% 20%, #c9a227 0%, transparent 45%), radial-gradient(circle at 80% 80%, #c9a227 0%, transparent 40%);"></div>
            <div class="relative">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-gold-400 text-navy-950 font-display font-extrabold text-xl">E</span>
                    <span class="leading-tight">
                        <span class="block font-display font-bold text-white text-xl tracking-tight">Ebenezer</span>
                        <span class="block text-[10px] uppercase tracking-[0.2em] text-gold-300">Secondary School</span>
                    </span>
                </a>
            </div>
            <div class="relative mt-16 lg:mt-0 max-w-sm">
                <h1 class="font-display text-3xl font-bold leading-tight">Excellence in education, nurtured with character and integrity.</h1>
                <p class="mt-4 text-navy-300 text-sm leading-relaxed">The official portal for students, parents, teachers and staff of Ebenezer Secondary School.</p>
            </div>
            <p class="relative text-xs text-navy-500 mt-8">© {{ date('Y') }} Ebenezer Secondary School</p>
        </div>

        {{-- Form panel --}}
        <div class="order-1 lg:order-2 flex-1 flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-navy-950 text-gold-300 font-display font-extrabold text-xl">E</span>
                        <span class="leading-tight">
                            <span class="block font-display font-bold text-navy-950 dark:text-white text-xl tracking-tight">Ebenezer</span>
                            <span class="block text-[10px] uppercase tracking-[0.2em] text-gold-600 dark:text-gold-300">Secondary School</span>
                        </span>
                    </a>
                </div>
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
