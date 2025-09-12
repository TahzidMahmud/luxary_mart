<?php

use App\Http\Controllers\Frontend\Api\AuthController;
use App\Http\Controllers\Frontend\Api\BrandController;
use App\Http\Controllers\Frontend\Api\CampaignController;
use App\Http\Controllers\Frontend\Api\CartController;
use App\Http\Controllers\Frontend\Api\CheckoutController;
use App\Http\Controllers\Frontend\Api\ChatController;
use App\Http\Controllers\Frontend\Api\CouponController;
use App\Http\Controllers\Frontend\Api\HomepageController;
use App\Http\Controllers\Frontend\Api\OrderController;
use App\Http\Controllers\Frontend\Api\PasswordResetController;
use App\Http\Controllers\Frontend\Api\ProductController;
use App\Http\Controllers\Frontend\Api\ReviewController;
use App\Http\Controllers\Frontend\Api\SearchController;
use App\Http\Controllers\Frontend\Api\SellerController;
use App\Http\Controllers\Frontend\Api\ShopController;
use App\Http\Controllers\Frontend\Api\SubscribeController;
use App\Http\Controllers\Frontend\Api\UserController;
use App\Http\Controllers\Frontend\Api\WishlistController;
use App\Http\Controllers\Installation\InstallController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\DeliveryPartner\CourierOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => config('app.api_version')], function () {

    # installation APIs
    Route::group(['prefix' => 'installation'], function () {
        Route::get('/checklist', [InstallController::class, 'checklist'])->name('installation.checklist');

        Route::post('/database-setup', [InstallController::class, 'storeDatabaseSetup'])->name('installation.storeDbSetup');
        Route::post('/run-db-migration', [InstallController::class, 'runDbMigration'])->name('installation.runMigration');

        Route::post('/admin-configuration', [InstallController::class, 'storeAdmin'])->name('installation.storeAdmin');
    });

    # Auth routes
    Route::group(['prefix' => 'auth'], function () {

        Route::group(['middleware' => ['zone']], function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('signup', [AuthController::class, 'signup']);
            Route::post('verify', [AuthController::class, 'verify']);
        });

        Route::post('resend-code', [AuthController::class, 'resend_code']);
        Route::post('verify/code', [AuthController::class, 'verifyCode']);
        Route::post('password/reset', [PasswordResetController::class, 'reset']);

        Route::group(['middleware' => ['auth:sanctum', 'zone']], function () {
            Route::post('user', [AuthController::class, 'user']);
        });

        Route::group(['middleware' => ['auth:sanctum', 'zone']], function () {
            Route::get('logout', [AuthController::class, 'logout']);
        });
    });
  // delivery partner
    Route::post('/courier/order', [CourierOrderController::class, 'store']);
    // Endpoints specifically for courier area data
    Route::get('/courier/pathao/cities', [CourierOrderController::class, 'getCities']);
    Route::get('/courier/pathao/zones/{cityId}', [CourierOrderController::class, 'getZones']);
    Route::get('/courier/pathao/areas/{zoneId}', [CourierOrderController::class, 'getAreas']);
    Route::get('/courier/redx/areas', [CourierOrderController::class, 'getRedxAreas']);
    Route::get('/courier/status', [CourierOrderController::class, 'getDeliveryStatus']);

    # homepage
    Route::group(['prefix' => 'homepage'], function () {
        Route::get('/categories', [HomepageController::class, 'categories']);
        Route::get('/sliders', [HomepageController::class, 'sliders']);
        Route::get('/videos', [HomepageController::class, 'videos']);
        Route::get('/featured-categories', [HomepageController::class, 'featuredCategories']);
        Route::get('/featured-products', [HomepageController::class, 'featuredProducts'])->middleware('zone');
        Route::get('/flash-sale-products', [HomepageController::class, 'flashSaleProducts'])->middleware('zone');
        Route::get('/product-section-one', [HomepageController::class, 'productSectionOne'])->middleware('zone');
        Route::get('/full-width-banner', [HomepageController::class, 'fullWidthBanner']);
        Route::get('/product-section-two', [HomepageController::class, 'productSectionTwo'])->middleware('zone');
        Route::get('/four-banners-row', [HomepageController::class, 'fourBannersInARow']);
        Route::get('/featured-shops', [HomepageController::class, 'featuredShops']);
        Route::get('/two-banners-row', [HomepageController::class, 'twoBannersInARow']);
        Route::get('/new-arrivals', [HomepageController::class, 'newArrivals'])->middleware('zone');
        Route::get('/featured-brands', [HomepageController::class, 'featuredBrands']);
        Route::get('/trendy-products', [HomepageController::class, 'trendyProducts'])->middleware('zone');
        Route::get('/three-banners-row', [HomepageController::class, 'threeBannersInARow']);
        Route::get('/category-products', [HomepageController::class, 'categoryProducts'])->middleware('zone');
        Route::get('/about-us', [HomepageController::class, 'aboutUs']);
        Route::get('/main-categories', [HomepageController::class, 'mainCategories']);
        Route::get('/footer', [HomepageController::class, 'footer']);
    });

    # pages
    Route::get('/pages/{slug}', [HomepageController::class, 'pageDetails'])->name('api.pages.show');

    # seller registration
    Route::post('/seller/signup', [SellerController::class, 'store']);

    # search
    Route::group(['prefix' => 'search', 'middleware' => ['zone']], function () {
        Route::get('/', [SearchController::class, 'index']);
    });

    # products
    Route::group(['prefix' => 'products', 'middleware' => ['zone']], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/details/{slug}', [ProductController::class, 'show'])->name('api.products.show');
    });

    # brands
    Route::group(['prefix' => 'brands', 'middleware' => ['zone']], function () {
        Route::get('/', [BrandController::class, 'index']);
    });

    # shops
    Route::group(['prefix' => 'shops'], function () {
        Route::get('/', [ShopController::class, 'index']);
        Route::get('/details/{slug}', [ShopController::class, 'show'])->name('api.shops.show');

        # shop home sections
        Route::get('/sections/{slug}', [ShopController::class, 'shopSections'])->middleware('zone');

        # shop profile info
        Route::get('/profile/{slug}', [ShopController::class, 'profile'])->name('api.shops.profile')->middleware('zone');
    });

    # subscribe
    Route::post('subscribe', [SubscribeController::class, 'subscribe']);

    # coupons
    Route::group(['prefix' => 'coupons'], function () {
        Route::get('/', [CouponController::class, 'index']);
        Route::post('/apply', [CouponController::class, 'apply'])->middleware(['auth:sanctum', 'zone']);
        Route::get('/details/{code}', [CouponController::class, 'show'])->name('coupons.show')->middleware('zone');
    });

    # campaigns
    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/', [CampaignController::class, 'index']);
        Route::get('/details/{slug}', [CampaignController::class, 'show'])->name('campaigns.show')->middleware('zone');
    });

    # carts
    Route::group(['prefix' => 'carts', 'middleware' => ['zone']], function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/create', [CartController::class, 'store']);
        Route::post('/update', [CartController::class, 'update']);
        Route::post('/delete', [CartController::class, 'destroy']);
    });

    # wishlists
    Route::group(['prefix' => 'wishlists', 'middleware' => ['auth:sanctum', 'zone']], function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/create', [WishlistController::class, 'store']);
        Route::post('/delete', [WishlistController::class, 'destroy']);
    });

    # locations
    Route::get('/get-states/{countryId}', [UserController::class, 'states']);
    Route::get('/get-cities/{stateId}', [UserController::class, 'cities']);
    Route::get('/get-areas/{cityId}', [UserController::class, 'areas']);

    # reviews
    Route::group(['prefix' => 'reviews', 'middleware' => ['zone']], function () {
        // get all reviews of a product
        Route::get('/{productId}', [ReviewController::class, 'index']);

        // customer review
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('/user/all', [ReviewController::class, 'allUserReviews']);
            Route::post('/user/store', [ReviewController::class, 'storeOrUpdate']);
            Route::get('/user/{productId}', [ReviewController::class, 'show']);

            # shop review
            Route::get('/shop/{shopId}', [ReviewController::class, 'showShopReview']);
            Route::post('/shop/store', [ReviewController::class, 'storeShopReview']);
        });
    });


    # customer protected routes
    Route::group(['middleware' => 'auth:sanctum'], function () {

        // address
        Route::get('/user/addresses', [UserController::class, 'addresses']);
        Route::post('/user/address', [UserController::class, 'addNewAddress']);
        Route::post('/user/address/update', [UserController::class, 'updateAddress']);
        Route::post('/user/address/update-default', [UserController::class, 'setDefaultAddress']);
        Route::post('/user/address/delete', [UserController::class, 'destroy']);



        // orders
        Route::group(['prefix' => 'orders', 'middleware' => ['zone']], function () {
            Route::get('/', [OrderController::class, 'index']);

        });

        // chat
        Route::get('/chats', [ChatController::class, 'index']);
        Route::post('/chats', [ChatController::class, 'store']);
        Route::post('/chats/delete', [ChatController::class, 'destroy']);

        Route::get('/chats/messages/{chatId}', [ChatController::class, 'indexMessages']);
        Route::post('/chats/messages', [ChatController::class, 'storeMessages']);

        // profile
        Route::post('/user/update-info', [AuthController::class, 'updateInfo']);
        Route::post('/user/update-password', [AuthController::class, 'updatePassword']);
    });
     // checkout
    Route::group(['prefix' => 'checkout'], function () {
        // Route::post('get-shipping-charge', [CheckoutController::class, 'getShippingCharge']);
        #guest checkout
        Route::post('manual-order/store', [CheckoutController::class, 'store']);
        // Route::post('order/store', [CheckoutController::class, 'store'])->middleware('zone');
        Route::post('zone/update', [CheckoutController::class, 'updateZone'])->middleware('zone');
        Route::post('get-shipping-charge', [CheckoutController::class, 'getShippingCharge']);

    });
    Route::get('/orders/success/{code}', [OrderController::class, 'success'])->name('orders.success');



    Route::group(['prefix' => 'orders', 'middleware' => ['zone']], function () {
        Route::get('/{code}', [OrderController::class, 'show'])->name('orders.show');
    });

});
