<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Http\Services\ProductService;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    # get sliders
    public function sliders(Request $request)
    {
        $sliders        = collect();
        $sliderLinks    = collect();

        if (getSetting('homeSliderImages') != null) {
            $sliders        = json_decode(getSetting('homeSliderImages'), true);
            $sliderLinks    = json_decode(getSetting('homeSliderLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $sliders for this example
        foreach ($sliders as $index => $id) {
            // Check if the index exists in $sliderLinks
            if (isset($sliderLinks[$index])) {
                // If it exists, extract the URL corresponding to the index
                $url = $sliderLinks[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $url = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'image' => uploadedAsset($id),
                'url'   => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get videos
    public function videos(Request $request)
    {
        $homeVideoIds       = collect();

        if (getSetting('homeVideoIds') != null) {
            $homeVideoIds   = json_decode(getSetting('homeVideoIds'), true);
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $homeVideoIds,
        ];
    }

    # get all categories
    public function categories(Request $request)
    {
        $categories     = Category::with('childrenCategories')->orderBy('sorting_order_level', 'asc')->get();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories' => CategoryResource::collection($categories)
            ],
        ];
    }

    # get featured categories
    public function featuredCategories(Request $request)
    {
        $featuredCategories = collect();

        if (getSetting('homeFeaturedCategories') != null) {
            $featuredCategories = json_decode(getSetting('homeFeaturedCategories'), true);
        }

        $categories = Category::whereIn('id', $featuredCategories)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories'                => CategoryResource::collection($categories),
                'showFeaturedCategories'    => getSetting('showHomeFeaturedCategories') ?? 0,
            ],
        ];
    }

    # get featured products
    public function featuredProducts(Request $request)
    {
        $featuredProducts = collect();

        if (getSetting('homeFeaturedProducts') != null) {
            $featuredProducts = json_decode(getSetting('homeFeaturedProducts'), true);
        }

        $products = Product::fromPublishedShops()->isPublished()->whereIn('id', $featuredProducts)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                     => ProductResource::collection($products),
                'showHomeFeaturedProducts'     => getSetting('showHomeFeaturedProducts') ?? 0,
                'homeFeaturedProductBanner'    => uploadedAsset(getSetting('homeFeaturedProductBanner')),
                'homeFeaturedProductLink'      => getSetting('homeFeaturedProductLink'),
            ],
        ];
    }
    # get products
    public function trendyProducts(Request $request)
    {
        $trendyProducts = collect();

        if (getSetting('homeTrendyProducts') != null) {
            $trendyProducts = json_decode(getSetting('homeTrendyProducts'), true);
        }

        $products = Product::fromPublishedShops()->isPublished()->whereIn('id', $trendyProducts)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                      => ProductResource::collection($products),
                'showHomeTrendyProducts'        => getSetting('showHomeTrendyProducts') ?? 0,
            ]
        ];
    }

    # get flash sale products
    public function flashSaleProducts(Request $request)
    {
        $campaign          = null;
        $flashSaleProducts = collect();

        if (getSetting('homeFlashSale') != null) {
            $campaign = Campaign::where('id', getSetting('homeFlashSale'))->first();
        }

        if (!is_null($campaign)) {

            $flashSaleProducts = Product::isPublished()->whereHas('campaignProducts', function ($q) use ($campaign) {
                $q->where('campaign_id', $campaign->id);
            });

            $columns    = (new ProductService)->getSpecificColumns();
            $flashSaleProducts   = $flashSaleProducts->take(15)->get($columns);
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'showHomeFlashSale'         => getSetting('showHomeFlashSale') ?? 0,
                'flashSaleCampaign'         => $campaign ? new CampaignResource($campaign) : null,
                'flashSaleProducts'         => ProductResource::collection($flashSaleProducts),
            ],
        ];
    }

    # get flash productSectionOne
    public function productSectionOne(Request $request)
    {
        $homeProductSectionOne = collect();

        if (getSetting('homeProductSectionOne') != null) {
            $homeProductSectionOne = json_decode(getSetting('homeProductSectionOne'), true);
        }

        $products = Product::fromPublishedShops()->isPublished()->whereIn('id', $homeProductSectionOne)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                     => ProductResource::collection($products),
                'showHomeProductSectionOne'    => getSetting('showHomeProductSectionOne') ?? 0,
                'homeProductSectionOneTitle'   => getSetting('homeProductSectionOneTitle') ? translate(getSetting('homeProductSectionOneTitle')) : ''
            ],
        ];
    }

    # get full width banner
    public function fullWidthBanner(Request $request)
    {
        $banners        = collect();
        $bannerLinks    = collect();

        if (getSetting('homeFullWidthBanners') != null) {
            $banners        = json_decode(getSetting('homeFullWidthBanners'), true);
            $bannerLinks    = json_decode(getSetting('homeFullWidthBannerLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $banners for this example
        foreach ($banners as $index => $id) {
            // Check if the index exists in $bannerLinks
            if (isset($bannerLinks[$index])) {
                // If it exists, extract the URL corresponding to the index
                $url = $bannerLinks[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $url = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'image' => uploadedAsset($id),
                'url'   => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get flash productSectionTwo
    public function productSectionTwo(Request $request)
    {
        $homeProductSectionTwo = collect();

        if (getSetting('homeProductSectionTwo') != null) {
            $homeProductSectionTwo = json_decode(getSetting('homeProductSectionTwo'), true);
        }

        $products = Product::fromPublishedShops()->isPublished()->whereIn('id', $homeProductSectionTwo)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'                     => ProductResource::collection($products),
                'showHomeProductSectionTwo'    => getSetting('showHomeProductSectionTwo') ?? 0,
                'homeProductSectionTwoTitle'   => getSetting('homeProductSectionTwoTitle') ? translate(getSetting('homeProductSectionTwoTitle')) : ''
            ],
        ];
    }

    # get fourBannersInARow
    public function fourBannersInARow(Request $request)
    {
        $banners        = collect();
        $bannerLinks    = collect();

        if (getSetting('homeFourInRowBanners') != null) {
            $banners        = json_decode(getSetting('homeFourInRowBanners'), true);
            $bannerLinks    = json_decode(getSetting('homeFourInRowBannerLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $banners for this example
        foreach ($banners as $index => $id) {
            // Check if the index exists in $bannerLinks
            if (isset($bannerLinks[$index])) {
                // If it exists, extract the URL corresponding to the index
                $url = $bannerLinks[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $url = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'image' => uploadedAsset($id),
                'url'   => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get featured Shops
    public function featuredShops(Request $request)
    {
        $featuredShops = collect();

        if (getSetting('homeFeaturedShops') != null) {
            $featuredShops = json_decode(getSetting('homeFeaturedShops'), true);
        }

        $shops = Shop::isApproved()->isPublished()->whereIn('id', $featuredShops)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'shops'                => ShopResource::collection($shops),
                'showFeaturedShops'    => getSetting('showHomeFeaturedShops') ?? 0,
            ],
        ];
    }

    # get twoBannersInARow
    public function twoBannersInARow(Request $request)
    {
        $banners        = collect();
        $bannerLinks    = collect();

        if (getSetting('homeTwoInRowBanners') != null) {
            $banners        = json_decode(getSetting('homeTwoInRowBanners'), true);
            $bannerLinks    = json_decode(getSetting('homeTwoInRowBannerLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $banners for this example
        foreach ($banners as $index => $id) {
            // Check if the index exists in $bannerLinks
            if (isset($bannerLinks[$index])) {
                // If it exists, extract the URL corresponding to the index
                $url = $bannerLinks[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $url = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'image' => uploadedAsset($id),
                'url'   => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # new arrivals
    public function newArrivals(Request $request)
    {
        $products = Product::fromPublishedShops()->isPublished()->latest()->take(10)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => ProductResource::collection($products),
        ];
    }

    # get featured brands
    public function featuredBrands(Request $request)
    {
        $featuredBrands = collect();

        if (getSetting('homeFeaturedBrands') != null) {
            $featuredBrands = json_decode(getSetting('homeFeaturedBrands'), true);
        }

        $brands = Brand::isActive()->whereIn('id', $featuredBrands)->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'brands'                => BrandResource::collection($brands),
                'showFeaturedBrands'    => getSetting('showHomeFeaturedBrands') ?? 0,
            ],
        ];
    }

    # get threeBannersInARow
    public function threeBannersInARow(Request $request)
    {
        $banners        = collect();
        $bannerLinks    = collect();

        if (getSetting('homeThreeInRowBanners') != null) {
            $banners        = json_decode(getSetting('homeThreeInRowBanners'), true);
            $bannerLinks    = json_decode(getSetting('homeThreeInRowBannerLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        // Iterate over one of the arrays, assuming $banners for this example
        foreach ($banners as $index => $id) {
            // Check if the index exists in $bannerLinks
            if (isset($bannerLinks[$index])) {
                // If it exists, extract the URL corresponding to the index
                $url = $bannerLinks[$index];
            } else {
                // If not, set the URL to null or handle it as needed
                $url = null;
            }

            // Add the ID and URL to the merged data array
            $mergedData[] = [
                'image' => uploadedAsset($id),
                'url'   => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get categoryProducts
    public function categoryProducts(Request $request)
    {
        $homeCategories         = collect();
        $homeCategoryImages     = collect();
        $homeCategoryLinks            = collect();

        if (getSetting('homeCategories') != null) {
            $homeCategories         = json_decode(getSetting('homeCategories'), true);
            $homeCategoryImages     = json_decode(getSetting('homeCategoryImages'), true);
            $homeCategoryLinks      = json_decode(getSetting('homeCategoryLinks'), true);
        }

        // Initialize an empty array to store the merged data
        $mergedData = [];

        foreach ($homeCategories as $index => $id) {

            if (isset($homeCategoryImages[$index])) {
                $image = uploadedAsset($homeCategoryImages[$index]);
            } else {
                $image = uploadedAsset(0);
            }

            if (isset($homeCategoryLinks[$index])) {
                $url = $homeCategoryLinks[$index];
            } else {
                $url = null;
            }

            $category = Category::where('id', $id)->first();
            $products = Product::fromPublishedShops()->isPublished()->with('productCategories')->whereHas('productCategories', function ($query) use ($category) {
                return $query->where('category_id', $category->id);
            })->take(10)->get();

            $mergedData[] = [
                'category'  => new CategoryResource($category),
                'products'  => ProductResource::collection($products),
                'image'     => $image,
                'url'       => $url,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => $mergedData,
        ];
    }

    # get aboutUs
    public function aboutUs(Request $request)
    {
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'showHomeAboutUsSection'    => getSetting('showHomeAboutUsSection') ?? 0,
                'homeAboutUsTitle'          => getSetting('homeAboutUsTitle') ? translate(getSetting('homeAboutUsTitle')) : "",
                'homeAboutUsSubTitle'       => getSetting('homeAboutUsSubTitle') ? translate(getSetting('homeAboutUsSubTitle')) : "",
                'homeAboutUsText'           => getSetting('homeAboutUsText') ? translate(getSetting('homeAboutUsText')) : "",
                'homeAboutUsImage'          => uploadedAsset(getSetting('homeAboutUsImage'))
            ],
        ];
    }

    # get main categories
    public function mainCategories(Request $request)
    {
        $mainCategories = collect();

        if (getSetting('homeMainCategories') != null) {
            $mainCategories = json_decode(getSetting('homeMainCategories'), true);
        }

        $categories = Category::whereIn('id', $mainCategories)->with('childrenCategories')->get();
        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'categories'                => CategoryResource::collection($categories),
                'showHomeMainCategories'    => getSetting('showHomeMainCategories') ?? 0,
            ],
        ];
    }

    # get footer
    public function footer(Request $request)
    {
        $topCategories = Category::orderBy('total_sale_amount', 'DESC')->take(6)->get();

        $quickLinks = collect();
        if (getSetting('homeFooterQuickLinks') != null) {
            $quickLinks = json_decode(getSetting('homeFooterQuickLinks'), true);
        }
        $quickLinkPages = Page::where('is_active', 1)->where('type', 'custom')->whereIn('id', $quickLinks)->get();

        $defaultPages = Page::where('is_active', 1)->where('type', 'default')->get();

        $popularTags = getSetting('homeFooterPopularTags') ? json_decode(getSetting('homeFooterPopularTags'), true) : [];

        $popularTags = collect();

        if (getSetting('homeFooterPopularTags') != null) {
            $popularTags = json_decode(getSetting('homeFooterPopularTags'), true);
        }

        $tags = Tag::whereIn('id', $popularTags)->pluck('name');

        $quickLinks        = collect();
        $quickLinkTitles    = collect();

        if (getSetting('homeFooterQuickLinks') != null) {
            $quickLinks        = json_decode(getSetting('homeFooterQuickLinks'), true);
            $quickLinkTitles    = json_decode(getSetting('homeFooterQuickLinkTitles'), true);
        }

        $mergedData = [];
        foreach ($quickLinks as $index => $link) {
            if (isset($quickLinkTitles[$index])) {
                $title = $quickLinkTitles[$index];
            } else {
                $title = null;
            }
            $mergedData[] = [
                'title' => translate($title),
                'url'   => $link,
            ];
        }

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'homeFooterLogo'             => uploadedAsset(getSetting('homeFooterLogo')),
                'homeFooterCustomerSupport'  => getSetting('homeFooterCustomerSupport') ?? "",
                'homeFooterAddress'          => getSetting('homeFooterAddress') ?? "",
                'homeFooterEmail'            => getSetting('homeFooterEmail') ?? "",

                'showHomeFacebookLink'       => getSetting('showHomeFacebookLink') ?? 0,
                'homeFooterFacebookLink'     => getSetting('homeFooterFacebookLink') ?? "",

                'showHomeTwitterLink'        => getSetting('showHomeTwitterLink') ?? 0,
                'homeFooterTwitterLink'      => getSetting('homeFooterTwitterLink') ?? "",

                'showHomeInstagramLink'      => getSetting('showHomeInstagramLink') ?? 0,
                'homeFooterInstagramLink'    => getSetting('homeFooterInstagramLink') ?? "",

                'showHomeYoutubeLink'        => getSetting('showHomeYoutubeLink') ?? 0,
                'homeFooterYoutubeLink'        => getSetting('homeFooterYoutubeLink') ?? "",

                'showHomeLinkedInLink'       => getSetting('showHomeLinkedInLink') ?? 0,
                'homeFooterLinkedInLink'     => getSetting('homeFooterLinkedInLink') ?? "",

                'quickLinkPages'             => $mergedData,
                'topCategories'              => CategoryResource::collection($topCategories),

                'popularTags'                => $tags,
                'defaultPages'               => PageResource::collection($defaultPages),

                'showHomeFooterPlayStoreLink'  => getSetting('showHomeFooterPlayStoreLink') ?? 0,
                'homeFooterPlayStoreLink'      => getSetting('homeFooterPlayStoreLink') ?? "",

                'showHomeFooterAppStoreLink'  => getSetting('showHomeFooterAppStoreLink') ?? 0,
                'homeFooterAppStoreLink'      => getSetting('homeFooterAppStoreLink') ?? "",

                'homeFooterCopyrightText'     => getSetting('homeFooterCopyrightText') ? translate(getSetting('homeFooterCopyrightText')) : '',
                'homeFooterSecuredPayments'   => uploadedAsset(getSetting('homeFooterSecuredPayments'))
            ],
        ];
    }

    # page details
    public function pageDetails($slug)
    {
        $data    = [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => null,
        ];

        $page = Page::where('is_active', 1)->where('slug', $slug)->first();

        if (!is_null($page)) {
            $data['result'] = new PageResource($page);
        } else {
            $data['message'] = translate('Page not found');
        }

        return $data;
    }
}
