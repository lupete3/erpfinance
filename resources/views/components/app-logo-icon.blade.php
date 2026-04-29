@php
  $width = $width ?? '50';

  $logoQuira = \App\Models\CompanySetting::first();
@endphp
@if($logoQuira?->logo && file_exists(public_path($logoQuira->logo)))

  <a href="{{ url('/') }}" class="app-brand-link">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path($logoQuira->logo))) }}"
      class="logo" alt="{{ __('QUIRA POS') }}" width="{{ $width }}">
  </a>
@else
    <a href="{{ url('/') }}" class="app-brand-link"><x-app-logo /></a>
@endif
