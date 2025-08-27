<?php

namespace App\Http\Controllers\Backend\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_purchase_orders'])->only(['index']);
        $this->middleware(['permission:create_purchase_orders'])->only(['create', 'store']);
        $this->middleware(['permission:edit_purchase_orders'])->only(['edit', 'update', 'destroy']);
        $this->middleware(['permission:view_return_purchase_orders'])->only(['returnIndex']);
        $this->middleware(['permission:create_return_purchase_orders'])->only(['returnCreate', 'returnStore']);
    }


    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $response = PurchaseOrderService::index($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.purchase-orders.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # Show the form for creating a new resource.
    public function create()
    {
        $response = PurchaseOrderService::create();
        return view('backend.admin.inventory.purchase-orders.create', $response['result']);
    }

    # Show the form for editing the specified resource.
    public function getProducts(Request $request)
    {
        $response = PurchaseOrderService::getProducts($request);
        return view('backend.admin.inventory.purchase-orders.product-search-results', $response['result']);
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $response = PurchaseOrderService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = PurchaseOrderService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.admin.inventory.purchase-orders.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.purchase-orders.index');
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $response = PurchaseOrderService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # cancel purchase
    public function destroy($id)
    {
        $response = PurchaseOrderService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.purchase-orders.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.purchase-orders.index');
    }

    # Display a listing of the resource.
    public function returnIndex(Request $request, $limit = 15)
    {
        $response = PurchaseOrderService::returnIndex($request, $limit);

        if ($response['status'] == 200) {
            return view('backend.admin.inventory.purchase-orders.return', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # Show the form for returning a new resource.
    public function returnCreate(Request $request, $id)
    {
        $response = PurchaseOrderService::returnCreate($request, $id);
        return view('backend.admin.inventory.purchase-orders.edit', $response['result']);
    }

    # Show the form for returning a new resource.
    public function returnStore(Request $request, $id)
    {
        $response = PurchaseOrderService::returnStore($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.purchase-return.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.purchase-return.index');
    }
}
