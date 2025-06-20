@props(['disabled' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-white bg-gray-200 text-gray-900 p-5 text-lg text-semibold  focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
