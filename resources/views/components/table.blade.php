@props(['columns' => [], 'rows' => [], 'empty' => 'No records found'])

<div class="overflow-x-auto -mx-5 px-5">
    <table class="w-full min-w-full text-sm">
        <thead>
            <tr class="border-b border-navy-100 dark:border-navy-700">
                @foreach ($columns as $column)
                    <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-navy-500 dark:text-navy-400 whitespace-nowrap">{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-navy-100 dark:divide-navy-700/60">
            @forelse ($rows as $row)
                <tr class="hover:bg-cream-50/60 dark:hover:bg-navy-800/40 transition-colors">
                    @foreach ($row as $cell)
                        <td class="px-4 py-3.5 text-navy-800 dark:text-navy-100 align-middle">{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="px-4 py-10 text-center text-navy-500 dark:text-navy-400">{{ $empty }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<slot />
