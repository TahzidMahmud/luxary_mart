<?php

use App\Models\CommissionHistory;
use App\Models\MediaFile;
use App\Models\ProductVariation;
use App\Models\SystemSetting;
use App\Models\Translation;
use App\Models\Variation;
use App\Models\VariationValue;

# apiUserId
if (!function_exists('apiUserId')) {
    function apiUserId()
    {
        return auth('sanctum')->user()->id;
    }
}
if(!function_exists('extractTrackingNumber')){
    function extractTrackingNumber(string $partner, array $response): ?string
    {
        return match ($partner) {
            'pathao'    => $response['data']['consignment_id'] ?? null,
            'redx'      => $response['tracking_id'] ?? null,
            'steadfast' => $response['consignment']['tracking_code'] ?? null,
            default     => null,
        };
    }
}
if(!function_exists('extractDeliveryStatus')){
    function extractDeliveryStatus(string $partner, array $response): ?string
    {
        return match ($partner) {
            'pathao'    => $response['data']['order_status'] ?? null,
            'redx'      => 'pickup-pending',
            'steadfast' => $response['consignment']['status'] ?? null,
            default     => null,
        };
    }
}

# apiUser
if (!function_exists('apiUser')) {
    function apiUser()
    {
        return auth('sanctum')->user();
    }
}

# userId
if (!function_exists('userId')) {
    function userId()
    {
        return auth()->user()->id;
    }
}
# user
if (!function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}

# shopId
if (!function_exists('shopId')) {
    function shopId()
    {
        return auth()->user()->shop_id;
    }
}

# shop
if (!function_exists('shop')) {
    function shop()
    {
        return auth()->user()->shop;
    }
}

# adminShopId
if (!function_exists('adminShopId')) {
    function adminShopId()
    {
        return 1;
    }
}

# routePrefix
if (!function_exists('routePrefix')) {
    function routePrefix()
    {
        $routePrefix = 'admin';
        if (auth()->check() && user()->user_type == 'seller') {
            $routePrefix = 'seller';
        }
        return $routePrefix;
    }
}

# adminApiUrl
if (!function_exists('adminApiUrl')) {
    function adminApiUrl()
    {
        $apiUrl = config('app.url') . '/admin/api';
        // $apiUrl = 'https://4e45-103-176-19-44.ngrok-free.app/armtech/multivendor-ecommerce/admin/api';
        return $apiUrl;
    }
}

# sellerApiUrl
if (!function_exists('sellerApiUrl')) {
    function sellerApiUrl()
    {
        $apiUrl = config('app.url') . '/seller/api';
        // $apiUrl = 'https://4e45-103-176-19-44.ngrok-free.app/armtech/multivendor-ecommerce/admin/api';
        return $apiUrl;
    }
}

# useInventory
if (!function_exists('useInventory')) {
    function useInventory()
    {
        return shop()->manage_stock_by == "default" ? false : true; // or inventory
    }
}

# clear server cache
if (!function_exists('cacheClear')) {
    function cacheClear()
    {
        try {
            Artisan::call('cache:forget spatie.permission.cache');
        } catch (\Throwable $th) {
            //throw $th;
        }

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
    }
}

# get specific setting value
if (!function_exists('getSetting')) {
    function getSetting($key, $default = null, $lang = false)
    {
        $settings = Cache::remember('settings', 86400, function () {
            return SystemSetting::all();
        });

        if ($lang == false) {
            $setting = $settings->where('type', $key)->first();
        } else {
            $setting = $settings->where('type', $key)->where('lang', $lang)->first();
            $setting = !$setting ? $settings->where('type', $key)->first() : $setting;
        }
        return $setting == null ? $default : $setting->value;
    }
}

# overwrite env file
if (!function_exists('overWriteEnvFile')) {
    function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"' . trim($val) . '"';
            if (is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0) {
                file_put_contents($path, str_replace(
                    $type . '="' . env($type) . '"',
                    $type . '=' . $val,
                    file_get_contents($path)
                ));
            } else {
                file_put_contents($path, file_get_contents($path) . "\r\n" . $type . '=' . $val);
            }
        }
    }
}

