{{-- resources/views/errors/404.blade.php --}}
<x-layouts.auth>
    <x-slot:title>
        {{ __('messages.page_not_found_title') }}
    </x-slot:title>

    <div class="" style="height: calc(100vh - 85px)">
        <div class="misc-wrapper text-center">
            <h1 class="mb-2 mx-2" style="font-size: 6rem; font-weight: bold;">404</h1>
            <h4 class="mb-2">{{ __('messages.page_not_found_heading') }}</h4>
            <p class="mb-4 mx-2">
                {{ __('messages.page_not_found_text') }}<br>
                {{ __('messages.page_not_found_tip') }}
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary">{{ __('messages.back_to_home') }}</a>
        </div>
    </div>
</x-layouts.auth>
