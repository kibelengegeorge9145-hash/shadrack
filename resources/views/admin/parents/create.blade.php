@extends('layouts.app')

@section('title', 'Add Parent')

@section('content')
    <x-page-header title="Add Parent" subtitle="Register a new parent or guardian and create their login account">
        <x-slot:actions>
            <x-button href="{{ route('admin.parents.index') }}" variant="secondary" icon="chevron-left">Back to Parents</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->any())
        <x-alert type="error" title="Please fix the following errors" class="mb-6">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif

    <form method="POST" action="{{ route('admin.parents.store') }}">
        @csrf

        <x-card title="Parent Information" icon="users">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="first_name" label="First Name" required placeholder="Sarah" />
                <x-input name="middle_name" label="Middle Name" placeholder="Grace" />
                <x-input name="last_name" label="Last Name" required placeholder="Doe" />
                <div>
                    <x-select name="relationship" label="Relationship" required :options="['father' => 'Father', 'mother' => 'Mother', 'guardian' => 'Guardian']" placeholder="Select relationship" />
                </div>
                <x-input name="phone" label="Phone" leadingIcon="phone" placeholder="+255 7XX XXX XXX" />
                <x-input name="email" label="Email" type="email" leadingIcon="mail" placeholder="parent@example.com" />
                <div class="md:col-span-2 lg:col-span-3">
                    <x-textarea name="address" label="Address" rows="3" placeholder="Home address">{{ old('address') }}</x-textarea>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.parents.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="check">Create Parent</x-button>
        </div>
    </form>
@endsection