# file delete
if (!function_exists('file_delete')) {
    function file_delete($file)
    {
        if (File::exists('public/' . $file)) {
            File::delete('public/' . $file);
        }
    }
}

# per page pagination
if (!function_exists('perPage')) {
    function perPage($limit = 15)
    {
        return $limit;
    }
}

# Generate an asset path for the application.
if (!function_exists('staticAsset')) {
    function staticAsset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

# highlights the selected navigation on admin panel
if (!function_exists('areActiveRoutes')) {
    function areActiveRoutes(array $routes, $output = "active")
    {
        foreach ($routes as $route) {
            if (str_contains($route, '.*')) {
                if (str_contains(Route::currentRouteName(), str_replace('.*', '', $route))) return $output;
            } else {
                if (Route::currentRouteName() == $route) return $output;
            }
        }
    }
}

# timezones
function timezones()
{
    return array(
        '(GMT-12:00) International Date Line West' => 'Pacific/Kwajalein',
        '(GMT-11:00) Midway Island' => 'Pacific/Midway',
        '(GMT-11:00) Samoa' => 'Pacific/Apia',
        '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
        '(GMT-09:00) Alaska' => 'America/Anchorage',
        '(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
        '(GMT-08:00) Tijuana' => 'America/Tijuana',
        '(GMT-07:00) Arizona' => 'America/Phoenix',
        '(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',
        '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
        '(GMT-07:00) La Paz' => 'America/Chihuahua',
        '(GMT-07:00) Mazatlan' => 'America/Mazatlan',
        '(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',
        '(GMT-06:00) Central America' => 'America/Managua',
        '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
        '(GMT-06:00) Mexico City' => 'America/Mexico_City',
        '(GMT-06:00) Monterrey' => 'America/Monterrey',
        '(GMT-06:00) Saskatchewan' => 'America/Regina',
        '(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',
        '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
        '(GMT-05:00) Bogota' => 'America/Bogota',
        '(GMT-05:00) Lima' => 'America/Lima',
        '(GMT-05:00) Quito' => 'America/Bogota',
        '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
        '(GMT-04:00) Caracas' => 'America/Caracas',
        '(GMT-04:00) La Paz' => 'America/La_Paz',
        '(GMT-04:00) Santiago' => 'America/Santiago',
        '(GMT-03:30) Newfoundland' => 'America/St_Johns',
        '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
        '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
        '(GMT-03:00) Greenland' => 'America/Godthab',
        '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
        '(GMT-01:00) Azores' => 'Atlantic/Azores',
        '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
        '(GMT) Casablanca' => 'Africa/Casablanca',
        '(GMT) Dublin' => 'Europe/London',
        '(GMT) Edinburgh' => 'Europe/London',
        '(GMT) Lisbon' => 'Europe/Lisbon',
        '(GMT) London' => 'Europe/London',
        '(GMT) UTC' => 'UTC',
        '(GMT) Monrovia' => 'Africa/Monrovia',
        '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
        '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
        '(GMT+01:00) Berlin' => 'Europe/Berlin',
        '(GMT+01:00) Bern' => 'Europe/Berlin',
        '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
        '(GMT+01:00) Brussels' => 'Europe/Brussels',
        '(GMT+01:00) Budapest' => 'Europe/Budapest',
        '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
        '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
        '(GMT+01:00) Madrid' => 'Europe/Madrid',
        '(GMT+01:00) Paris' => 'Europe/Paris',
        '(GMT+01:00) Prague' => 'Europe/Prague',
        '(GMT+01:00) Rome' => 'Europe/Rome',
        '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
        '(GMT+01:00) Skopje' => 'Europe/Skopje',
        '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
        '(GMT+01:00) Vienna' => 'Europe/Vienna',
        '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
        '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
        '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
        '(GMT+02:00) Athens' => 'Europe/Athens',
        '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
        '(GMT+02:00) Cairo' => 'Africa/Cairo',
        '(GMT+02:00) Harare' => 'Africa/Harare',
        '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
        '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
        '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
        '(GMT+02:00) Kyev' => 'Europe/Kiev',
        '(GMT+02:00) Minsk' => 'Europe/Minsk',
        '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
        '(GMT+02:00) Riga' => 'Europe/Riga',
        '(GMT+02:00) Sofia' => 'Europe/Sofia',
        '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
        '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
        '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
        '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
        '(GMT+03:00) Moscow' => 'Europe/Moscow',
        '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
        '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
        '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
        '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
        '(GMT+03:30) Tehran' => 'Asia/Tehran',
        '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
        '(GMT+04:00) Baku' => 'Asia/Baku',
        '(GMT+04:00) Muscat' => 'Asia/Muscat',
        '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
        '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
        '(GMT+04:30) Kabul' => 'Asia/Kabul',
        '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
        '(GMT+05:00) Islamabad' => 'Asia/Karachi',
        '(GMT+05:00) Karachi' => 'Asia/Karachi',
        '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
        '(GMT+05:30) Chennai' => 'Asia/Kolkata',
        '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
        '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
        '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
        '(GMT+05:45) Kathmandu' => 'Asia/Kathmandu',
        '(GMT+06:00) Almaty' => 'Asia/Almaty',
        '(GMT+06:00) Astana' => 'Asia/Dhaka',
        '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
        '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
        '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
        '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
        '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
        '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
        '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
        '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
        '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
        '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
        '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
        '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
        '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
        '(GMT+08:00) Perth' => 'Australia/Perth',
        '(GMT+08:00) Singapore' => 'Asia/Singapore',
        '(GMT+08:00) Taipei' => 'Asia/Taipei',
        '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
        '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
        '(GMT+09:00) Osaka' => 'Asia/Tokyo',
        '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
        '(GMT+09:00) Seoul' => 'Asia/Seoul',
        '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
        '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
        '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
        '(GMT+09:30) Darwin' => 'Australia/Darwin',
        '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
        '(GMT+10:00) Canberra' => 'Australia/Sydney',
        '(GMT+10:00) Guam' => 'Pacific/Guam',
        '(GMT+10:00) Hobart' => 'Australia/Hobart',
        '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
        '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
        '(GMT+10:00) Sydney' => 'Australia/Sydney',
        '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
        '(GMT+11:00) Magadan' => 'Asia/Magadan',
        '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
        '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
        '(GMT+12:00) Auckland' => 'Pacific/Auckland',
        '(GMT+12:00) Fiji' => 'Pacific/Fiji',
        '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
        '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
        '(GMT+12:00) Wellington' => 'Pacific/Auckland',
        '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
    );
}

# return application timezone
if (!function_exists('appTimezone')) {
    function appTimezone()
    {
        return config('app.timezone');
    }
}

# add or return translation
if (!function_exists('translate')) {
    function translate($key, $lang = null)
    {
        if ($lang == null) {
            $lang = Session::get('locale', Config::get('app.locale'));
        }

        $t_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($key)));

        $englishTranslation = Cache::rememberForever('translations-en-US', function () {
            return Translation::where('lang_key', 'en-US')->pluck('t_value', 't_key');
        });

        if (!isset($englishTranslation[$t_key])) {
            # add new translation
            $t_key ? newTranslation('en-US', $t_key, $key) : null;
        }

        # return user session lang
        $localTranslations = Cache::rememberForever("translations-{$lang}", function () use ($lang) {
            return Translation::where('lang_key', $lang)->pluck('t_value', 't_key')->toArray();
        });

        // dd($localTranslations);
        if (isset($localTranslations[$t_key])) {
            return trim($localTranslations[$t_key]);
        }

        return isset($englishTranslation[$t_key]) ? trim($englishTranslation[$t_key]) : $key;
    }
}

