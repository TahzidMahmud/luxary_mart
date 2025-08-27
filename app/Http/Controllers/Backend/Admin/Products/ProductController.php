<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_products'])->only(['index']);
        $this->middleware(['permission:create_products'])->only(['create', 'store']);
        $this->middleware(['permission:edit_products'])->only(['edit', 'update', 'updateStatus', 'duplicate']);
        $this->middleware(['permission:delete_products'])->only(['destroy']);
    }

    # resources list
    public function index(Request $request)
    {
        $response = ProductService::index($request);

        if ($response['status'] == 200) {
            return view('backend.admin.products.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # return view of create form
    public function create()
    {
        $response = ProductService::create();
        return view('backend.admin.products.create', $response['result']);
    }

    # generate variation combinations
    public function generateVariationCombinations(Request $request)
    {
        $response = ProductService::generateVariationCombinations($request);
        return view('backend.admin.products.new-variation-combinations', $response['result'])->render();
    }

    # get variation values to add new product
    public function getVariationValues(Request $request)
    {
        $response = ProductService::getVariationValues($request);
        return view('backend.admin.products.new-variation-values', $response['result']);
    }

    # new chosen variation
    public function getNewVariation(Request $request)
    {
        $response = ProductService::getNewVariation($request);
        $variations = $response['result']['variations'];
        $count      = $response['result']['count'];
        $chosenCounter      = $response['result']['chosenCounter'];

        if ($count == -1) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view' => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else if ($count > 0) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view'  => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else {
            return false;
        }
    }

    # add new data
    public function store(Request $request)
    {
        $response = ProductService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = ProductService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.admin.products.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.products.index');
    }

    # update data
    public function update(Request $request, $id)
    {
        $response = ProductService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # update status
    public function updateStatus(Request $request)
    {
        $response = ProductService::updateStatus($request);
        return $response;
    }

    # duplicate data
    public function duplicate($id)
    {
        $response = ProductService::duplicate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.products.index');
    }

    # delete data
    public function destroy($id)
    {
        $response = ProductService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('admin.products.index');
    }

    # real pictures
    public function pictures($id)
    {
        $product = Product::findOrFail((int) $id);
        return view('backend.admin.products.pictures', compact('product'));
    }
}
