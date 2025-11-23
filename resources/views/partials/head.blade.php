<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? \App\Helpers\WebsiteHelper::getWebsiteName() }}</title>

@php
    $favicon = \App\Helpers\WebsiteHelper::getFavicon();
@endphp
@if ($favicon)
    <link rel="icon" href="{{ $favicon }}">
@else
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
@endif
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@filamentStyles
@if (app()->environment('local') && file_exists(public_path('hot')))
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
@else
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
@fluxAppearance