# new translation
if (!function_exists('newTranslation')) {
    function newTranslation($lang, $t_key, $key)
    {
        $translation            = new Translation;
        $translation->lang_key  = $lang;
        $translation->t_key     = $t_key;
        $translation->t_value   = str_replace(array("\r", "\n", "\r\n"), "", $key);
        $translation->save();

        # clear cache
        Cache::forget('translations-en-US');
        Cache::forget('translations-' . $lang);

        return trim($key);
    }
}

#  Generate an asset path for the uploaded files.
if (!function_exists('uploadedAsset')) {
    function uploadedAsset($fileId)
    {
        $mediaFile = MediaFile::find($fileId);
        if (!is_null($mediaFile)) {
            return asset($mediaFile->media_file);
        }
        return '';
    }
}

# formats price
if (!function_exists('formatPrice')) {
    function formatPrice($price, $truncate = false, $addSymbol = true, $numberFormat = true)
    {
        if ($numberFormat) {
            $numOfDecimals = getSetting('numOfDecimals') ?? 0;

            // truncate price
            $negativeCheck = $price < 0 ? true : false;
            $price = abs($price);

            if ($price < 1000000) {
                // less than a million
                // decimals
                if ($numOfDecimals > 0) {
                    $price = number_format($price, $numOfDecimals);
                } else {
                    $price = number_format($price, $numOfDecimals, '.', ',');
                }
            } else if ($price < 1000000000) {
                // less than a billion
                $price = number_format($price / 1000000, $numOfDecimals) . 'M';
            } else {
                // at least a billion
                $price = number_format($price / 1000000000, $numOfDecimals) . 'B';
            }
            if ($negativeCheck) {
                $price = "-" . $price;
            }
        }

        if ($addSymbol) {
            $symbol             = getSetting('currencySymbol') ?? '$';
            $symbolAlignment    = (int) getSetting('currencySymbolAlignment') ?? 0;
            switch ($symbolAlignment) {
                case 1:
                    $price = $symbol . $price;
                    break;
                case 2:
                    $price = $price . $symbol;
                    break;
                case 3:
                    $price = $symbol . ' ' . $price; // symbol left & space
                    break;
                default:
                    $price = $price . ' ' .  $symbol; // symbol right & space
                    break;
            }
        }
        return $price;
    }
}

