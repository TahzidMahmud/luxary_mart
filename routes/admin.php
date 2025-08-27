<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\ChatController;
use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Backend\Admin\Staffs\RoleController;
use App\Http\Controllers\Backend\Admin\Configurations\SettingController;
use App\Http\Controllers\Backend\Admin\DashboardController;
use App\Http\Controllers\Backend\Admin\FileManagerController;
use App\Http\Controllers\Backend\Admin\Inventory\PurchaseOrderController;
use App\Http\Controllers\Backend\Admin\Inventory\PurchasePaymentController;
use App\Http\Controllers\Backend\Admin\Inventory\StockAdjustmentController;
use App\Http\Controllers\Backend\Admin\Inventory\StockTransferController;
use App\Http\Controllers\Backend\Admin\Products\BadgeController;
use App\Http\Controllers\Backend\Admin\Products\BrandController;
use App\Http\Controllers\Backend\Admin\Products\ProductController;
use App\Http\Controllers\Backend\Admin\Products\UnitController;
use App\Http\Controllers\Backend\Admin\Products\CategoryController;
use App\Http\Controllers\Backend\Admin\Products\TaxController;
use App\Http\Controllers\Backend\Admin\Products\VariationController;
use App\Http\Controllers\Backend\Admin\Products\VariationValueController;
use App\Http\Controllers\Backend\Admin\Promotions\CouponController;
use App\Http\Controllers\Backend\Admin\Promotions\CampaignController;
use App\Http\Controllers\Backend\Admin\Inventory\SupplierController;
use App\Http\Controllers\Backend\Admin\Moderator\ModeratorController;
use App\Http\Controllers\Backend\Admin\Orders\OrderController;
use App\Http\Controllers\Backend\Admin\Otp\OtpController;
use App\Http\Controllers\Backend\Admin\PosController;
use App\Http\Controllers\Backend\Admin\Promotions\NewsletterController;
use App\Http\Controllers\Backend\Admin\Promotions\SubscriberController;
use App\Http\Controllers\Backend\Admin\Resources\TagController;
use App\Http\Controllers\Backend\Admin\Shipping\AreaController;
use App\Http\Controllers\Backend\Admin\Shipping\CityController;
use App\Http\Controllers\Backend\Admin\Shipping\CountryController;
use App\Http\Controllers\Backend\Admin\Shipping\DeliveryChargeController;
use App\Http\Controllers\Backend\Admin\Shipping\StateController;
use App\Http\Controllers\Backend\Admin\Shipping\WarehouseController;
use App\Http\Controllers\Backend\Admin\Shipping\ZoneController;
use App\Http\Controllers\Backend\Admin\ShopSettings\ShopSectionController;
use App\Http\Controllers\Backend\Admin\Staffs\StaffController;
use App\Http\Controllers\Backend\Admin\Users\CustomerController;
use App\Http\Controllers\Backend\Admin\Users\SellerController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\ColorBrandingController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\PageController;

/*
|----------------------------------------------------------------------------------------------------------------------
| file manager - uploader, file selector routes
|----------------------------------------------------------------------------------------------------------------------
*/

Route::post('/file-manager/upload', [FileManagerController::class, 'upload']);
Route::get('/file-manager/get-uploaded-files', [FileManagerController::class, 'getUploadedFiles'])->name('get_uploaded_files');
Route::delete('/file-manager/destroy/{id}', [FileManagerController::class, 'destroy'])->name('destroy_file');
Route::post('/file-manager/get-file-by-ids', [FileManagerController::class, 'getPreviewFiles'])->name('get_file_by_ids');
Route::get('/file-manager/download/{id}', [FileManagerController::class, 'attachmentDownload'])->name('download_attachment');
Route::post('/file-manager/bg-remove/{id}', [FileManagerController::class, 'backgroundRemove'])->name('bg_remove');
Route::post('/file-manager/alt-text/{id}', [FileManagerController::class, 'altText'])->name('alt_text');

# admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'unbanned', 'xss']], function () {

    # cache clear
    Route::get('/clear-cache', function () {
        cacheClear();
        flash(translate('Cache cleared successfully'))->success();
        return back();
    })->name('admin.clearCache');

    # notification
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('admin.notifications');
    Route::get('/notifications/mark-as-read', [DashboardController::class, 'markRead'])->name('admin.notifications.markRead');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | dashboard routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/chats', [ChatController::class, 'index'])->name('admin.chat');
    Route::post('/navbar-search', [DashboardController::class, 'getNavbarSearchData'])->name('admin.getNavbarSearchData');
    Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('admin.changeLanguage');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | POS routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/pos', [PosController::class, 'index'])->name('admin.pos');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | role permissions
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::resource('/staffs', StaffController::class, ['names' => 'admin.staffs']);
    Route::post('/staffs/toggle-ban', [StaffController::class, 'toggleBan'])->name('admin.staffs.toggleBan');

    Route::resource('/roles', RoleController::class, ['names' => 'admin.roles']);
    Route::post('/roles/update-status', [RoleController::class, 'updateStatus'])->name('admin.roles.status');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | order routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::post('/update-order', [OrderController::class, 'updateOrder'])->name('admin.orders.update');
    Route::post('/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.updatePaymentStatus');
    Route::post('/update-delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('admin.orders.updateDeliveryStatus');
    Route::post('/update-order-address', [OrderController::class, 'updateOrderAddress'])->name('admin.orders.updateOrderAddress');
    Route::post('/orders/track', [OrderController::class, 'updateOrderTracking'])->name('admin.orders.updateOrderTracking');
    Route::post('/orders/note', [OrderController::class, 'storeOrderUpdates'])->name('admin.orders.storeOrderUpdates');
    Route::post('/orders/note/delete/{id}', [OrderController::class, 'deleteOrderUpdate'])->name('admin.orders.deleteOrderUpdate');
    Route::get('/orders/{code}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::delete('/orders/remove-product/{id}', [OrderController::class, 'removeOrderItem'])->name('admin.orders.removeOrderItem');
    Route::post('/orders/update-qty', [OrderController::class, 'updateQty'])->name('admin.orders.updateQty');
    Route::get('/invoice-download/{id}', [OrderController::class, 'downloadInvoice'])->name('admin.orders.downloadInvoice');

    Route::post('/update-orders/get-products', [OrderController::class, 'getProducts'])->name('admin.update-orders.getProducts');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | customers routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # customers 
    Route::resource('/customers', CustomerController::class, ['names' => 'admin.customers']);
    Route::post('/customers/toggle-ban', [CustomerController::class, 'toggleBan'])->name('admin.customers.toggleBan');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | moderator routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # moderators 
    Route::resource('/moderators', ModeratorController::class, ['names' => 'admin.moderators']);
    Route::get('/moderator-payouts', [ModeratorController::class, 'payouts'])->name('admin.moderators.payouts');
    Route::post('/moderator-payouts', [ModeratorController::class, 'storePayouts'])->name('admin.moderators.storePayouts');
    Route::get('/moderator-payout-list', [ModeratorController::class, 'payoutList'])->name('admin.moderators.payoutList');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | sellers routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # seller payments
    Route::get('/sellers/payouts', [SellerController::class, 'payouts'])->name('admin.sellers.payouts');
    Route::get('/sellers/payout-requests', [SellerController::class, 'payoutRequests'])->name('admin.sellers.payoutRequests');
    Route::post('/sellers/payout-requests', [SellerController::class, 'makePayment'])->name('admin.sellers.makePayment');
    Route::get('/sellers/earnings', [SellerController::class, 'earnings'])->name('admin.sellers.earnings');

    # sellers 
    Route::resource('/sellers', SellerController::class, ['names' => 'admin.sellers']);
    Route::post('/sellers/toggle-ban', [SellerController::class, 'toggleBan'])->name('admin.sellers.toggleBan');
    Route::post('/sellers/toggle-approval', [SellerController::class, 'toggleApproval'])->name('admin.sellers.toggleApproval');
    Route::post('/sellers/toggle-published', [SellerController::class, 'togglePublished'])->name('admin.sellers.togglePublished');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | product routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # products 
    Route::resource('/products', ProductController::class, ['names' => 'admin.products']);
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::get('/products/duplicate/{id}', [ProductController::class, 'duplicate'])->name('admin.products.duplicate');
    Route::get('/products/real-pictures/{id}', [ProductController::class, 'pictures'])->name('admin.products.pictures');
    Route::post('/update-status-products', [ProductController::class, 'updateStatus'])->name('admin.products.status');

    Route::post('/get-variation-values', [ProductController::class, 'getVariationValues'])->name('admin.products.getVariationValues');
    Route::post('/new-variation', [ProductController::class, 'getNewVariation'])->name('admin.products.newVariation');
    Route::post('/variation-combination', [ProductController::class, 'generateVariationCombinations'])->name('admin.products.generateVariationCombinations');

    # categories
    Route::resource('/categories', CategoryController::class, ['names' => 'admin.categories']);
    Route::get('/categories/create/modal', [CategoryController::class, 'initCreateModal'])->name('admin.categories.initCreateModal');


    # variations
    Route::resource('/variations', VariationController::class, ['names' => 'admin.variations']);
    Route::post('/variations/update-status', [VariationController::class, 'updateStatus'])->name('admin.variations.status');

    # variation values
    Route::get('/variation-values/{variation_id}', [VariationValueController::class, 'index'])->name('admin.variation-values.index');

    Route::get('/variation-values/{variation_id}/create', [VariationValueController::class, 'create'])->name('admin.variation-values.create');
    Route::post('/variation-values/{variation_id}', [VariationValueController::class, 'store'])->name('admin.variation-values.store');

    Route::get('/variation-values/{id}/edit', [VariationValueController::class, 'edit'])->name('admin.variation-values.edit');
    Route::put('/variation-values/{id}/update', [VariationValueController::class, 'update'])->name('admin.variation-values.update');

    Route::post('/variation-values/status/update', [VariationValueController::class, 'updateStatus'])->name('admin.variation-values.status');
    Route::delete('/variation-values/{id}/delete', [VariationValueController::class, 'destroy'])->name('admin.variation-values.destroy');

    # brands
    Route::resource('/brands', BrandController::class, ['names' => 'admin.brands']);

    # units
    Route::resource('/units', UnitController::class, ['names' => 'admin.units']);

    # taxes
    Route::resource('/taxes', TaxController::class, ['names' => 'admin.taxes']);
    Route::post('/update-status-tax', [TaxController::class, 'updateStatus'])->name('admin.taxes.status');

    # badges
    Route::resource('/badges', BadgeController::class, ['names' => 'admin.badges']);
    Route::post('/update-status-badge', [BadgeController::class, 'updateStatus'])->name('admin.badges.status');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Inventory routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # purchases routes
    Route::resource('/purchase-orders', PurchaseOrderController::class, ['names' => 'admin.purchase-orders']);
    Route::post('/purchase-orders/get-products', [PurchaseOrderController::class, 'getProducts'])->name('admin.purchase-orders.getProducts');
    Route::get('/purchase-return/list', [PurchaseOrderController::class, 'returnIndex'])->name('admin.purchase-return.index');
    Route::get('/purchase-return/{id}', [PurchaseOrderController::class, 'returnCreate'])->name('admin.purchase-return.create');
    Route::post('/purchase-return/{id}', [PurchaseOrderController::class, 'returnStore'])->name('admin.purchase-return.store');

    # payments
    Route::post('/purchases/payments', [PurchasePaymentController::class, 'index'])->name('admin.purchase-payments.index');
    Route::post('/purchases/payment', [PurchasePaymentController::class, 'store'])->name('admin.purchase-payments.store');

    # stock adjustments
    Route::resource('/stocks/adjustment', StockAdjustmentController::class, ['names' => 'admin.stockAdjustments']);
    Route::post('/stocks/adjustment/get-products', [StockAdjustmentController::class, 'getProducts'])->name('admin.stockAdjustments.getProducts');

    # stock transfer
    Route::resource('/stocks/transfer', StockTransferController::class, ['names' => 'admin.stockTransfers']);
    Route::post('/stocks/transfer/get-products', [StockTransferController::class, 'getProducts'])->name('admin.stockTransfers.getProducts');



    # suppliers routes
    Route::resource('/suppliers', SupplierController::class, ['names' => 'admin.suppliers']);
    /*
    |----------------------------------------------------------------------------------------------------------------------
    | resources routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # tags routes
    Route::resource('/tags', TagController::class, ['names' => 'admin.tags']);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | promotions routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # coupons routes
    Route::resource('/coupons', CouponController::class, ['names' => 'admin.coupons']);

    # campaign routes
    Route::resource('/campaigns', CampaignController::class, ['names' => 'admin.campaigns']);
    Route::post('/campaigns/update-campaign-status', [CampaignController::class, 'updateStatus'])->name('admin.campaigns.status');

    # subscribers
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('admin.subscribers.index');
    Route::delete('/subscribers/{id}', [SubscriberController::class, 'destroy'])->name('admin.subscribers.destroy');

    # newsletters
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters.index');
    Route::post('/newsletters', [NewsletterController::class, 'store'])->name('admin.newsletters.store');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | sale routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/sales', [DashboardController::class, 'index'])->name('admin.sales');
    Route::get('/refunds', [DashboardController::class, 'index'])->name('admin.refunds');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Shipping routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # shipping countries
    Route::get('/shipping/countries', [CountryController::class, 'index'])->name('admin.countries');
    Route::post('/shipping/update-country-status', [CountryController::class, 'updateStatus'])->name('admin.countries.status');

    # shipping states
    Route::resource('/shipping/states', StateController::class, ['names' => 'admin.states']);
    Route::post('/shipping/update-status-state', [StateController::class, 'updateStatus'])->name('admin.states.status');

    # shipping cities
    Route::resource('/shipping/cities', CityController::class, ['names' => 'admin.cities']);
    Route::post('/shipping/update-status-cities', [CityController::class, 'updateStatus'])->name('admin.cities.status');

    # shipping areas
    Route::resource('/shipping/areas', AreaController::class, ['names' => 'admin.areas']);
    Route::post('/shipping/update-status-areas', [AreaController::class, 'updateStatus'])->name('admin.areas.status');

    # shipping zones
    Route::resource('/shipping/zones', ZoneController::class, ['names' => 'admin.zones']);
    Route::post('/shipping/update-status-zones', [ZoneController::class, 'updateStatus'])->name('admin.zones.status');

    # shipping warehouses
    Route::resource('/shipping/warehouses', WarehouseController::class, ['names' => 'admin.warehouses']);
    Route::post('/shipping/update-status-warehouse', [WarehouseController::class, 'updateStatus'])->name('admin.warehouses.status');
    Route::post('/shipping/update-default-warehouse', [WarehouseController::class, 'updateDefault'])->name('admin.warehouses.default');

    # shipping delivery charges
    Route::get('/shipping/delivery-charges', [DeliveryChargeController::class, 'index'])->name('admin.delivery-charges.index');
    Route::post('/shipping/delivery-charges', [DeliveryChargeController::class, 'store'])->name('admin.delivery-charges.store');
    Route::get('/shipping/delivery-charges/{zone_id}', [DeliveryChargeController::class, 'show'])->name('admin.delivery-charges.show');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | file manager - admin
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::resource('/file-manager', FileManagerController::class);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | shop settings routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # section routes
    Route::resource('/shop/sections', ShopSectionController::class, ['names' => 'admin.shop-sections']);

    # update profile
    Route::get('/shop/profile', [ShopSectionController::class, 'profile'])->name('admin.shops.profile');
    Route::post('/shop/profile', [ShopSectionController::class, 'updateProfile'])->name('admin.shops.updateProfile');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | website setup routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # pages routes 
    Route::get('/color-and-branding', [ColorBrandingController::class, 'colorBranding'])->name('admin.colorBranding.index');
    Route::resource('/pages', PageController::class, ['names' => 'admin.pages']);
    Route::get('/pages/configure/{id}', [PageController::class, 'configure'])->name('admin.homepage.configure');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | otp setup routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/otp-configuration', [OtpController::class, 'configure'])->name('admin.otp.configure');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Configurations routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'configurations'], function () {

        # env key update
        Route::post('/env-key-update', [SettingController::class, 'envKeyUpdate'])->name('admin.env-key.update');

        # general settings
        Route::get('/general-settings', [SettingController::class, 'generalSetting'])->name('admin.general-settings.index');
        Route::post('/general-settings', [SettingController::class, 'update'])->name('admin.general-settings.update');

        # order settings
        Route::get('/order-settings', [SettingController::class, 'orderSetting'])->name('admin.order-settings.index');

        # language settings
        Route::resource('/languages', LanguageController::class, ['names' => 'admin.languages']);
        Route::get('/languages/{code}', [LanguageController::class, 'show'])->name('admin.languages.show');
        Route::post('/update-update_rtl_status', [LanguageController::class, 'updateRtlStatus'])->name('admin.languages.updateRtlStatus');
        Route::post('/languages/keyValueStore', [LanguageController::class, 'keyValueStore'])->name('admin.languages.keyValueStore');
        Route::get('/delete-language/{id}', [LanguageController::class, 'destroy'])->name('admin.languages.destroy');

        # smtp settings
        Route::get('/smtp-settings', [SettingController::class, 'smtpSettings'])->name('admin.smtp-settings.index');
        Route::post('/smtp/test', [SettingController::class, 'testEmail'])->name('test.smtp');

        # social login settings
        Route::get('/social-login', [SettingController::class, 'social_login'])->name('admin.social-login.index');

        # payment method settings
        Route::get('/payment-method', [SettingController::class, 'payment_method'])->name('admin.payment-method.index');
        Route::post('/payment_method_update', [SettingController::class, 'payment_method_update'])->name('admin.payment-method.update');
    });
});
