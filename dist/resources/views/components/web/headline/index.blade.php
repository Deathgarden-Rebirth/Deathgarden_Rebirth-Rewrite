<h1 {{ $attributes->merge([
    'class' => "text-3xl font-semibold my-4
                after:block after:w-full after:content-[''] after:border after:border-t-solid after:[border-image:linear-gradient(to_right,theme('colors.web-main')_10%,transparent)_1]",
]) }} >
    {{ $slot }}
</h1>