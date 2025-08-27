<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProductVariationCombination;
use App\Models\Variation;
use App\Models\VariationValueTranslation;
use App\Models\VariationValue;
use Illuminate\Http\Request;

class VariationValueController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_variation_values'])->only(['index']);
        $this->middleware(['permission:create_variation_values'])->only(['store']);
        $this->middleware(['permission:edit_variation_values'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_variation_values'])->only(['destroy']);
    }


    # Display a listing of the resource.
    public function index(Request $request, $variation_id)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $variation          = Variation::whereId((int)$variation_id)->first();
        $variationValues    = VariationValue::where('variation_id', $variation->id)->latest();
        if ($request->search != null) {
            $variationValues = $variationValues->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $variationValues = $variationValues->paginate($limit);
        return view('backend.admin.variation-values.index', compact('variation', 'variationValues', 'searchKey'));
    }

    # Store a newly created resource in storage.
    public function store(Request $request, $variationId)
    {
        $variationValue                  = new VariationValue;
        $variationValue->variation_id    = $variationId;
        $variationValue->name            = $request->name;
        $variationValue->color_code      = $request->color_code;
        $variationValue->thumbnail_image = $request->thumbnail_image;
        $variationValue->save();

        $variationValueTranslation = VariationValueTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'variation_value_id' => $variationValue->id]);
        $variationValueTranslation->name = $variationValue->name;
        $variationValueTranslation->save();

        flash(translate('Variation value has been added successfully'))->success();
        return redirect()->route('admin.variation-values.index', ['variation_id' => $variationId]);
    }

    # Show the form for editing the specified resource.
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.variation-values.index');
        }
        $variationValue = VariationValue::findOrFail($id);
        $variation      = $variationValue->variation;
        return view('backend.admin.variation-values.edit', compact('variation', 'variationValue', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $variationValue = VariationValue::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $variationValue->name = $request->name;
        }

        $variationValue->color_code      = $request->color_code;
        $variationValue->thumbnail_image = $request->thumbnail_image;

        $variationValueTranslation = VariationValueTranslation::firstOrNew(['lang_key' => $request->lang_key, 'variation_value_id' => $variationValue->id]);
        $variationValueTranslation->name = $request->name;

        $variationValue->save();
        $variationValueTranslation->save();
        flash(translate('Variation value has been updated successfully'))->success();
        return back();
    }

    # update status
    public function updateStatus(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $variationValue              = VariationValue::findOrFail($request->id);
        $variationValue->is_active   = $request->isActive;
        $variationValue->save();
        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $variationValue = VariationValue::findOrFail($id);

        try {
            ProductVariationCombination::where('variation_value_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $variationId = $variationValue->variation_id;
        $variationValue->delete();
        flash(translate('Variation value has been deleted successfully'))->success();
        return redirect()->route('admin.variation-values.index', ['variation_id' => $variationId]);
    }
}