# product base price
if (!function_exists('productBasePrice')) {
    function productBasePrice($product, $formatted = true, $addTax = true)
    {
        $price  = $product->min_price;
        $tax    = 0;

        if ($addTax) {
            foreach ($product->taxes as $product_tax) {
                if ($product_tax->tax->status) {
                    $tax += ($price * $product_tax->tax_percentage) / 100;
                }
            }
        }

        $price += $tax;
        return $formatted ? formatPrice($price) : $price;
    }
}

# generate variation name
if (!function_exists('generateVariationName')) {
    function generateVariationName($code)
    {
        $name = '';
        $code_array = array_filter(explode('/', $code));
        $lstKey = array_key_last($code_array);

        foreach ($code_array as $key2 => $comb) {
            $comb = explode(':', $comb);
            $choice_name = \App\Models\VariationValue::withTrashed()->find($comb[1])->collectTranslation('name');

            $name .= $choice_name;

            if ($lstKey != $key2) {
                $name .= '-';
            }
        }

        return $name;
    }
}

# variation price
if (!function_exists('variationPrice')) {
    function variationPrice($product, $variation, $incTax = true)
    {
        $price = $variation->price;
        $tax = 0;

        # calculate tax
        if ($incTax) {
            foreach ($product->taxes as $productTax) {
                if ($productTax->tax->is_active) {
                    if ($productTax->tax_type == "amount") {
                        $tax += $productTax->tax_value;
                    } else {
                        $tax += ($price * $productTax->tax_value) / 100;
                    }
                }
            }
        }

        $price += $tax;
        return (float) $price;
    }
}

# discounted variation price
if (!function_exists('variationDiscountedPrice')) {
    function variationDiscountedPrice($product, $variation, $incTax = true)
    {
        $price = $variation->price;
        $tax = 0;

        $discount_applicable = false;
        $discountType        = $variation->discount_type;
        $discountValue       = $variation->discount_value;

        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        } elseif (
            strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date
        ) {
            $discount_applicable = true;
        }

        # check campaign
        $campaignProduct = $variation->campaignProducts()->whereHas('campaign', function ($q) {
            $q->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')));
        })->first();

        if ($campaignProduct) {
            $discount_applicable = true;

            $discountType  = $campaignProduct->discount_type;
            $discountValue = $campaignProduct->discount_value;
        };

        # apply discount
        if ($discount_applicable) {
            if ($discountType == "amount" || $discountType == "flat") {
                $price -= $discountValue;
            } else {
                $price = $price - (($price * $discountValue) / 100);
            }
        }

        # calculate tax
        if ($incTax) {
            foreach ($product->taxes as $productTax) {
                if ($productTax->tax->is_active) {
                    if ($productTax->tax_type == "amount") {
                        $tax += $productTax->tax_value;
                    } else {
                        $tax += ($price * $productTax->tax_value) / 100;
                    }
                }
            }
        }

        $price += $tax;
        return (float) $price;
    }
}

