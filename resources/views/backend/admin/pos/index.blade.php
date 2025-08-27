@extends('layouts.admin')

@section('title')
    {{ translate('POS') }} | {{ getSetting('systemName') }}
@endsection

{{-- @can('show_dashboard') --}}
@section('content')
    <div id="app" class="bg-background-primary-light"></div>
@endsection

@section('scripts')
    <script>
        window.config = @json($settings);
    </script>

    @viteReactRefresh
    @vite(['resources/backend/pos/index.tsx'])
@endsection
{{-- @endcan --}}
