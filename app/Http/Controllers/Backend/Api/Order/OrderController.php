<?php

namespace App\Http\Controllers\Backend\Api\Order;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Order;
use App\Models\Country;
use App\Http\Resources\OrderResource;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;

        $this->middleware(['permission:view_orders'])->only(['index', 'downloadInvoice']);
        $this->middleware(['permission:manage_orders'])->only(['show', 'updateOrderTracking', 'storeOrderUpdates', 'deleteOrderUpdate', 'updatePaymentStatus', 'updateDeliveryStatus', 'updateOrderAddress']);
    }

    # get all resources
    public function index(Request $request)
    {

        $limit  = $request->limit ?? perPage();

        $orders = apiUser()->orders()->latest();


        $orders = $this->orderService->index($request);
        // dd($orders["result"]["orders"]);
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
            'countries'     => Country::where('is_active', 1)->get(),
        ];

        // $view = view('backend.admin.pos.index', compact('settings'));
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => OrderResource::collection($orders["result"]["orders"])->response()->getData(true)
            ];
    }

    public function updateOrderStatus(Request $request){
         $response = $this->orderService->updateDeliveryStatus($request);
        if ($response['status'] == 200) {
            return $response;
        }
        return [
                'success'   => false,
                'status'    => 200,
                'message'   => 'Something wnet wrong',
            ];;
    }
}
