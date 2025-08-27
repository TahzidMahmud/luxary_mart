@extends('layouts.admin')

@section('title')
    {{ translate('Moderator Commission List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Moderator Commission List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Moderator Commission List') }}</p>
        </div>
    </div>
    <!-- end::dashboard breadcrumb -->

    <div class="grid md:grid-cols-5 gap-3">
        <div class="md:col-span-5">
            <div class="card theme-table">
                <div
                    class="card__title card__title border-none theme-table__filter flex flex-col md:flex-row xl:items-center justify-between gap-3">

                    <div></div>

                    <x-backend.forms.search-form searchKey="{{ $searchKey }}" class="max-w-[480px]"
                        placeholder="Type name/email/phone & hit enter">

                        <x-backend.inputs.select name="status" class="filterSelect" :isRequired="false">
                            <x-backend.inputs.select-option name="{{ translate('Paid & Due') }}" value=""
                                selected="{{ Request::get('status') }}" />
                            <x-backend.inputs.select-option name="{{ translate('Paid') }}" value="paid"
                                selected="{{ Request::get('status') }}" />
                            <x-backend.inputs.select-option name="{{ translate('Due') }}" value="due"
                                selected="{{ Request::get('status') }}" />
                        </x-backend.inputs.select>

                    </x-backend.forms.search-form>
                </div>

                <table class="product-list-table footable w-full">
                    <thead class="uppercase text-left bg-theme-primary/10">
                        <tr>
                            <th data-breakpoints="xs sm">
                                #
                            </th>
                            <th>
                                {{ translate('Name') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Product') }}
                            </th>
                            <th data-breakpoints="xs sm">
                                {{ translate('Invoice') }}
                            </th>
                            <th>
                                {{ translate('Invoice Date') }}
                            </th>
                            <th>
                                {{ translate('Bill Amount') }}
                            </th>
                            <th>
                                {{ translate('Commission %') }}
                            </th>
                            <th>
                                {{ translate('Commission Amount') }}
                            </th>
                            <th>
                                {{ translate('Status') }}
                            </th>
                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commissions as $key => $commission)
                            @php
                                $order = \App\Models\Order::where('order_code', $commission->invoice_no)->first();
                            @endphp
                            <tr>
                                <td>{{ $key + 1 + ($commissions->currentPage() - 1) * $commissions->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($commission->user?->avatar) }}" alt=""
                                                class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div>
                                            <div class="line-clamp-2">
                                                {{ $commission->user?->name }}
                                            </div>
                                            <div class="line-clamp-2">
                                                {{ $commission->user?->email }}
                                            </div>
                                            <div class="line-clamp-2">
                                                {{ $commission->user?->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($commission->product?->thumbnail_image) }}"
                                                alt="" class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div>
                                            <div class="line-clamp-2">
                                                {{ $commission->product?->collectTranslation('name') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <a href="{{ route('admin.orders.show', $commission->invoice_no) }}">
                                        {{ getSetting('orderCodePrefix') }}{{ $commission->invoice_no }}
                                        <span class="text-lg text-theme-orange">
                                            <i class="fa-solid fa-eye"></i>
                                        </span>
                                    </a>
                                </td>

                                <td>
                                    {{ $order?->order_receiving_date ? date('d M, Y', strtotime($order?->order_receiving_date)) : '' }}
                                </td>

                                <td>
                                    <span
                                        class="font-bold text-sky-500">{{ formatPrice($commission->total_amount) }}</span>
                                </td>
                                <td>
                                    {{ $commission->commission_rate }}
                                </td>
                                <td>
                                    <span
                                        class="font-bold text-green-500">{{ formatPrice($commission->commission_amount) }}</span>
                                </td>

                                <td>
                                    <div class="inline-flex items-center capitalize gap-1.5">
                                        <span
                                            class="{{ $commission->status == 'paid' ? 'text-teal-600' : 'text-neutral-300 dark:text-neutral-800' }} text-2xl">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </span>
                                        <span>{{ ucfirst($commission->status) }}
                                            {{ $commission->due_amount > 0 ? '(' . formatPrice($commission->due_amount) . ')' : '' }}</span>
                                    </div>
                                </td>

                                <td class="text-end">
                                    @if ($user->user_type == 'admin' && $commission->status != 'paid')
                                        <a href="{{ route('admin.moderators.payouts', 'id=' . $commission->id) }}">
                                            <span class="text-xl text-theme-primary">
                                                <i class="fa-solid fa-plus"></i>
                                            </span>
                                        </a>

                                        <a href="javascript:void(0);"
                                            data-href="{{ route('admin.moderators.destroy', $commission->id) }}"
                                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                                            data-text="{{ translate('All data related to this may get deleted.') }}"
                                            data-method="DELETE" data-micromodal-trigger="confirm-modal"
                                            class="confirm-modal text-red-500 text-lg ms-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @else
                                        {{ translate('n/a') }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $commissions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
