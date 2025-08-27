<?php

namespace App\Http\Controllers\Backend\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view_cities'])->only(['index']);
        $this->middleware(['permission:create_cities'])->only(['store']);
        $this->middleware(['permission:edit_cities'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_cities'])->only(['destroy']);
    }

    # get all data
    public function index(Request $request)
    {
        $searchKey = null;
        $stateId   = null;
        $limit     = $request->limit ?? perPage();
        $cities    = City::orderBy('is_active', 'desc');

        if ($request->search != null) {
            $cities     = $cities->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }

        if ($request->stateId != null) {
            $cities->where('state_id', $request->stateId);
            $stateId = $request->stateId;
        }

        $cities = $cities->paginate($limit);
        $states = State::isActive()->get();

        return view('backend.admin.shipping.cities.index', compact('cities', 'searchKey', 'stateId', 'states'));
    }

    # add new data
    public function store(Request $request)
    {
        $city = new City;
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->is_active   = 1;
        $city->save();
        flash(translate('State has been added successfully'))->success();
        return redirect()->route('admin.cities.index');
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $lang_key   = $request->lang_key;
        $states     = State::isActive()->get();
        $city       = City::findOrFail($id);
        return view('backend.admin.shipping.cities.edit', compact('states', 'city', 'lang_key'));
    }

    # update state
    public function update(Request $request, $id)
    {
        $city              = City::findOrFail((int) $id);
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->save();
        flash(translate('State has been updated successfully'))->success();
        return redirect()->route('admin.cities.index');
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
        $city = City::findOrFail($request->id);
        $city->is_active = !$city->is_active;
        if ($city->save()) {
            // Cache::forget('countries'); 
            Area::where('city_id', $city->id)->update([
                'is_active' => $city->is_active
            ]);
        }

        return $data;
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();
        flash(translate('State has been deleted successfully'))->success();
        return redirect()->route('admin.cities.index');
    }
}
