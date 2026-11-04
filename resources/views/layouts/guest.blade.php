<x-app-layout :title="$title ?? null">
    <div class="py-5">
        <div class="container container-narrow">
            <div class="card p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-app-layout>
