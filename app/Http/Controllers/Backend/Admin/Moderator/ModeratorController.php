<?php

namespace App\Http\Controllers\Backend\Admin\Moderator;

use App\Http\Controllers\Controller;
use App\Models\ModeratorCommission;
use App\Models\ModeratorPayout;
use App\Models\User;
use Illuminate\Http\Request;

class ModeratorController extends Controller
{

    # constructor
    public function __construct()
    {
        // $this->middleware(['permission:general_settings'])->only('generalSetting');
    }

    public function index(Request $request)
    {
        $moderators     = User::role('Moderator')->get();
        $searchKey      = null;
        $limit          = $request->limit ?? 15;
        $user           = user();
        $commissions    = $user->hasRole('Moderator') ?  ModeratorCommission::where('user_id', $user->id)->latest() : ModeratorCommission::latest();

        if ($request->search != null) {
            $commissions = $commissions->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        // filter by status
        if ($request->status != null) {
            $commissions->where('status', $request->status);
        }


        if ($request->userId != null) {
            $commissions->where('user_id', $request->userId);
        }


        $commissions = $commissions->paginate($limit);
        return view('backend.admin.moderators.commissions', compact('commissions', 'searchKey', 'user'));
    }


    public function payouts(Request $request)
    {
        if (user()->user_type == "admin") {
            $commission = ModeratorCommission::where('id', $request->id)->first();
            return view('backend.admin.moderators.payout', compact('commission'));
        }
    }

    public function storePayouts(Request $request)
    {
        if (user()->user_type == "admin") {
            $commission = ModeratorCommission::where('id', $request->id)->first();
            if ($commission && $commission->status != "paid") {
                $payment = new ModeratorPayout;
                $payment->user_id           = $commission->user_id;
                $payment->paid_by_user_id   = user()->id;
                $payment->paid_amount       = $request->paid_amount;
                $payment->payment_method    = $request->payment_method;
                $payment->status            = $request->paid_amount == $commission->due_amount ? "full" : "partial";
                $payment->save();

                $commission->due_amount         -= $request->paid_amount;
                $commission->status             = $commission->due_amount > 0 ? "due" : "paid";
                $commission->save();
                flash(translate('Payment has been made successfully'))->success();
            }

            return redirect()->route('admin.moderators.index');
        }
    }



    public function payoutList(Request $request)
    {
        $moderators     = User::role('Moderator')->get();
        $searchKey      = null;
        $limit          = $request->limit ?? 15;
        $user           = user();
        $payouts        = $user->hasRole('Moderator') ?  ModeratorPayout::where('user_id', $user->id)->latest() : ModeratorPayout::latest();

        if ($request->search != null) {
            $payouts = $payouts->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        // filter by status
        if ($request->status != null) {
            $payouts->where('status', $request->status);
        }

        $payouts = $payouts->paginate($limit);
        return view('backend.admin.moderators.payout-list', compact('payouts', 'searchKey', 'user'));
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $commission = ModeratorCommission::findOrFail((int)$id);


        $commission->delete();
        flash(translate('Commission has been deleted successfully'))->success();
        return redirect()->route('admin.moderators.index');
    }
}
