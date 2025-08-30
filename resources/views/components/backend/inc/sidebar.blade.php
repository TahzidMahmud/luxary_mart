<aside class="sidebar h-full hover-scrollbar">
    <ul class="sidebar__list flex-grow">
        <li class="sidebar__logo hidden pl-[57px] py-10">
            <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}" alt="" class="max-w-[200px]" />
        </li>

        <li class="sidebar__li {{ areActiveRoutes(['admin.dashboard']) }}">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-house"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Dashboard') }}</span>
            </a>
        </li>

        @can('view_orders')
            {{-- Orders --}}
            <li class="sidebar__li {{ areActiveRoutes(['admin.orders.index', 'admin.orders.show']) }}">
                <a href="{{ route('admin.orders.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-square-poll-vertical"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Orders') }}</span>
                </a>
            </li>
        @endcan

        {{-- @can('view_orders') --}}
        {{-- Orders --}}
        <li class="sidebar__li {{ areActiveRoutes(['admin.pos']) }}">
            <a href="{{ route('admin.pos',['warehouseId'=>'1']) }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-solid fa-bell-concierge"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Sell Panel') }}</span>
            </a>
        </li>
        {{-- @endcan --}}

        @canany(['view_products', 'view_categories', 'view_variations', 'view_brands', 'view_units', 'view_taxes',
            'view_badges'])
            {{-- products --}}
            @php
                $productsActiveRoutes = [
                    'admin.products.*',
                    'admin.refunds',
                    'admin.units.*',
                    'admin.taxes.*',
                    'admin.badges.*',
                    'admin.brands.*',
                    'admin.categories.*',
                    'admin.variations.*',
                    'admin.variation-values.*',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($productsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-cart-plus"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Products') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    @can('view_products')
                        <li class="{{ areActiveRoutes(['admin.products.*']) }}">
                            <a href="{{ route('admin.products.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Products') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_categories')
                        <li class="{{ areActiveRoutes(['admin.categories.*']) }}">
                            <a href="{{ route('admin.categories.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Categories') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_variations')
                        <li class="{{ areActiveRoutes(['admin.variations.*', 'admin.variation-values.*']) }}">
                            <a href="{{ route('admin.variations.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Variations') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_brands')
                        <li class="{{ areActiveRoutes(['admin.brands.*']) }}">
                            <a href="{{ route('admin.brands.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Brands') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_units')
                        <li class="{{ areActiveRoutes(['admin.units.*']) }}">
                            <a href="{{ route('admin.units.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Units') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_taxes')
                        <li class="{{ areActiveRoutes(['admin.taxes.*']) }}">
                            <a href="{{ route('admin.taxes.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Taxes') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_badges')
                        <li class="{{ areActiveRoutes(['admin.badges.*']) }}">
                            <a href="{{ route('admin.badges.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Badges') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @canany(['view_purchase_orders', 'view_return_purchase_orders', 'view_stock_adjustment', 'view_stock_transfer',
            'view_suppliers'])
            @if (useInventory())
                {{-- Inventory --}}
                @php
                    $inventoryActiveRoutes = [
                        'admin.suppliers.*',
                        'admin.purchase-orders.*',
                        'admin.purchase-return.*',
                        'admin.stockAdjustments.*',
                        'admin.stockTransfers.*',
                    ];
                @endphp
                <li class="sidebar__li has-submenu {{ areActiveRoutes($inventoryActiveRoutes, 'active expanded') }}">
                    <a href="javascript:void(0);" class="sidebar__item">
                        <span class="sidebar__item--icon">
                            <i class="fa-regular  fa-calendar-check"></i>
                        </span>
                        <span class="sidebar__item--text">{{ translate('Inventory') }}</span>

                        <button class="sidebar__list--toggler">
                            <i class="fa-regular fa-chevron-down"></i>
                        </button>
                    </a>

                    <ul class="text-foreground divide-y divide-border bg-theme-primary/5">

                        @can('view_purchase_orders')
                            <li class="{{ areActiveRoutes(['admin.purchase-orders.*']) }}">
                                <a href="{{ route('admin.purchase-orders.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Purchase Orders') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_return_purchase_orders')
                            <li class="{{ areActiveRoutes(['admin.purchase-return.*']) }}">
                                <a href="{{ route('admin.purchase-return.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Purchases Return') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_stock_adjustment')
                            <li class="{{ areActiveRoutes(['admin.stockAdjustments.*']) }}">
                                <a href="{{ route('admin.stockAdjustments.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Stock Adjustment') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_stock_transfer')
                            <li class="{{ areActiveRoutes(['admin.stockTransfers.*']) }}">
                                <a href="{{ route('admin.stockTransfers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Stock Transfer') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_suppliers')
                            <li class="{{ areActiveRoutes(['admin.suppliers.*']) }}">
                                <a href="{{ route('admin.suppliers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Suppliers') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endcanany


        @can('view_customers')
            {{-- Customers --}}
            <li class="sidebar__li {{ areActiveRoutes(['admin.customers.index', 'admin.customers.show']) }}">
                <a href="{{ route('admin.customers.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-users"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Customers') }}</span>
                </a>
            </li>
        @endcan

        {{-- moderators --}}
        @php
            $moderatorActiveRoutes = ['admin.moderators.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($moderatorActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-user"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Moderators') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul class="text-foreground divide-y divide-border bg-theme-primary/5">

                <li class="{{ areActiveRoutes(['admin.moderators.index', 'admin.moderators.edit']) }}">
                    <a href="{{ route('admin.moderators.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Moderator Commissions') }}</span>
                    </a>
                </li>
                <li class="{{ areActiveRoutes(['admin.moderators.payoutList']) }}">
                    <a href="{{ route('admin.moderators.payoutList') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Payouts') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        @canany(['view_sellers', 'show_payouts', 'show_payout_requests', 'show_earning_histories'])
            {{-- sellers --}}
            @php
                $sellersActiveRoutes = ['admin.sellers.*'];
            @endphp
            @if (config('app.app_mode') == 'multiVendor')
                <li class="sidebar__li has-submenu {{ areActiveRoutes($sellersActiveRoutes, 'active expanded') }}">
                    <a href="javascript:void(0);" class="sidebar__item">
                        <span class="sidebar__item--icon">
                            <i class="fa-regular fa-shop"></i>
                        </span>
                        <span class="sidebar__item--text">{{ translate('Seller Management') }}</span>

                        <button class="sidebar__list--toggler">
                            <i class="fa-regular fa-chevron-down"></i>
                        </button>
                    </a>

                    <ul class="text-foreground divide-y divide-border bg-theme-primary/5">

                        @can('view_sellers')
                            <li class="{{ areActiveRoutes(['admin.sellers.index', 'admin.sellers.edit']) }}">
                                <a href="{{ route('admin.sellers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Sellers') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('show_payouts')
                            <li class="{{ areActiveRoutes(['admin.sellers.payouts']) }}">
                                <a href="{{ route('admin.sellers.payouts') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Payouts') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('show_payout_requests')
                            <li class="{{ areActiveRoutes(['admin.sellers.payoutRequests']) }}">
                                <a href="{{ route('admin.sellers.payoutRequests') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Payout Requests') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('show_earning_histories')
                            <li class="{{ areActiveRoutes(['admin.sellers.earnings']) }}">
                                <a href="{{ route('admin.sellers.earnings') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Earning Histories') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endcanany

        @can('view_tags')
            {{-- resources --}}
            @php
                $resourcesActiveRoutes = ['admin.tags.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($resourcesActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-copy"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Resources') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    <li class="{{ areActiveRoutes(['admin.tags.*']) }}">
                        <a href="{{ route('admin.tags.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Tags') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @canany(['view_coupons', 'view_campaigns', 'view_subscribers'])
            {{-- promotions --}}
            @php
                $promotionsActiveRoutes = [
                    'admin.coupons.*',
                    'admin.campaigns.*',
                    'admin.subscribers.index',
                    'admin.newsletters.index',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($promotionsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-wand-magic-sparkles"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Promotions') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    @can('view_coupons')
                        <li class="{{ areActiveRoutes(['admin.coupons.*']) }}">
                            <a href="{{ route('admin.coupons.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Coupons') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_campaigns')
                        <li class="{{ areActiveRoutes(['admin.campaigns.*']) }}">
                            <a href="{{ route('admin.campaigns.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Campaign') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_subscribers')
                        <li class="{{ areActiveRoutes(['admin.subscribers.index']) }}">
                            <a href="{{ route('admin.subscribers.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Subscribers') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('send_newsletters')
                        <li class="{{ areActiveRoutes(['admin.newsletters.index']) }}">
                            <a href="{{ route('admin.newsletters.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Newsletters') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['view_countries', 'view_states', 'view_cities', 'view_areas', 'view_zones', 'view_warehouses',
            'delivery_charges'])
            {{-- shipping --}}
            @php
                $shippingActiveRoutes = [
                    'admin.countries.*',
                    'admin.states.*',
                    'admin.cities.*',
                    'admin.areas.*',
                    'admin.zones.*',
                    'admin.warehouses.*',
                    'admin.delivery-charges.*',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shippingActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-shipping-fast"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Shipping & Delivery') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                {{-- country, state, city, area, zone, warehouses --}}
                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">

                    @can('view_countries')
                        <li class="{{ areActiveRoutes(['admin.countries.*']) }}">
                            <a href="{{ route('admin.countries') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Countries') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_states')
                        <li class="{{ areActiveRoutes(['admin.states.*']) }}">
                            <a href="{{ route('admin.states.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('States') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_cities')
                        <li class="{{ areActiveRoutes(['admin.cities.*']) }}">
                            <a href="{{ route('admin.cities.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Cities') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_areas')
                        <li class="{{ areActiveRoutes(['admin.areas.*']) }}">
                            <a href="{{ route('admin.areas.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Areas') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_zones')
                        <li class="{{ areActiveRoutes(['admin.zones.*']) }}">
                            <a href="{{ route('admin.zones.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Zones') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_warehouses')
                        <li class="{{ areActiveRoutes(['admin.warehouses.*']) }}">
                            <a href="{{ route('admin.warehouses.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Warehouses') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('delivery_charges')
                        <li class="{{ areActiveRoutes(['admin.delivery-charges.*']) }}">
                            <a href="{{ route('admin.delivery-charges.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Delivery Charges') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('file_manager')
            {{-- file manager --}}
            <li class="sidebar__li  {{ areActiveRoutes(['file-manager.index']) }}">
                <a href="{{ route('file-manager.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-image"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('File Management') }}</span>
                </a>
            </li>
        @endcan

        @can('conversation')
            @if (config('app.app_mode') == 'multiVendor')
                <li class="sidebar__li  {{ areActiveRoutes(['admin.chat']) }}">
                    <a href="{{ route('admin.chat') }}" class="sidebar__item">
                        <span class="sidebar__item--icon">
                            <i class="fa-regular fa-message"></i>
                        </span>
                        <span class="sidebar__item--text">{{ translate('Conversations') }}</span>
                    </a>
                </li>
            @endif
        @endcan

        @canany(['view_home_sections', 'configurations'])
            {{-- shop settings --}}
            @php
                $shopSettingsActiveRoutes = ['admin.shop-sections.*', 'admin.shops.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shopSettingsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-shop-lock"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Shop Settings') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    @can('view_home_sections')
                        <li class="{{ areActiveRoutes(['admin.shop-sections.*']) }}">
                            <a href="{{ route('admin.shop-sections.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Home Sections') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('configurations')
                        <li class="{{ areActiveRoutes(['admin.shops.profile']) }}">
                            <a href="{{ route('admin.shops.profile') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Configurations') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['view_staffs', 'view_roles_and_permissions'])
            {{-- staffs --}}
            @php
                $staffsActiveRoutes = ['admin.staffs.*', 'admin.roles.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($staffsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-user-lock"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Staff Management') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">

                    @can('view_staffs')
                        <li class="{{ areActiveRoutes(['admin.staffs.*']) }}">
                            <a href="{{ route('admin.staffs.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('All Staffs') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_roles_and_permissions')
                        <li class="{{ areActiveRoutes(['admin.roles.*']) }}">
                            <a href="{{ route('admin.roles.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Roles') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

        @endcanany

        @canany(['view_pages', 'homepage', 'color_and_branding'])
            {{-- website setup --}}
            @php
                $shopSettingsActiveRoutes = ['admin.pages.*', 'admin.homepage.configure', 'admin.colorBranding.index'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shopSettingsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-desktop"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Website Setup') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    @can('homepage')
                        <li class="{{ areActiveRoutes(['admin.homepage.configure']) }}">
                            @php
                                $homepageId = 1;
                            @endphp
                            <a href="{{ route('admin.homepage.configure', ['id' => $homepageId, 'lang_key' => config('app.default_language')]) }}&translate"
                                class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Homepage') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_pages')
                        <li class="{{ areActiveRoutes(['admin.pages.*']) }}">
                            <a href="{{ route('admin.pages.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Pages') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('color_and_branding')
                        <li class="{{ areActiveRoutes(['admin.colorBranding.index']) }}">
                            <a href="{{ route('admin.colorBranding.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Color & Branding') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        {{-- @canany(['view_pages', 'homepage', 'color_and_branding']) --}}
        {{-- website setup --}}
        @php
            $otpSettingsActiveRoutes = ['admin.otp.*'];
        @endphp
        @if (user()->user_type == 'admin')
            <li class="sidebar__li has-submenu {{ areActiveRoutes($otpSettingsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-phone"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('OTP System') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    {{-- @can('view_pages') --}}
                    <li class="{{ areActiveRoutes(['admin.otp.configure']) }}">
                        <a href="{{ route('admin.otp.configure') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Set OTP Credentials') }}</span>
                        </a>
                    </li>
                    {{-- @endcan  --}}
                </ul>
            </li>
        @endif
        {{-- @endcanany --}}

        @canany(['general_settings', 'order_settings', 'smtp_settings', 'payment_methods', 'social_media_login',
            'view_languages'])
            {{-- configurations --}}
            @php
                $configActiveRoutes = [
                    'admin.general-settings.index',
                    'admin.general-settings.update',
                    'admin.order-settings.index',
                    'admin.smtp-settings.index',
                    'test.smtp',
                    'admin.env-key.update',
                    'admin.payment-method.index',
                    'admin.payment-method.update',
                    'admin.social-login.index',
                    'admin.languages.*',
                    'admin.delivery-partner.index',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($configActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-gear"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('System Setup') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="text-foreground divide-y divide-border bg-theme-primary/5">
                    @can('general_settings')
                        <li class="{{ areActiveRoutes(['admin.general-settings.index']) }}">
                            <a href="{{ route('admin.general-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('General Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('order_settings')
                        <li class="{{ areActiveRoutes(['admin.order-settings.index']) }}">
                            <a href="{{ route('admin.order-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Order Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_languages')
                        <li class="{{ areActiveRoutes(['admin.languages.*']) }}">
                            <a href="{{ route('admin.languages.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Language Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('smtp_settings')
                        <li class="{{ areActiveRoutes(['admin.smtp-settings.index']) }}">
                            <a href="{{ route('admin.smtp-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('SMTP Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('payment_methods')
                        <li class="{{ areActiveRoutes(['admin.payment-method.index']) }}">
                            <a href="{{ route('admin.payment-method.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Payment Methods') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('social_media_login')
                        <li class="{{ areActiveRoutes(['admin.social-login.index']) }}">
                            <a href="{{ route('admin.social-login.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Social Media Login') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('payment_methods')
                        <li class="{{ areActiveRoutes(['admin.delivery-partner.index']) }}">
                            <a href="{{ route('admin.delivery-partner.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Delivery Partner Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

    </ul>

    <div
        class="sticky bottom-0 bg-background px-[15px] pb-3 pt-5 border-t border-border grid grid-cols-2 lg:hidden items-center gap-4">
        <div class="option-dropdown" tabindex="0">
            @php
                if (Session::has('locale')) {
                    $locale = Session::get('locale', Config::get('app.locale'));
                } else {
                    $locale = env('DEFAULT_LANGUAGE');
                }

                $currentLanguage = \App\Models\Language::where('code', $locale)->first();

                if (is_null($currentLanguage)) {
                    $currentLanguage = \App\Models\Language::where('code', 'en-US')->first();
                }
            @endphp

            <div class="option-dropdown__toggler option-dropdown__toggler--icon-small bg-background text-foreground">
                <span>
                    <img src="{{ asset('images/flags/' . $currentLanguage->flag . '.png') }}" alt=""
                        class="w-[20px]" />
                </span>
                <span>{{ $currentLanguage->name }}</span>
            </div>

            <div class="option-dropdown__options option-dropdown__options--top">
                <ul>
                    @foreach (\App\Models\Language::where('is_active', 1)->get() as $key => $language)
                        <li>
                            <a href="javascript:void(0);"
                                class="option-dropdown__option @if ($currentLanguage->code == $language->code) active @endif"
                                onclick="changeLocaleLanguage(this)" data-flag="{{ $language->code }}">
                                <span>
                                    <img src="{{ asset('images/flags/' . $language->flag . '.png') }}"
                                        alt="" class="w-[20px]" />
                                </span>
                                <span>{{ $language->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a href="{{ url('/') }}" target="_blank"
            class="whitespace-nowrap h-10 inline-flex items-center gap-2 bg-theme-primary text-white rounded-md px-4 font-bold border-2 border-transparent hover:bg-transparent hover:border-theme-primary hover:text-theme-primary">
            <span>
                <i class="fa-light fa-globe-pointer"></i>
            </span>
            <span>{{ translate('Browse') }}</span>
        </a>
    </div>

    <div class="copyright pl-[57px] pb-9">
        <p class="text-muted leading-none">
            {{ translate('Powered By') }}
        </p>
        <a href="">
            <img src="{{ uploadedAsset(getSetting('poweredBy')) }}" alt="" />
        </a>
    </div>
</aside>
