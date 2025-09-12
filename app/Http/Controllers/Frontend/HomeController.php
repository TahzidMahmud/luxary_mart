<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use App\Models\Shop;
use App\Models\SystemSetting;
use App\Models\Translation;
use Cache;
use Illuminate\Http\Request;
use Route;

class HomeController extends Controller
{
    # return to frontend homepage with the necessary data
    public function index(Request $request, $slug = null)
    {
        $primaryColor   = implode(' ', sscanf(getSetting('primaryColor') ?? '#005bff', "#%02x%02x%02x"));
        $secondaryColor = implode(' ', sscanf(getSecondaryColor(getSetting('primaryColor') ?? '#005bff', 0.8), "#%02x%02x%02x"));
        $secondaryColorLight = implode(' ', sscanf(getSecondaryColor(getSetting('primaryColor') ?? '#005bff', 0.6), "#%02x%02x%02x"));

        $paymentMethods = [];
        if (getSetting('cod_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Cash On Delivery");
            $payment->value = "cash_on_delivery";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('card_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Card On Delivery");
            $payment->value = "card_on_delivery";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paypal_activation') == 1) {
            $payment            = new SystemSetting;
            $payment->name      = translate("Paypal");
            $payment->value     = "paypal";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('stripe_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Stripe");
            $payment->value = "stripe";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('flutterwave_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Flutterwave");
            $payment->value = "flutterwave";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paytm_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("PayTm");
            $payment->value = "paytm";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('paystack_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Paystack");
            $payment->value = "paystack";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('sslcommerz_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("SSLCommerz");
            $payment->value = "sslcommerz";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('bkash_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Bkash");
            $payment->value = "bkash";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('coingate_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("CoinGate");
            $payment->value = "coingate";
            array_push($paymentMethods, $payment);
        }

        if (getSetting('iyzico_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("IyZico");
            $payment->value = "iyzico";
            array_push($paymentMethods, $payment);
        }
        if (getSetting('razorpay_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Razorpay");
            $payment->value = "razorpay";
            array_push($paymentMethods, $payment);
        }
        if (getSetting('instamojo_activation') == 1) {
            $payment        = new SystemSetting;
            $payment->name  = translate("Instamojo");
            $payment->value = "instamojo";
            array_push($paymentMethods, $payment);
        }

        // social logins
        $socialLogins = [];
        if (config('app.GOOGLE_ACTIVATION') == 1) {
            $social         = new SystemSetting;
            $social->name  = "Google";
            $social->url   = route('social.login', 'google');
            array_push($socialLogins, $social);
        }

        if (config('app.FACEBOOK_ACTIVATION') == 1) {
            $social        = new SystemSetting;
            $social->name  = "Facebook";
            $social->url   = route('social.login', 'facebook');
            array_push($socialLogins, $social);
        }

        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            # currency settings
            'currency'      => [
                'code'      => getSetting('currencyCode') ?? 'usd',
                'symbol'    => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1  : 0] : 'symbol_first',

                    'show'  => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals'     => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator'  => getSetting('decimalSeparator') ?? '.',
            ],

            # general settings
            'generalSettings' => [
                'appName' => config('app.name'),
                'appMode' => config('app.app_mode'),
                'logo'    => uploadedAsset(getSetting('websiteHeaderLogo')),
                'rootUrl' => config('app.url'),
                'apiUrl'  => config('app.url') . config('app.api_pathname'),
                // 'apiUrl' => 'https://ba11-103-176-19-44.ngrok-free.app/armtech/multivendor-ecommerce/api/v1',
                'demoMode' => config('app.demo_mode'),
                'env'=>config('app.env')
            ],
            'countries'         => Country::where('is_active', 1)->get(),
            'languages'         => Language::isActive()->get(['name', 'code', 'flag', 'is_rtl']),
            'defaultLang'       => Language::where('code', config('app.default_language'))->get(['name', 'code', 'flag', 'is_rtl'])->first(),
            'paymentMethods'    => $paymentMethods,
            'socialLogins'      => $socialLogins,
        ];

        $meta = [
            'meta_title' => getSetting('meta_title'),
            'meta_description' => getSetting('meta_description'),
            'meta_image' => uploadedAsset(getSetting('meta_image')),
            'meta_keywords' => getSetting('meta_keywords'),
            'meta_url' => env('APP_URL')
        ];
        $meta['meta_title'] = $meta['meta_title'] ? $meta['meta_title'] : config('app.name');

        if (Route::currentRouteName() == 'product') {
            $product = Product::where('slug', $slug)->first();
            if ($product) {
                $meta['meta_title']         = $product->meta_title ? $product->meta_title : $product->name;
                $meta['meta_description']   = $product->meta_description ? $product->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $product->meta_keywords ? $product->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $product->meta_image ? uploadedAsset($product->meta_image) : uploadedAsset($product->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'category') {
            $category = Category::where('slug', $slug)->first();
            if ($category) {
                $meta['meta_title']         = $category->meta_title ? $category->meta_title : $category->name;
                $meta['meta_description']   = $category->meta_description ? $category->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $category->meta_keywords ? $category->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $category->meta_image ? uploadedAsset($category->meta_image) : uploadedAsset($category->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'brand') {
            $brand = Brand::where('slug', $slug)->first();
            if ($brand) {
                $meta['meta_title']         = $brand->meta_title ? $brand->meta_title : $brand->name;
                $meta['meta_description']   = $brand->meta_description ? $brand->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $brand->meta_keywords ? $brand->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $brand->meta_image ? uploadedAsset($brand->meta_image) : uploadedAsset($brand->thumbnail_img);
                $meta['meta_url']           = route('product', $slug);
            }
        } elseif (Route::currentRouteName() == 'shop') {
            $shop = Shop::where('slug', $slug)->first();
            if ($shop) {
                $meta['meta_title']         = $shop->meta_title ? $shop->meta_title : $shop->name;
                $meta['meta_description']   = $shop->meta_description ? $shop->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $shop->meta_keywords ? $shop->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $shop->meta_image ? uploadedAsset($shop->meta_image) : uploadedAsset($shop->logo);
                $meta['meta_url']           = route('shop', $slug);
            }
        } elseif (Route::currentRouteName() == 'page') {
            $page = Page::where('slug', $slug)->first();
            if ($page) {
                $meta['meta_title']         = $page->meta_title ? $page->meta_title : $page->title;
                $meta['meta_description']   = $page->meta_description ? $page->meta_description : $meta['meta_description'];
                $meta['meta_keywords']      = $page->meta_keywords ? $page->meta_keywords : $meta['meta_keywords'];
                $meta['meta_image']         = $page->meta_image ? uploadedAsset($page->meta_image) : $meta['meta_image'];
                $meta['meta_url']           = route('page', $slug);
            }
        }


        return view('frontend.home', compact('settings', 'meta', 'primaryColor', 'secondaryColor', 'secondaryColorLight'));
    }

    public function insertTranslationKeys()
    {
        $fn = fopen(base_path('translations.txt'), "r");

        while (!feof($fn)) {
            $result = fgets($fn);

            $t_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($result)));

            Translation::updateOrCreate(
                ['lang_key' => config('app.default_language'), 't_key' => $t_key],
                ['t_value' => trim($result)]
            );
        }

        fclose($fn);

        Cache::forget('translations-' . config('app.default_language'));

        (new LanguageController())->saveTranslationsAsJsonFile(config('app.default_language'));

        return Translation::where('lang_key', config('app.default_language'))->latest()->get(['t_key', 't_value']);
    }
}