# generate variation combinations
if (!function_exists('generateVariationCombinations')) {
    function generateVariationCombinations($productVariationCombinations)
    {
        if (count($productVariationCombinations) == 0) {
            return $productVariationCombinations;
        }

        $productId = $productVariationCombinations[0]->product_id;

        // fetching ids of variation values from product variation combinations table
        $variationValueIds = array();
        foreach ($productVariationCombinations as $combination) {
            $valueIds = array();

            if (isset($variationValueIds[$combination->variation_id])) {
                $valueIds = $variationValueIds[$combination->variation_id];
            }

            // push to array if not already exists
            if (!in_array($combination->variation_value_id, $valueIds)) {
                array_push($valueIds, $combination->variation_value_id);
            }

            $variationValueIds[$combination->variation_id] = $valueIds;
        }

        // preparing variation & variation values in same object
        $result = array();
        foreach ($variationValueIds as $id => $values) {


            $variationValues = array();
            foreach ($values as $value) {
                $colorImageVariationCode = $id . ':' . $value . '/';

                $productVariation = ProductVariation::where('product_id', $productId)->where('code', $colorImageVariationCode)->first();

                $variationValue = VariationValue::find($value);

                if ($variationValue) {
                    $val = array(
                        'id'                      => (int) $value,
                        'name'                    => $variationValue->collectTranslation('name'),
                        'colorCode'               => $variationValue->color_code,
                        'image'                   => uploadedAsset($variationValue->thumbnail_image),
                        'variationImage'          => uploadedAsset($productVariation?->image),
                        'matchVariationCode'      => $id . ':' . $value
                    );

                    array_push($variationValues, $val);
                }
            }



            $variationObjectWithValues = [
                'id'        =>  (int) $id,
                'name'      =>  Variation::find($id)->collectTranslation('name'),
                'values'    => $variationValues,
            ];

            array_push($result, $variationObjectWithValues);
        }

        return $result;
    }
}

# color variation id
if (!function_exists('colorVariationId')) {
    function colorVariationId()
    {
        return 1; // static 1 set for color in db
    }
}

# PO status badge
if (!function_exists('poStatusBadge')) {
    function poStatusBadge($status)
    {
        $className = 'bg-orange-500';
        switch ($status) {
            case 'pending':
                $className = 'bg-theme-primary';
                break;
            case 'received':
                $className = 'bg-green-500';
                break;
            case 'cancelled':
                $className = 'bg-red-500';
                break;
            case 'completed':
                $className = 'bg-green-500';
                break;
            default:
                break;
        }
        return $className;
    }
}

# get shop wise subtotal of cart items
if (!function_exists('getSubtotal')) {
    function getSubtotal($carts, $incTax = true)
    {
        $price = 0;
        foreach ($carts as $cart) {
            $product    = $cart->productVariation->product;
            $variation  = $cart->productVariation;
            $discountedVariationPriceIncTax = variationDiscountedPrice($product, $variation, $incTax);
            $price += (float) $discountedVariationPriceIncTax * $cart->qty;
        }

        return $price;
    }
}

