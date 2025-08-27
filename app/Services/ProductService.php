<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Brand;
use App\Models\CampaignProduct;
use App\Models\Category;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductBadge;
use App\Models\ProductCategory;
use App\Models\ProductReview;
use App\Models\ProductTag;
use App\Models\ProductTax;
use App\Models\ProductTranslation;
use App\Models\ProductVariation;
use App\Models\ProductVariationCombination;
use App\Models\ProductVariationStock;
use App\Models\Tag;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationValue;
use Str;

class ProductService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey     = null;
        $isPublished   = null;
        $limit         = $request->limit ?? perPage();

        $products = Product::shop()->latest();

        if ($request->search != null) {
            $products   = $products->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }

        if ($request->isPublished != null) {
            $products = $products->where('is_published', $request->isPublished);
            $isPublished    = $request->isPublished;
        }

        if ($request->categoryId != null) {
            $products = $products->with('productCategories')->whereHas('productCategories', function ($query) use ($request) {
                return $query->where('category_id', $request->categoryId);
            });
        }

        if ($request->tagId != null) {
            $products = $products->with('productTags')->whereHas('productTags', function ($query) use ($request) {
                return $query->where('tag_id', $request->tagId);
            });
        }

        if ($request->brandId != null) {
            $products = $products->where('brand_id', $request->brandId);
        }

        $products   = $products->paginate($limit);

        $categories = Category::where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->with('childrenCategories')
            ->get();

        $brands   = Brand::isActive()->orderBy('name', 'ASC')->get();
        $tags     = Tag::orderBy('name', 'ASC')->get();


        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'products'     => $products,
                'isPublished'  => $isPublished,
                'categories'   => $categories,
                'brands'       => $brands,
                'tags'         => $tags,
                'searchKey'    => $searchKey,
            ],
        ];

        return $data;
    }

    # return view of create form
    public static function create()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->with('childrenCategories')
            ->get();

        $brands     = Brand::isActive()->orderBy('name', 'ASC')->get();
        $units      = Unit::isActive()->orderBy('name', 'ASC')->get();
        $variations = Variation::isActive()->get();
        $taxes      = Tax::isActive()->orderBy('name', 'ASC')->get();
        $badges     = Badge::isActive()->orderBy('name', 'ASC')->get();
        $tags       = Tag::orderBy('name', 'ASC')->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'categories'    => $categories,
                'brands'        => $brands,
                'units'         => $units,
                'variations'    => $variations,
                'taxes'         => $taxes,
                'badges'        => $badges,
                'tags'          => $tags,
            ],
        ];

        return $data;
    }

    # generate variation combinations
    public static function generateVariationCombinations($request)
    {
        $variations_and_values = array();

        if ($request->has('chosen_variations')) {
            $chosen_variations = $request->chosen_variations;
            sort($chosen_variations, SORT_NUMERIC);

            foreach ($chosen_variations as $key => $option) {

                $option_name = 'option_' . $option . '_choices'; # $option = variation_id
                $value_ids = array();

                if ($request->has($option_name)) {

                    $variation_option_values = $request[$option_name];
                    sort($variation_option_values, SORT_NUMERIC);

                    foreach ($variation_option_values as $item) {
                        array_push($value_ids, $item);
                    }
                    $variations_and_values[$option] =  $value_ids;
                }
            }
        }

        $combinations = array(array());
        foreach ($variations_and_values as $variation => $variation_values) {
            $tempArray = array();
            foreach ($combinations as $combination_item) {
                foreach ($variation_values as $variation_value) {
                    $tempArray[] = $combination_item + array($variation => $variation_value);
                }
            }
            $combinations = $tempArray;
        }

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'combinations' => $combinations
            ],
        ];

        return $data;
    }

    # get variation values to add new product
    public static function getVariationValues($request)
    {
        $variation_id = $request->variation_id;
        $variation_values = VariationValue::isActive()->where('variation_id', $variation_id)->get();
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'variation_values'    => $variation_values,
                'variation_id'         => $variation_id
            ],
        ];
        return $data;
    }

    # new chosen variation
    public static function getNewVariation($request)
    {
        $variations = Variation::isActive();

        foreach ($request->chosen_variations as $key => $value) {
            if ($value == NULL) {
                $variations = $variations->get();
                $data = [
                    'status'        => 200,
                    'message'       => '',
                    'result'    => [
                        'variations'    => $variations,
                        'count'         => -1
                    ],
                ];

                return $data;
            }
        }

        $variations = $variations->whereNotIn('id', $request->chosen_variations)->get();

        $data = [
            'status'        => 200,
            'message'       => '',
            'result'    => [
                'variations'    => $variations,
                'count'         => count($variations),
                'chosenCounter' => count($request->chosen_variations) + 1
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        if ($request->has('is_variant') && !$request->has('variations')) {
            $data = [
                'status'    => 403,
                'message'   => translate('Invalid product variations, please check again'),
                'result'    => [],
            ];
            return $data;
        }

        try {
            $product                    = new Product;
            $product->shop_id           = shop()->id;
            $product->name              = $request->name;
            $product->slug              = Str::slug($request->name, '-');
            $product->brand_id          = $request->brand_id;
            $product->unit_id           = $request->unit_id;

            $product->thumbnail_image   = $request->thumbnail_image;
            $product->gallery_images    = $request->gallery_images;
            $product->real_pictures    = $request->real_pictures;

            $product->description       = $request->description;

            # min-max price
            if ($request->has('is_variant') && $request->has('variations')) {
                $product->min_price =  min(array_column($request->variations, 'price'));
                $product->max_price =  max(array_column($request->variations, 'price'));
            } else {
                $product->min_price =  $request->price;
                $product->max_price =  $request->price;
            }

            # discount
            if ($request->date_range != null) {
                if (Str::contains($request->date_range, '-')) {
                    $date_var = explode(" - ", $request->date_range);
                } else {
                    $date_var = [date("d/m/Y"), date("d/m/Y")];
                }
                $product->discount_start_date = strtotime($date_var[0]);
                $product->discount_end_date   = strtotime($date_var[1]);
            }
            // $product->discount_info   = $request->discount_info;

            # stock qty based on all variations / no variation 
            if (useInventory()) {
                $product->stock_qty = 0;
            } else {
                $product->stock_qty   = ($request->has('is_variant') && $request->has('variations')) ? array_sum(array_column($request->variations, 'stock_qty')) : $request->stock_qty;
            }
            $product->alert_qty   = $request->alert_qty;


            $product->is_published         = $request->is_published ?  $request->is_published : 0;
            $product->min_purchase_qty     = $request->min_purchase_qty;
            $product->max_purchase_qty     = $request->max_purchase_qty;
            $product->est_delivery_time    = $request->est_delivery_time;
            $product->commission_rate      = $request->commission_rate ?? 0;
            $product->emi_info             = $request->emi_info;

            $product->has_variation        = ($request->has('is_variant') && $request->has('variations')) ? 1 : 0;

            $product->has_warranty         = $request->has_warranty ? $request->has_warranty : 0;
            $product->has_emi              = $request->has_emi ? $request->has_emi : 0;
            $product->warranty_info        = $request->warranty_info;

            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_image        = $request->meta_image;

            $product->save();

            # Product translation
            $ProductTranslation = ProductTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'product_id' => $product->id]);
            $ProductTranslation->name        = $request->name;
            $ProductTranslation->description = $request->description;
            $ProductTranslation->save();

            # tags
            $product->tags()->sync($request->tag_ids);

            # badges
            $product->productBadges()->sync($request->badge_ids);

            # category
            $product->categories()->sync($request->category_ids);

            # taxes
            $tax_data = array();
            $tax_ids  = array();
            if ($request->has('tax_values')) {
                foreach ($request->tax_values as $key => $tax_value) {
                    array_push($tax_data, [
                        'tax_value' => $tax_value,
                        'tax_type' => $request->tax_types[$key]
                    ]);
                }
                $tax_ids = $request->tax_ids;
            }
            $taxes = array_combine($tax_ids, $tax_data);
            $product->productTaxes()->sync($taxes);

            # variations, combinations & stocks of warehouses
            $warehouses = shop()->warehouses;

            if ($request->has('is_variant') && $request->has('variations')) {
                foreach ($request->variations as $variation) {
                    $product_variation                  = new ProductVariation();
                    $product_variation->product_id      = $product->id;
                    $product_variation->code            = $variation['code'];
                    $product_variation->price           = $variation['price'];
                    $product_variation->image           = $variation['image'];
                    $product_variation->sku             = $variation['sku'];
                    $product_variation->discount_value  = $variation['discount_value'];
                    $product_variation->discount_type   = $variation['discount_type'];
                    $product_variation->save();

                    foreach ($warehouses as $warehouse) {
                        $product_variation_stock                        = new ProductVariationStock;
                        $product_variation_stock->product_variation_id  = $product_variation->id;
                        $product_variation_stock->warehouse_id          = $warehouse->id;

                        if (!useInventory() && $warehouse->is_default == 1) {
                            $product_variation_stock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $product_variation_stock->stock_qty  = 0;
                        }

                        $product_variation_stock->save();
                    }

                    foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                        $product_variation_combination                         = new ProductVariationCombination;
                        $product_variation_combination->product_id             = $product->id;
                        $product_variation_combination->product_variation_id   = $product_variation->id;
                        $product_variation_combination->variation_id           = explode(":", $combination)[0];
                        $product_variation_combination->variation_value_id     = explode(":", $combination)[1];
                        $product_variation_combination->save();
                    }
                }
            } else {
                $variation                  = new ProductVariation;
                $variation->product_id      = $product->id;
                $variation->price           = $request->price;
                $variation->image           = $request->image;
                $variation->sku             = $request->sku;
                $variation->discount_value  = $request->discount_value;
                $variation->discount_type   = $request->discount_type;
                $variation->save();

                foreach ($warehouses as $warehouse) {
                    $product_variation_stock                        = new ProductVariationStock;
                    $product_variation_stock->product_variation_id  = $variation->id;
                    $product_variation_stock->warehouse_id          = $warehouse->id;

                    if (!useInventory() && $warehouse->is_default == 1) {
                        $product_variation_stock->stock_qty  = $request->stock_qty;
                    } else {
                        $product_variation_stock->stock_qty  = 0;
                    }

                    $product_variation_stock->save();
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Product has been added successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # return view of edit form
    public static function edit($request, $id)
    {
        try {
            Notification::where('link_info', $id)->where('type', 'stock')->where('shop_id', shopId())->update([
                'is_read' => 1
            ]);
        } catch (\Throwable $th) {
        }


        try {
            $lang_key = $request->lang_key;
            $language = Language::where('is_active', 1)->where('code', $lang_key)->first();
            if (!$language) {
                if (!$language) {
                    $data = [
                        'status'    => 403,
                        'message'   => translate('Language you are trying to translate is not available or not active'),
                        'result'    => [],
                    ];
                    return $data;
                }
            }
            $product = Product::shop()->findOrFail($id);
            $categories = Category::where('parent_id', 0)
                ->orderBy('name', 'ASC')
                ->with('childrenCategories')
                ->get();

            $brands     = Brand::isActive()->orderBy('name', 'ASC')->get();
            $units      = Unit::isActive()->orderBy('name', 'ASC')->get();
            $variations = Variation::isActive()->get();
            $taxes      = Tax::isActive()->orderBy('name', 'ASC')->get();
            $badges     = Badge::isActive()->orderBy('name', 'ASC')->get();
            $tags       = Tag::orderBy('name', 'ASC')->get();

            $data = [
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'product'       => $product,
                    'categories'    => $categories,
                    'brands'        => $brands,
                    'units'         => $units,
                    'variations'    => $variations,
                    'taxes'         => $taxes,
                    'badges'        => $badges,
                    'lang_key'      => $lang_key,
                    'tags'          => $tags,
                ],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # add new data
    public static function update($request, $id)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        if ($request->has('is_variant') && !$request->has('variations')) {
            $data = [
                'status'    => 403,
                'message'   => translate('Invalid product variations, please check again'),
                'result'    => [],
            ];
            return $data;
        }

        // try {
        $product                    = Product::where('id', $id)->first();
        $oldProduct                 = clone $product;

        if ($product->shop_id != shop()->id) {
            abort(403);
        }

        if ($request->lang_key == config("app.default_language")) {
            $product->name              = $request->name;
            $product->slug              = (!is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-');
            $product->brand_id          = $request->brand_id;
            $product->unit_id           = $request->unit_id;

            $product->thumbnail_image   = $request->thumbnail_image;
            $product->gallery_images    = $request->gallery_images;
            $product->real_pictures    = $request->real_pictures;

            $product->description       = $request->description;

            # min-max price
            if ($request->has('is_variant') && $request->has('variations')) {
                $product->min_price =  min(array_column($request->variations, 'price'));
                $product->max_price =  max(array_column($request->variations, 'price'));
            } else {
                $product->min_price =  $request->price;
                $product->max_price =  $request->price;
            }

            # discount 
            if ($request->date_range != null) {
                if (Str::contains($request->date_range, '-')) {
                    $date_var = explode(" - ", $request->date_range);
                } else {
                    $date_var = [date("d/m/Y"), date("d/m/Y")];
                }
                $product->discount_start_date = strtotime($date_var[0]);
                $product->discount_end_date   = strtotime($date_var[1]);
            }
            // $product->discount_info   = $request->discount_info;

            # stock qty based on all variations / no variation 
            if (!useInventory()) {
                $product->stock_qty   = ($request->has('is_variant') && $request->has('variations')) ? array_sum(array_column($request->variations, 'stock_qty')) : $request->stock_qty;
            }

            $product->alert_qty   = $request->alert_qty;

            $product->is_published         = $request->is_published ? $request->is_published : 0;
            $product->min_purchase_qty     = $request->min_purchase_qty;
            $product->max_purchase_qty     = $request->max_purchase_qty;
            $product->est_delivery_time    = $request->est_delivery_time;
            $product->commission_rate      = $request->commission_rate ?? 0;
            $product->emi_info             = $request->emi_info;

            $product->has_warranty         = $request->has_warranty ? $request->has_warranty : 0;
            $product->has_emi              = $request->has_emi ? $request->has_emi : 0;

            $product->warranty_info        = $request->warranty_info;

            $product->meta_title        = $request->meta_title;
            $product->meta_description  = $request->meta_description;
            $product->meta_keywords     = $request->meta_keywords;
            $product->meta_image        = $request->meta_image;


            $product->save();

            # tags
            $product->tags()->sync($request->tag_ids);

            # badges
            $product->productBadges()->sync($request->badge_ids);

            # category
            $product->categories()->sync($request->category_ids);

            # taxes
            $tax_data = array();
            $tax_ids  = array();
            if ($request->has('tax_values')) {
                foreach ($request->tax_values as $key => $tax_value) {
                    array_push($tax_data, [
                        'tax_value' => $tax_value,
                        'tax_type' => $request->tax_types[$key]
                    ]);
                }
                $tax_ids = $request->tax_ids;
            }
            $taxes = array_combine($tax_ids, $tax_data);
            $product->productTaxes()->sync($taxes);


            # variations, combinations & stocks of warehouses
            $warehouses = shop()->warehouses;

            if ($request->has('is_variant') && $request->has('variations')) {
                $product->has_variation        = 1;
                $product->save();

                $requested_variations           = collect($request->variations);

                $requested_variation_codes      = $requested_variations->pluck('code')->toArray();
                $old_variations_codes           = $product->variations()->pluck('code')->toArray();

                $old_matched_variations         = $requested_variations->whereIn('code', $old_variations_codes);
                $new_variations                 = $requested_variations->whereNotIn('code', $old_variations_codes);

                # delete old variations that isn't requested
                $product->variations()->whereNotIn('code', $requested_variation_codes)->orWhere('code', null)->each(function ($variation) {
                    foreach ($variation->combinations as $comb) {
                        $comb->delete();
                    }
                    $variation->productVariationStocks()->delete();
                    // $variation->cartItems()->delete();
                    $variation->delete();
                });

                # update old matched variations
                foreach ($old_matched_variations as $variation) {
                    $pVariation                  = ProductVariation::where('product_id', $product->id)->where('code', $variation['code'])->first();
                    $pVariation->price           = $variation['price'];
                    $pVariation->image           = $variation['image'];
                    $pVariation->sku             = $variation['sku'];
                    $pVariation->discount_value  = $variation['discount_value'];
                    $pVariation->discount_type   = $variation['discount_type'];
                    $pVariation->save();

                    # update stock of this variation
                    foreach ($warehouses as $warehouse) {
                        $productVariationStock = $pVariation->productVariationStocks()->where('warehouse_id', $warehouse->id)->first();
                        if (is_null($productVariationStock)) {
                            $productVariationStock                          = new ProductVariationStock;
                            $productVariationStock->product_variation_id    = $pVariation->id;
                            $productVariationStock->warehouse_id            = $warehouse->id;
                        }
                        if (!useInventory() && $warehouse->is_default == 1) {
                            $productVariationStock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $productVariationStock->stock_qty  = 0;
                        }
                        $productVariationStock->save();
                    }
                }

                # store new requested variations
                foreach ($new_variations as $variation) {
                    $product_variation                  = new ProductVariation;
                    $product_variation->product_id      = $product->id;
                    $product_variation->code            = $variation['code'];
                    $product_variation->price           = $variation['price'];
                    $product_variation->image           = $variation['image'];
                    $product_variation->sku             = $variation['sku'];
                    $product_variation->discount_value  = $variation['discount_value'];
                    $product_variation->discount_type   = $variation['discount_type'];
                    $product_variation->save();

                    foreach ($warehouses as $warehouse) {
                        $product_variation_stock                        = new ProductVariationStock;
                        $product_variation_stock->product_variation_id  = $product_variation->id;
                        $product_variation_stock->warehouse_id          = $warehouse->id;

                        if (!useInventory() && $warehouse->is_default == 1) {
                            $product_variation_stock->stock_qty  = $variation['stock_qty'];
                        } else {
                            $product_variation_stock->stock_qty  = 0;
                        }
                        $product_variation_stock->save();
                    }

                    foreach (array_filter(explode("/", $variation['code'])) as $combination) {
                        $product_variation_combination                         = new ProductVariationCombination;
                        $product_variation_combination->product_id             = $product->id;
                        $product_variation_combination->product_variation_id   = $product_variation->id;
                        $product_variation_combination->variation_id           = explode(":", $combination)[0];
                        $product_variation_combination->variation_value_id     = explode(":", $combination)[1];
                        $product_variation_combination->save();
                    }
                }
            } else {
                $product->has_variation        = 0;
                $product->save();
                # check if old product is variant then delete all old variation & combinations
                if ($oldProduct->has_variation) {
                    foreach ($product->variations as $variation) {
                        foreach ($variation->combinations as $comb) {
                            $comb->delete();
                        }
                        $variation->productVariationStocks()->delete();
                        $variation->delete();
                    }
                    $variation = new ProductVariation;
                } else {
                    $variation = $oldProduct->variations()->first();
                    if (is_null($variation)) {
                        $variation = new ProductVariation;
                    }
                }

                $variation->product_id      = $product->id;
                $variation->price           = $request->price;
                $variation->image           = $request->image;
                $variation->sku             = $request->sku;
                $variation->discount_value  = $request->discount_value;
                $variation->discount_type   = $request->discount_type;
                $variation->save();

                foreach ($warehouses as $warehouse) {
                    $productVariationStock = $variation->productVariationStocks()->where('warehouse_id', $warehouse->id)->first();
                    if (is_null($productVariationStock)) {
                        $productVariationStock  = new ProductVariationStock;
                    }
                    $productVariationStock->product_variation_id  = $variation->id;
                    $productVariationStock->warehouse_id          = $warehouse->id;

                    if (!useInventory() && $warehouse->is_default == 1) {
                        $productVariationStock->stock_qty  = $request->stock_qty;
                    } else {
                        $productVariationStock->stock_qty  = $productVariationStock?->stock_qty ?? 0;
                    }

                    $productVariationStock->save();
                }
            }
        }

        # Product translation
        $ProductTranslation = ProductTranslation::firstOrNew(['lang_key' => $request->lang_key, 'product_id' => $product->id]);
        $ProductTranslation->name = $request->name;
        $ProductTranslation->description = $request->description;
        $ProductTranslation->save();

        $data = [
            'status'    => 200,
            'message'   => translate('Product has been updated successfully'),
            'result'    => [],
        ];
        return $data;
        // } catch (\Throwable $th) {
        //     $data = [
        //         'status'    => 403,
        //         'message'   => translate('Something went wrong'),
        //         'result'    => [],
        //     ];
        //     return $data;
        // }
    }

    # update status
    public static function updateStatus($request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        try {
            $product = Product::shop()->findOrFail($request->id);
            $product->is_published = $request->isActive;
            $product->save();
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    public static function duplicate($id)
    {
        $product                        = Product::find($id);
        $productNew                     = $product->replicate();
        $productNew->stock_qty          = 0;
        $productNew->total_sale_count   = 0;
        $productNew->slug               = Str::slug($productNew->name, '-') . '-' . strtolower(Str::random(5));

        if ($productNew->save()) {

            //categories duplicate
            foreach ($product->productCategories as $key => $category) {
                $pCategory                 = new ProductCategory;
                $pCategory->product_id     = $productNew->id;
                $pCategory->category_id    = $category->category_id;
                $pCategory->save();
            }

            //badges duplicate
            foreach ($product->badges as $key => $badge) {
                $pBadge                 = new ProductBadge;
                $pBadge->product_id     = $productNew->id;
                $pBadge->badge_id    = $badge->badge_id;
                $pBadge->save();
            }

            //tags duplicate
            foreach ($product->productTags as $key => $tag) {
                $pTag                 = new ProductTag;
                $pTag->product_id     = $productNew->id;
                $pTag->tag_id    = $tag->tag_id;
                $pTag->save();
            }

            // taxes duplicate
            foreach ($product->taxes as $key => $tax) {
                $pTax                = new ProductTax;
                $pTax->product_id    = $productNew->id;
                $pTax->tax_id        = $tax->tax_id;
                $pTax->tax_value     = $tax->tax_value;
                $pTax->tax_type      = $tax->tax_type;
                $pTax->save();
            }

            // translation duplicate
            foreach ($product->productTranslations as $key => $translation) {
                $productTranslation                    = new ProductTranslation;
                $productTranslation->lang_key          = $translation->lang_key;
                $productTranslation->product_id        = $productNew->id;
                $productTranslation->name              = $translation->name;
                $productTranslation->short_description = $translation->short_description;
                $productTranslation->description   = $translation->description;
                $productTranslation->save();
            }

            // variation duplicate
            foreach ($product->variations()->withoutTrashed()->get() as $key => $variation) {
                $pVariation                 = new ProductVariation;
                $pVariation->product_id     = $productNew->id;
                $pVariation->sku            = $variation->sku;
                $pVariation->image          = $variation->image;
                $pVariation->code           = $variation->code;
                $pVariation->price          = $variation->price;
                $pVariation->discount_value = $variation->discount_value;
                $pVariation->discount_type  = $variation->discount_type;
                $pVariation->save();

                // variation combination duplicate
                foreach ($variation->combinations as $key => $combination) {
                    $pVariationComb                         = new ProductVariationCombination;
                    $pVariationComb->product_id             = $productNew->id;
                    $pVariationComb->product_variation_id   = $pVariation->id;
                    $pVariationComb->variation_id           = $combination->variation_id;
                    $pVariationComb->variation_value_id     = $combination->variation_value_id;
                    $pVariationComb->save();
                }
            }

            // todo:: duplicate stock but set 0

            $data = [
                'status'    => 200,
                'message'   => translate('Product has been duplicated successfully'),
                'result'    => [],
            ];
            return $data;
        } else {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }

    # delete data
    public static function destroy($id)
    {
        try {
            $product    = Product::shop()->where('id', $id)->first();

            ProductCategory::where('product_id', $product->id)->delete();
            ProductBadge::where('product_id', $product->id)->delete();
            ProductReview::where('product_id', $product->id)->delete();
            ProductTag::where('product_id', $product->id)->delete();
            ProductTax::where('product_id', $product->id)->delete();
            ProductTranslation::where('product_id', $product->id)->delete();
            ProductVariation::where('product_id', $product->id)->delete();
            ProductVariationCombination::where('product_id', $product->id)->delete();
            CampaignProduct::where('product_id', $product->id)->delete();
            $product->delete();

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Product deleted successfully'),
                'result'    => null
            ];

            return $data;
        } catch (\Throwable $th) {
            $data = [
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
