<div class="report-header text-center border-b-2 border-navy-900 pb-4 mb-6">
    <div class="flex items-center justify-center gap-4">
        <div class="school-logo flex items-center justify-center w-16 h-16 rounded-full bg-navy-900 text-gold-300 font-display font-extrabold text-2xl">
            E
        </div>
        <div class="text-left">
            <h1 class="font-display text-2xl md:text-3xl font-extrabold text-navy-900 tracking-tight uppercase">{{ config('app.name') }}</h1>
            <p class="text-xs uppercase tracking-[0.22em] text-navy-500 mt-0.5">P.O. Box 0000, Ebenezer, Tanzania · Tel: +255 700 000 000</p>
            <p class="text-xs text-navy-500">Email: info@ebenezer.edu.tz · www.ebenezer.edu.tz</p>
        </div>
    </div>
    <h2 class="mt-3 inline-block border-2 border-gold-400 px-8 py-1.5 font-display text-xl md:text-2xl font-bold text-navy-900 tracking-wide">{{ $reportTitle ?? 'ACADEMIC REPORT' }}</h2>
    @if (!empty($reportSubtitle))
        <p class="mt-1 text-sm font-semibold text-navy-600">{{ $reportSubtitle }}</p>
    @endif
    @if (!empty($reference))
        <p class="mt-1 text-[11px] text-navy-400">Ref: {{ $reference }} · Generated {{ $generatedAt ?? now()->format('d F Y') }}</p>
    @endif
</div>
