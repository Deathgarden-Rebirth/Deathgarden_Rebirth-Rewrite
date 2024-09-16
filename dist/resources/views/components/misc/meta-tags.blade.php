@php
    $metaObjectType ??= 'website';
    $metaUrl ??= url()->current();
    $metaImage ??= asset('img/logos/DG_Rebirth_Logo.png');
    $metaTitle ??= $title ?? 'Deathgarden: Rebirth';
@endphp

@isset($metaDescription)
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="og:description" content="{{ $metaDescription }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
@endisset

<meta name="og:type" content="{{ $metaObjectType }}">
<meta name="og:url" content="{{ $metaUrl }}">
<meta name="og:title" content="{{ $metaTitle }}">
<meta name="og:image:secure_url" content="{{ $metaImage }}">
<meta name="twitter:image" content="{{ $metaImage }}">

@if(isset($metaKeywords) && is_array($metaKeywords))
    <meta name="keywords" content="{{ implode(',', $metaKeywords) }}"
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@DGRebirth_">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:url" content="{{ $metaUrl }}">