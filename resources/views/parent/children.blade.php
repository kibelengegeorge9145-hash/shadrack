@extends('layouts.app')

@section('title', 'My Children')

@section('content')
    <x-page-header title="My Children" subtitle="Students linked to your account" />

    @if ($children->isEmpty())
        <x-card>
            <x-empty-state icon="users" title="No Children" message="No children are linked to your parent account." />
        </x-card>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($children as $child)
                <x-card>
                    <div class="flex items-center gap-4 mb-4">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-navy-800 text-gold-300 text-lg font-bold shrink-0">{{ $child->initials }}</span>
                        <div>
                            <h3 class="font-display font-bold text-navy-900 dark:text-navy-100">{{ $child->full_name }}</h3>
                            <p class="text-sm text-navy-500">{{ $child->admission_number }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-navy-500">Class</span>
                            <span class="font-medium text-navy-900 dark:text-navy-100">{{ $child->schoolClass?->name ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-navy-500">Level</span>
                            <span class="font-medium text-navy-900 dark:text-navy-100">{{ $child->level }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-navy-500">Gender</span>
                            <span class="font-medium text-navy-900 dark:text-navy-100 capitalize">{{ $child->gender }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-navy-500">Status</span>
                            <x-badge color="{{ $child->status === 'active' ? 'green' : 'gray' }}">{{ ucfirst($child->status) }}</x-badge>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
