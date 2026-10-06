@extends('layouts.app')

@section('title', 'Admission Details')

@section('content')
    <x-page-header title="Admission {{ $admission->application_number }}" subtitle="{{ $admission->full_name }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.admissions.index') }}" variant="secondary" icon="arrow-up">Back to List</x-button>
        </x-slot:actions>
    </x-page-header>

    <x-alert flash />

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Student Information" icon="user">
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-navy-500 mb-1">Full Name</p>
                        <p class="font-medium text-navy-900 dark:text-navy-100">{{ $admission->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Application Number</p>
                        <p class="font-mono font-medium text-navy-900 dark:text-navy-100">{{ $admission->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Date of Birth</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->date_of_birth?->format('d M Y') ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Gender</p>
                        <p class="text-navy-900 dark:text-navy-100 capitalize">{{ $admission->gender }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Level</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->level }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Applying For</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->applying_class }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Current School</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->current_school ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Date Applied</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                @if ($admission->notes)
                    <div class="mt-4 pt-4 border-t border-navy-100 dark:border-navy-700">
                        <p class="text-navy-500 mb-1 text-sm">Notes</p>
                        <p class="text-sm text-navy-700 dark:text-navy-300">{{ $admission->notes }}</p>
                    </div>
                @endif
            </x-card>

            <x-card title="Parent/Guardian Information" icon="user-plus">
                <div class="grid sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-navy-500 mb-1">Name</p>
                        <p class="font-medium text-navy-900 dark:text-navy-100">{{ $admission->parent_name }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Phone</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->parent_phone }}</p>
                    </div>
                    <div>
                        <p class="text-navy-500 mb-1">Email</p>
                        <p class="text-navy-900 dark:text-navy-100">{{ $admission->parent_email ?? '—' }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card title="Status" icon="target">
                @php
                    $statusColors = ['pending' => 'amber', 'under_review' => 'blue', 'accepted' => 'green', 'rejected' => 'red'];
                @endphp
                <div class="mb-4">
                    <p class="text-sm text-navy-500 mb-2">Current Status</p>
                    <x-badge :color="$statusColors[$admission->status] ?? 'gray'">{{ ucfirst(str_replace('_', ' ', $admission->status)) }}</x-badge>
                </div>

                <div class="border-t border-navy-100 dark:border-navy-700 pt-4">
                    <p class="text-sm font-medium text-navy-700 dark:text-navy-200 mb-3">Change Status</p>
                    <form method="POST" action="{{ route('admin.admissions.update-status', $admission) }}" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <x-select name="status" label="" :options="['pending' => 'Pending', 'under_review' => 'Under Review', 'accepted' => 'Accepted', 'rejected' => 'Rejected']" :selected="$admission->status" placeholder="Select status" required />
                        <x-button type="submit" variant="primary" icon="check" class="w-full">Update Status</x-button>
                    </form>
                </div>
            </x-card>

            <x-card title="Danger Zone" icon="trash">
                <p class="text-sm text-navy-500 mb-3">Permanently delete this application.</p>
                <form method="POST" action="{{ route('admin.admissions.destroy', $admission) }}" onsubmit="return confirm('Are you sure you want to delete this admission application?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" icon="trash" class="w-full">Delete Application</x-button>
                </form>
            </x-card>
        </div>
    </div>
@endsection