if (!function_exists('validateCouponForProductsAndCategories')) {
    # check coupon for products & categories
    function validateCouponForProductsAndCategories($cartItems, $coupon)
    {
        if ($coupon->couponProducts()->count() > 0) {
            $productIds = $coupon->couponProducts()->pluck('product_id');
            foreach ($cartItems as $cartItem) {
                if (in_array($cartItem->productVariation->product->id, $productIds)) {
                    return true;
                }
            }
        }

        if ($coupon->couponCategories()->count() > 0) {
            $categoryIds = json_decode($coupon->category_ids);
            $productIds = $coupon->couponCategories()->pluck('category_id');
            foreach ($cartItems as $cartItem) {
                $productCategories = $cartItem->productVariation->product->productCategories;
                foreach ($productCategories as $productCategory) {
                    if (in_array($productCategory->category_id, $categoryIds)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

# get shop ratings
if (!function_exists('getShopRatings')) {
    function getShopRatings($reviewQuery)
    {
        $dividedBy = 1;
        $total = $reviewQuery->count();
        if ($total > 0) {
            $dividedBy = $total;
        }
        $sum   = $reviewQuery->sum('rating');

        $review5Query   = clone $reviewQuery;
        $review4Query   = clone $reviewQuery;
        $review3Query   = clone $reviewQuery;
        $review2Query   = clone $reviewQuery;
        $review1Query   = clone $reviewQuery;

        $fiveStarsCount     = $review5Query->where('rating', 5)->count();
        $fourStarsCount     = $review4Query->where('rating', 4)->count();
        $threeStarsCount    = $review3Query->where('rating', 3)->count();
        $twoStarsCount      = $review2Query->where('rating', 2)->count();
        $oneStarsCount      = $review1Query->where('rating', 1)->count();

        $data = [
            'average'           => $sum /  $dividedBy,
            'total'             => $total,
            'fiveStarsCount'    => $fiveStarsCount,
            'fourStarsCount'    => $fourStarsCount,
            'threeStarsCount'   => $threeStarsCount,
            'twoStarsCount'     => $twoStarsCount,
            'oneStarsCount'     => $oneStarsCount,
        ];
        return $data;
    }
}

# calculateCommission of seller on order
if (!function_exists('calculateCommission')) {
    function calculateCommission($order)
    {
        $shop = $order->shop;

        $adminCommissionRate = $shop->admin_commission_percentage;
        $adminCommission     = 0;
        $sellerEarning       = $order->total_amount;

        if ($adminCommissionRate > 0) {
            $adminCommission = ($adminCommissionRate * $sellerEarning) / 100;
            $sellerEarning   -= $adminCommission;
        }

        $commission = $order->commissionHistory;
        if (is_null($commission)) {
            $commission                                 = new CommissionHistory;
            $commission->shop_id                        = $shop->id;
            $commission->order_id                       = $order->id;
            $commission->admin_commission_percentage    = $adminCommissionRate;
            $commission->amount                         = $order->total_amount;
            $commission->admin_earning_amount           = $adminCommission;
            $commission->shop_earning_amount            = $sellerEarning;
            $commission->save();
        }

        // shop balance
        $orderGroup = $order->orderGroup;
        $transaction = $orderGroup->transaction;

        if ($transaction->payment_method == "cash_on_delivery" || $transaction->payment_method == "card_on_delivery") {
            $shop->current_balance -= $adminCommission; // all money is received by seller, so balance is reduced to give admin commission
        } else {
            $shop->current_balance += $sellerEarning;
        }
        $shop->save();
    }
}

# get product ratings
if (!function_exists('getProductRatings')) {
    function getProductRatings($productQuery)
    {
        $avgQuery = clone $productQuery;
        $data = [
            'average'  => (float) $avgQuery->avg('rating'),
            'total'    => $productQuery->count(),
        ];
        return $data;
    }
}

# get order bg-class
if (!function_exists('getOrderBgClass')) {
    function getOrderBgClass($status)
    {
        switch ($status) {
            case 'processing':
                $class = 'bg-badge-processing';
                break;
            case 'confirmed':
                $class = 'bg-badge-confirmed';
                break;
            case 'shipped':
                $class = 'bg-badge-shipped';
                break;
            case 'delivered':
                $class = 'bg-badge-success';
                break;
            case 'cancelled':
                $class = 'bg-badge-danger';
                break;
            default:
                $class = 'bg-badge-default';
                break;
        }
        return $class;
    }
}

# get notification text
if (!function_exists('getNotificationText')) {
    function getNotificationText($notification)
    {
        $type = $notification->type;
        $text = '';
        switch ($type) {
            case 'order':
                $text = 'New Order - ' . $notification->text;
                break;
            case 'payout-request':
                $text = 'Payout Request - ' . formatPrice($notification->text);
                break;
            case 'payout':
                $text = 'Payout Received - ' . formatPrice($notification->text);
                break;
            case 'stock':
                $text = 'Low Stock - ' . $notification->text;
                break;
            case 'seller-registration':
                $text = 'New Seller';
                break;
            case 'customer-registration':
                $text = 'New Customer';
                break;
            default:
                $text = '';
                break;
        }
        return $text;
    }
}

# get notification icon
if (!function_exists('getNotificationIcon')) {
    function getNotificationIcon($notification, $for = "icon")
    {
        $type = $notification->type;
        $class = '';
        $icon = '';
        switch ($type) {
            case 'order':
                $icon  = 'far fa-shopping-cart';
                $class = 'text-green-500 bg-green-200';
                break;
            case 'payout-request':
                $icon  = 'far fa-dollar-sign';
                $class = 'text-indigo-500 bg-indigo-100';
                break;
            case 'payout':
                $icon  = 'far fa-dollar-sign';
                $class = 'text-indigo-500 bg-indigo-100';
                break;
            case 'stock':
                $icon  = 'far fa-exclamation-triangle';
                $class = 'text-red-500 bg-red-100';
                break;
            case 'seller-registration':
                $icon  = 'far fa-user';
                $class = 'text-green-500 bg-green-200';
                break;
            case 'customer-registration':
                $icon  = 'far fa-user';
                $class = 'text-green-500 bg-green-200';
                break;
            default:
                break;
        }
        if ($for == "icon") return $icon;
        return $class;
    }
}

# get notification icon
if (!function_exists('getNotificationLink')) {
    function getNotificationLink($notification, $for = "admin")
    {
        $type = $notification->type;
        $link = '';
        switch ($type) {
            case 'order':
                if ($for == 'admin') {
                    $link  = route('admin.orders.show', $notification->link_info);
                } else {
                    $link  = route('seller.orders.show', $notification->link_info);
                }
                break;
            case 'payout-request':
                $link  = route('admin.sellers.payoutRequests') . '?shopId=' . $notification->link_info;
                break;
            case 'payout':
                $link  = route('seller.earnings.payouts');
                break;
            case 'stock':
                if ($for == 'admin') {
                    $link  = route('admin.products.edit', $notification->link_info);
                } else {
                    $link  = route('seller.products.edit', $notification->link_info);
                }
                break;
            case 'seller-registration':
                if ($for == 'admin') {
                    $link  = route('admin.sellers.index') . '?search=' . $notification->link_info;
                }
                break;
            case 'customer-registration':
                if ($for == 'admin') {
                    $link  = route('admin.customers.index') . '?search=' . $notification->link_info;
                }
                break;
            default:
                break;
        }
        return $link;
    }
}

# clearPaymentSession
if (!function_exists('clearPaymentSession')) {
    function clearPaymentSession()
    {
        session()->forget('order_group_id');
        session()->forget('amount');
        session()->forget('currency');
        session()->forget('temp_user');
    }
}

# getSecondaryColor
if (!function_exists('getSecondaryColor')) {
    function getSecondaryColor($primaryColor, $ratio = 1)
    {
        // Convert hexadecimal color string to RGB components
        list($r, $g, $b) = sscanf($primaryColor, "#%02x%02x%02x");

        // Calculate secondary color by blending with white
        $r = (1 - $ratio) * 255 + $ratio * $r;
        $g = (1 - $ratio) * 255 + $ratio * $g;
        $b = (1 - $ratio) * 255 + $ratio * $b;

        // Combine RGB values into a single color
        $secondaryColor = sprintf("#%02x%02x%02x", $r, $g, $b);

        return $secondaryColor;
    }
}

# sms send
if (!function_exists('sendSMSViaBulkSmsBd')) {
    function sendSMSViaBulkSmsBd($to, $text)
    {
        // Check if the $to number starts with "+88"
        if (substr($to, 0, 3) === "+88") {
            $to = substr($to, 1);  // Remove the "+" by taking the substring from the second character onwards
        }

        if (substr($to, 0, 2) !== "88") {
            $to = "88" . $to;
        }

        $url = "http://bulksmsbd.net/api/smsapi";
        $data = [
            "api_key"   => env('BULK_SMS_API_KEY'),
            "senderid"  => env('SENDER_ID'),
            'number'    => $to,
            'message'   => $text
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
        return true;
    }
}
