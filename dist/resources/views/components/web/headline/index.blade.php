<h1 {{ $attributes->merge([
    'class' => "text-3xl font-semibold my-4
                after:block after:w-full after:content-[''] after:border after:[border-image:linear-gradient(to_right,var(--color-web-main)_10%,transparent)_1]",
]) }} >
    {{ $slot }}
</h1>