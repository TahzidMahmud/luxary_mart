@extends('layouts.admin')

@section('title')
    {{ translate('Moderator Payout List') }} | {{ getSetting('systemName') }}
@endsection

@section('content')
    <!-- start::dashboard breadcrumb -->
    <div class="dashboard-nav pt-6 flex items-center justify-between mb-9">
        <div class="flex items-center">
            <span class="text-xl mr-3 text-theme-secondary">
                <i class="fa-regular fa-folder"></i>
            </span>
            <span class="text-sm smtext-base font-bold">
                {{ translate('Moderator Payout List') }}
            </span>
        </div>

        <div class="max-sm:hidden flex items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="font-bold ">{{ translate('Dashboard') }}</a>
            <span class="text-theme-primary dark:text-muted">
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <p class="text-muted">{{ translate('Moderator Payout List') }}</p>
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
                            <x-backend.inputs.select-option name="{{ translate('Paid & Partial') }}" value=""
                                selected="{{ Request::get('status') }}" />
                            <x-backend.inputs.select-option name="{{ translate('Paid') }}" value="full"
                                selected="{{ Request::get('status') }}" />
                            <x-backend.inputs.select-option name="{{ translate('Partial') }}" value="partial"
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

                            <th>
                                {{ translate('Paid Amount') }}
                            </th>

                            <th>
                                {{ translate('Paid By') }}
                            </th>


                            <th>
                                {{ translate('Pay Status') }}
                            </th>

                            <th data-breakpoints="xs sm" class="text-end">
                                {{ translate('Options') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payouts as $key => $payout)
                            <tr>
                                <td>{{ $key + 1 + ($payouts->currentPage() - 1) * $payouts->perPage() }}</td>

                                <td>
                                    <div class="inline-flex items-center gap-4">
                                        <div class="max-xs:hidden">
                                            <img src="{{ uploadedAsset($payout->user?->avatar) }}" alt=""
                                                class="w-[70px] h-[80px] rounded-md"
                                                onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                                        </div>
                                        <div>
                                            <div class="line-clamp-2">
                                                {{ $payout->user?->name }}
                                            </div>
                                            <div class="line-clamp-2">
                                                {{ $payout->user?->email }}
                                            </div>
                                            <div class="line-clamp-2">
                                                {{ $payout->user?->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ formatPrice($payout->paid_amount) }}
                                </td>

                                <td>
                                    {{ $payout->paidBy?->name }}
                                </td>


                                <td>
                                    <div class="inline-flex items-center capitalize gap-1.5">
                                        <span
                                            class="{{ $payout->status == 'full' ? 'text-teal-600' : 'text-neutral-300 dark:text-neutral-800' }} text-2xl">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </span>
                                        <span>{{ ucfirst($payout->status) }} </span>
                                    </div>
                                </td>

                                <td class="text-end">

                                    {{ date('d M, Y', strtotime($payout->created_at)) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card__footer">
                    {{ $payouts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
