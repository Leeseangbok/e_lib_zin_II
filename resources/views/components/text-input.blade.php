@props(['disabled' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white bg-gray-700 p-5 text-lg text-semibold text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
