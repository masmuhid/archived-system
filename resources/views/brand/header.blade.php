@push('head')
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
@endpush

<div class="d-flex {{ auth()->check() ? '' : 'flex-column align-items-center text-center' }}">
    @auth
        <img src="{{ asset('favicon/logo.png') }}" alt="Sidebar Image" width="80%" class="img-fluid">
        <p class="my-0">
            <small class="align-top opacity" style="font-size: 10px">{{ config('app.env') }}</small>
        </p>
    @else
        <img src="{{ asset('favicon/logo.png') }}" alt="Login Image" class="mx-auto mb-2">
        <p class="my-0 mb-0">
            <small class="align-top opacity">Content Management System</small>
        </p>
    @endauth
</div>