@props(['name', 'id', 'type' => 'text', 'value', 'required' => false])

<input 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => 'block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white']) }}
    @if($name) name="{{ $name }}" @endif
    @if($id) id="{{ $id }}" @endif
    @if($required) required @endif
    value="{{ old($name, $value) }}"
>