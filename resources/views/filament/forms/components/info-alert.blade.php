<div class="flex items-center gap-x-3 rounded-lg bg-primary-50 p-4">
    <x-dynamic-component :component="$icon" class="h-5 w-5 text-primary-400" />
    <p>
        {{ $content }}
    </p>
</div>