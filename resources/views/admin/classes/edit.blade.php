@extends('layouts.app')

@section('title', 'Edit Class')

@section('content')
    <x-page-header title="Edit Class" subtitle="{{ $class->name }}">
        <x-slot:actions>
            <x-button href="{{ route('admin.classes.index') }}" variant="secondary" icon="chevron-left">Back to Classes</x-button>
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

    <form method="POST" action="{{ route('admin.classes.update', $class) }}">
        @csrf
        @method('PUT')

        <x-card title="Class Details" icon="building">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-input name="name" label="Class Name" required :value="$class->name" />
                <div>
                    <x-select name="level" label="Level" required :options="$levelOptions" :selected="$class->level" />
                </div>
                <x-input name="form_number" label="Form Number" type="number" :value="$class->form_number" />
                <x-input name="label" label="Label" :value="$class->label" />
                <x-input name="academic_year" label="Academic Year" :value="$class->academic_year" />
                <div>
                    <x-select name="class_teacher_id" label="Class Teacher" :options="$teachers" :selected="$class->class_teacher_id" placeholder="Optional" />
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-navy-300 text-gold-400 focus:ring-gold-400/40" {{ $class->is_active ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-navy-800 dark:text-navy-100">Active class</span>
                    </label>
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <x-textarea name="description" label="Description" rows="3">{{ $class->description }}</x-textarea>
                </div>
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3 mt-6">
            <x-button href="{{ route('admin.classes.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="gold" icon="save">Save Changes</x-button>
        </div>
    </form>
@endsection