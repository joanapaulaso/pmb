@props(['disabled' => false])

<input 
    {{ $disabled ? 'disabled' : '' }} 
    {!! $attributes->merge(['class' => 'w-full px-4 py-2 border-1 border-gray-300 rounded text-gray-700 focus:ring-2 focus:ring-blue-500 focus:outline-transparent transition-colors']) !!}
>