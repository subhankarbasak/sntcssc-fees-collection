@props(['messages'])

@if (count($messages) > 0)
    <div class="mt-2">
        @foreach ($messages as $message)
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @endforeach
    </div>
@endif