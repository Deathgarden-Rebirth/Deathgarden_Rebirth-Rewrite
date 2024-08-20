<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Deathgarden Rebirth' }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="dark bg-gray-100 dark:bg-gray-950 font-sans text-gray-950 dark:text-white antialiased">
<div class="absolute top-0 w-full my-20 z-50">
    @if(Session::has('alert-error'))
        <x-alerts.error heading="An error occured">{!! Session::get('alert-error') !!}</x-alerts.error>
    @endif
    @if(Session::has('alert-success'))
        <x-alerts.success heading="Success">{!! Session::get('alert-success') !!}</x-alerts.success>
    @endif
    @if(Session::has('alert-warning'))
        <x-alerts.warning heading="Warning">{!! Session::get('alert-warning') !!}</x-alerts.warning>
    @endif
</div>
@php
    use App\Enums\Auth\Permissions;
    use Illuminate\Support\Facades\Auth;

    $maintenanceMode = \App\Helper\AppHelper\AppEnv::isInMaintenanceMode();
    $showMaintenancePage = request()->route()->getPrefix() !== 'admin' &&
        !(Auth::user() !== null && Auth::user()->can(Permissions::VIEW_MAINTENANCE->value));
@endphp
@if($maintenanceMode && $showMaintenancePage)
    <x-misc.maintenance/>
@else
    @if($maintenanceMode)
        <div class="fixed bottom-0 right-0">
            <span class="text-gray-500/80">Maintenance mode active</span>
        </div>
    @endif
    {{ $slot }}
@endif
</body>
<footer>
    @stack('footer')
</footer>
</html>