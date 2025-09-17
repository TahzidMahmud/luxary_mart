<?php

namespace App\Http\Controllers\Backend\Api\Order;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use App\Models\Order;
use App\Models\Country;
use App\Models\OrderItem;
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

    public function show(Request $request,$id){
        $order = Order::with(['orderItems.productVariation','orderItems.productVariation.product' => function ($query) {
            $query->select('id', 'name');
        }])->find((int) $id);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'order' => $order,
            ],
        ];
        return $data;
    }
    public function update_order(Request $request){
        $order_items= $request->orderItems;
        // dd($order_items);
        try{
            foreach ($order_items as $key => $value) {
                if($value['removed']){
                    OrderItem::destroy($value['id']);
                }else{
                    $order_item = OrderItem::findOrFail($value['id']);
                    $order_item->qty= $value['qty'];
                    $order_item->total_price = ($value['qty'] * $order_item->unit_price);
                    $order_item->save();
                }
            }
        }catch(\Exception $e){
            dd($e);
             $data = [
                'status'    => 500,
                'message'   => 'Something Went Wrong',
            ];
            return $data;
        }

        $data = [
           'status'    => 200,
            'message'   => 'Upated Successfully..!!',
        ];
        return $data;
    }
}
