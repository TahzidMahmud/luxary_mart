<?php

namespace App\Http\Controllers\Backend\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_customers'])->only(['index']);
        $this->middleware(['permission:edit_customers'])->only(['toggleBan']);
        $this->middleware(['permission:delete_customers'])->only(['destroy']);
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $customers = User::customers();
        if ($request->search != null) {
            try {
                Notification::where('link_info', $request->search)->where('type', 'customer-registration')->update([
                    'is_read' => 1
                ]);
            } catch (\Throwable $th) {
            }
            $customers = $customers->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        // sort by
        if ($request->sortBy) {
            switch ($request->sortBy) {
                case 'amountHighToLow':
                    $customers = $customers->with(['orders' => function ($query) {
                        $query->select('user_id', \DB::raw('sum(total_amount) as total_amount'))
                            ->groupBy('user_id');
                    }])->orderByDesc(\DB::raw('(select sum(total_amount) from orders where orders.user_id = users.id)'));
                    break;
                case 'amountLowToHigh':
                    $customers = $customers->with(['orders' => function ($query) {
                        $query->select('user_id', \DB::raw('sum(total_amount) as total_amount'))
                            ->groupBy('user_id');
                    }])->orderBy(\DB::raw('(select sum(total_amount) from orders where orders.user_id = users.id)'));
                    break;
                case 'qtyHighToLow':
                    $customers = $customers->withCount('orders')
                        ->orderByDesc('orders_count');
                    break;
                case 'qtyLowToHigh':
                    $customers = $customers->withCount('orders')
                        ->orderBy('orders_count');
                    break;
                default:
                    $customers = $customers->latest();
                    break;
            }
        }

        $customers = $customers->paginate($limit);
        return view('backend.admin.customers.index', compact('customers', 'searchKey'));
    }

    # show
    public function show($id)
    {
        $customer = User::findOrFail($id);
        return view('backend.admin.customers.show', compact('customer'));
    }

    # toggle Ban 
    public function toggleBan(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $customer = User::findOrFail($request->id);
        $customer->is_banned = !$customer->is_banned;
        $customer->save();

        return $data;
    }


    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $customer = User::findOrFail($id);

        try {
            Cart::where('user_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }

        $customer->delete();
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('admin.customers.index');
    }
}
