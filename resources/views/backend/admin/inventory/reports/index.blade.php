@extends('layouts.admin')

@section('title')
    {{ translate('Stock Report') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-solid fa-file-invoice"></i>
            </span>
            <span class="text-sm sm:text-base font-bold">
                {{ translate('Stock Report') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Stock Report') }}</p>
        </div>
    </div>
    <div class="card theme-table">

        <table class="product-list-table footable w-full">
            <thead class="uppercase text-left bg-theme-primary/10">
                <tr>
                    <th>
                        {{ translate('Product Image') }}
                    </th>
                    <th>
                        {{ translate('Product Name') }}
                    </th>
                    <th data-breakpoints="xs sm">
                        {{ translate('Stock Amount') }}
                    </th>
                    <th>
                        {{ translate('Stock Value') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reportData as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}" width="50" class="object-cover">
                        </td>
                        <td>
                            <div class="font-medium text-heading mb-1">{{ $product['name'] }}</div>
                            @if(count($product['variations']) > 0)
                                <div class="text-xs text-muted">Variations:</div>
                                <ul class="list-disc list-inside text-xs text-muted">
                                    @foreach($product['variations'] as $variation)
                                        <li>{{ $variation['name'] }}: {{ $variation['stock_qty'] }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-xs text-muted">{{ translate('No variations') }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $product['total_stock_amount'] }}
                        </td>
                        <td>
                            {{ formatPrice($product['total_stock_value']) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (count($reportData) > 0)
            <div class="card__footer">
                {{-- Pagination links would go here if you decide to paginate --}}
            </div>
        @else
            <div class="text-center p-4">{{ translate('No data available') }}</div>
        @endif
    </div>
@endsection
