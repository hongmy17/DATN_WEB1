<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline btn-full']) }}>
    {{ $slot }}
</button>
