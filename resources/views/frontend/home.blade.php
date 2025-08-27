<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ uploadedAsset(getSetting('favicon')) }}">

    <title>{{ getSetting('systemName') }}</title>

    <meta name="description" content="@yield('meta_description', $meta['meta_description'])" />
    <meta name="keywords" content="@yield('meta_keywords', $meta['meta_keywords'])">

    @yield('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta['meta_title'] }}">
    <meta itemprop="description" content="{{ $meta['meta_description'] }}">
    <meta itemprop="image" content="{{ $meta['meta_image'] }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $meta['meta_title'] }}">
    <meta name="twitter:description" content="{{ $meta['meta_description'] }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ $meta['meta_image'] }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta['meta_title'] }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $meta['meta_url'] }}" />
    <meta property="og:image" content="{{ $meta['meta_image'] }}" />
    <meta property="og:description" content="{{ $meta['meta_description'] }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Public+Sans:wght@300;400;500;600;700&family=Sacramento&display=swap"
        rel="stylesheet">

    {{-- <style type="text/css">
        :root {
            --theme-primary: {{ $primaryColor }};
            --theme-secondary: {{ $secondaryColor }};
            --theme-secondary-light: {{ $secondaryColorLight }}
        }
    </style> --}}

    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/frontend/index.css', 'resources/frontend/index.tsx'])
</head>

<body class="antialiased">

    @php
        // for guests to add to cart
        $tempValue = strtotime('now') . rand(10, 1000);
        $theTime = time() + 86400 * 365;

        if (!isset($_COOKIE['guestUserId'])) {
            setcookie('guestUserId', $tempValue, $theTime, '/'); // 86400 = 1 day
        }
    @endphp

    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="app"></div>

    <script>
        function getStrToLangKey(inputString) {
            // Convert to lowercase, replace spaces with underscores
            let modifiedString = inputString.toLowerCase().replace(/ /g, '_');

            // Remove non-alphanumeric characters and underscores
            let sanitizedString = modifiedString.replace(/[^a-z0-9_]/g, '');

            return sanitizedString;
        }
    </script>
</body>

</html>
