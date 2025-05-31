@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
    {{ $slot }} <!-- Để có thể truyền các option từ bên ngoài khi gọi component -->
</select>
