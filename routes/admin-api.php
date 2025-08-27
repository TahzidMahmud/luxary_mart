<?php

use App\Http\Controllers\Backend\Api\Campaign\CampaignProductController;
use App\Http\Controllers\Backend\Api\Chat\ChatController;
use App\Http\Controllers\Backend\Api\Dashboard\DashboardController;
use App\Http\Controllers\Backend\Api\Goal\GoalController;
use App\Http\Controllers\Backend\Api\POS\PosController;
use App\Http\Controllers\Frontend\Api\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\Api\Order\OrderController;

Route::group(['prefix' => 'admin/api', 'middleware' => ['auth', 'admin', 'unbanned', 'xss']], function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/new', [DashboardController::class, 'new']);
        Route::get('/total-orders', [DashboardController::class, 'totalOrders']);
        Route::get('/total-sales', [DashboardController::class, 'totalSales']);
        Route::get('/total-sellers', [DashboardController::class, 'totalSellers']);
        Route::get('/total-customers', [DashboardController::class, 'totalCustomers']);
        Route::get('/sales-amount', [DashboardController::class, 'SalesAmount']);
        Route::get('/top-categories', [DashboardController::class, 'topCategories']);
        Route::get('/top-brands', [DashboardController::class, 'topBrands']);
        Route::get('/recent-orders', [DashboardController::class, 'recentOrders'])->name('admin.dashboard.recentOrders');
        Route::get('/order-updates', [DashboardController::class, 'orderUpdates'])->name('admin.dashboard.orderUpdates');
        Route::get('/recent-products', [DashboardController::class, 'recentProducts'])->name('admin.dashboard.recentProducts');
        Route::get('/most-selling-products', [DashboardController::class, 'mostSellingProducts'])->name('admin.dashboard.mostSellingProducts');
        Route::get('/earning-from-sellers', [DashboardController::class, 'earningFromSellers'])->name('admin.dashboard.earningFromSellers');
        Route::get('/top-rated-sellers', [DashboardController::class, 'topRatedSellers'])->name('admin.dashboard.topRatedSellers');
        Route::get('/top-sellers', [DashboardController::class, 'topSellers'])->name('admin.dashboard.topSellers');
    });
    Route::get('/orders',[OrderController::class, 'index'])->name('admin.allorders');
 // quick order status update
    Route::post('/orders/update-order-status',[OrderController::class, 'updateOrderStatus'])->name('admin.updateOrderStatus');

    /*
    |--------------------------------------------------------------------------
    | goal Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'goals'], function () {
        Route::get('/', [GoalController::class, 'index']);
        Route::post('/', [GoalController::class, 'store']);
    });


    /*
    |--------------------------------------------------------------------------
    | Campaign product Routes for CRUD.
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/filter-data', [CampaignProductController::class, 'getFilterData']);
        Route::get('/products', [CampaignProductController::class, 'index']);
        Route::post('/products', [CampaignProductController::class, 'store']);
        Route::post('/products/update', [CampaignProductController::class, 'update']);
        Route::post('/products/delete', [CampaignProductController::class, 'delete']);
        Route::get('/products/{campaignId}', [CampaignProductController::class, 'getCampaignProducts']);
    });

    /*
    |--------------------------------------------------------------------------
    | chats Routes.
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'chats'], function () {
        // chat
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/delete', [ChatController::class, 'destroy']);

        Route::get('/messages/{chatId}', [ChatController::class, 'indexMessages']);
        Route::post('/messages', [ChatController::class, 'storeMessages']);
    });


    /*
    |--------------------------------------------------------------------------
    | pos Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'pos'], function () {
        Route::post('/add-to-cart', [PosController::class, 'addToPosCart']);
        Route::post('/cart/update', [PosController::class, 'updatePosCart']);
        Route::get('/cart/hold', [PosController::class, 'indexHoldPosCartGroup']);
        Route::post('/cart/hold', [PosController::class, 'holdPosCartGroup']);
        Route::post('/cart/hold/delete/{id}', [PosController::class, 'deleteHoldPosCartGroup']);
        Route::post('/cart/submit-order', [PosController::class, 'storeOrder']);

        Route::get('/filter-data', [PosController::class, 'filterData']);
        Route::get('/product-variations', [PosController::class, 'index']);
        Route::get('/customers', [PosController::class, 'customers']);
        Route::post('/customer', [PosController::class, 'storeCustomer']);
        Route::get('/customers/address/{id}', [PosController::class, 'customerAddresses']);
        Route::post('/address', [PosController::class, 'storeAddress']);

        Route::get('/get-states/{countryId}', [UserController::class, 'states']);
        Route::get('/get-cities/{stateId}', [UserController::class, 'cities']);
        Route::get('/get-areas/{cityId}', [UserController::class, 'areas']);
    });
});
