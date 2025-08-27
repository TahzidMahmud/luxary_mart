<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UpdateProfileController;
use App\Http\Controllers\AI\OpenAIController;
use App\Http\Controllers\Backend\Payments\CoinGate\CoinGateController;
use App\Http\Controllers\Backend\Payments\Flutterwave\FlutterwaveController;
use App\Http\Controllers\Backend\Payments\Instamojo\InstamojoController;
use App\Http\Controllers\Backend\Payments\IyZico\IyZicoController;
use App\Http\Controllers\Backend\Payments\PaymentController;
use App\Http\Controllers\Backend\Payments\Paypal\PaypalController;
use App\Http\Controllers\Backend\Payments\Paystack\PaystackPaymentController;
use App\Http\Controllers\Backend\Payments\Paytm\PaytmPaymentController;
use App\Http\Controllers\Backend\Payments\Razorpay\RazorpayController;
use App\Http\Controllers\Backend\Payments\SSLCommerz\SSLCommerzPaymentController;
use App\Http\Controllers\Backend\Payments\Bkash\BkashPaymentController;
use App\Http\Controllers\Backend\Payments\Stripe\StripePaymentController;
use App\Http\Controllers\Frontend\HomeController;
use App\Services\GPTEngine;
use Illuminate\Support\Facades\Route;

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

/*
|----------------------------------------------------------------------------------------------------------------------
| auth routes
|----------------------------------------------------------------------------------------------------------------------
*/

Route::get('resources/{lng}/{ns}.json', function ($lng, $ns) {
    if ($ns == "en") {
        $ns = "en-US";
    }
    $filePath = resource_path("{$lng}/{$ns}.json");
    if (file_exists($filePath)) {
        return response()->file(resource_path("{$lng}/{$ns}.json"));
    } else {
        return response()->json(['error' => 'Language file not found'], 404);
    }
});

Auth::routes(['register' => false]);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/update-profile', [UpdateProfileController::class, 'edit'])->name('profile.edit')->middleware('auth');
Route::post('/update-profile', [UpdateProfileController::class, 'update'])->name('profile.update')->middleware('auth');

Route::post('/generate_openai_content', [OpenAIController::class, 'generateOpenAIContent'])->name('openai');

Route::get('/', [HomeController::class, 'index'])->where('any', '.*')->name('home');

# payment routes
Route::group(['prefix' => 'payments'], function () {
    # payments
    Route::post('/', [PaymentController::class, 'initPayment']);

    # paypal
    Route::any('/paypal/success', [PaypalController::class, 'success'])->name('paypal.success');
    Route::any('/paypal/cancel', [PaypalController::class, 'cancel'])->name('paypal.cancel');

    # stripe 
    Route::any('/stripe/create-session', [StripePaymentController::class, 'checkoutSession'])->name('stripe.get_token');
    Route::get('/stripe/success', [StripePaymentController::class, 'success'])->name('stripe.success');
    Route::get('/stripe/cancel', [StripePaymentController::class, 'cancel'])->name('stripe.cancel');

    # flutterwave
    Route::any('/flutterwave/payment/callback', [FlutterwaveController::class, 'callback'])->name('flutterwave.callback');

    # paytm
    Route::any('/paytm/callback', [PaytmPaymentController::class, 'callback'])->name('paytm.callback');

    # paystack
    Route::any('/paystack/callback', [PaystackPaymentController::class, 'return'])->name('paystack.return');

    # sslcommerz
    Route::any('/sslcommerz/success', [SSLCommerzPaymentController::class, 'success'])->name('sslcommerz.success');
    Route::any('/sslcommerz/fail', [SSLCommerzPaymentController::class, 'fail'])->name('sslcommerz.fail');
    Route::any('/sslcommerz/cancel', [SSLCommerzPaymentController::class, 'cancel'])->name('sslcommerz.cancel');

    # bkash
    Route::post('/bkash-create', [BkashPaymentController::class, 'createPayment'])->name('url-create');
    Route::get('/bkash-callback', [BkashPaymentController::class, 'callback'])->name('url-callback');


    # coingate
    Route::post('/coingate/callback', [CoinGateController::class, 'callback'])->name('coingate.callback');
    Route::get('/coingate/success', [CoinGateController::class, 'success'])->name('coingate.success');
    Route::get('/coingate/cancel', [CoinGateController::class, 'cancel'])->name('coingate.cancel');

    # iyzico
    Route::any('/iyzico/payment/callback', [IyZicoController::class, 'callback'])->name('iyzico.callback');

    # instamojo 
    Route::any('/instamojo/callback', [InstamojoController::class, 'callback'])->name('instamojo.success');

    # razorpay
    Route::post('razorpay/payment', [RazorpayController::class, 'payment'])->name('razorpay.payment');
});


# social login
Route::any('/social-login/redirect/{provider}', [LoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('/social-login/{provider}/callback', [LoginController::class, 'handleProviderCallback'])->name('social.callback');

#
Route::get('/translate', [HomeController::class, 'insertTranslationKeys']);

# routes to set SEO meta tags 
Route::get('/products/{slug}', [HomeController::class, 'index'])->name('product');
Route::get('/categories/{slug}', [HomeController::class, 'index'])->name('category');
Route::get('/brands/{slug}', [HomeController::class, 'index'])->name('brand');
Route::get('/shops/{slug}', [HomeController::class, 'index'])->name('shop');
Route::get('/pages/{slug}', [HomeController::class, 'index'])->name('page');

Route::get('/{any?}', [HomeController::class, 'index'])->where('any', '.*')->name('home');
