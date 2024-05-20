@php
    use App\Enums\Api\Ban\BanStatus;
        /** @var \App\Models\User\User $user */
        /** @var \App\Enums\Api\Ban\BanStatus $banStatus*/
@endphp

@switch($banStatus)
    @case(\App\Enums\Api\Ban\BanStatus::Good)
        <span {{ $attributes->merge(['class' => 'text-green-400']) }}>GOOD</span>
        @break

    @case(\App\Enums\Api\Ban\BanStatus::Warning)
        <span {{ $attributes->merge(['class' => 'text-yellow-500']) }}>WARNING</span>
        @break

    @case(\App\Enums\Api\Ban\BanStatus::Banned)
        <span {{ $attributes->merge(['class' => 'text-red-600']) }}>BANNED</span>
        @break

@endswitch

