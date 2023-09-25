<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\ShippingCharge;
use Validator;

class ShippingController extends Controller
{
    public function create() {
        $countries = Country::get();
        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                           ->leftJoin('countries','countries.id','shipping_charges.country_id')->get();

        return view('admin.shipping.create',[
            'countries' => $countries,
            'shippingCharges' => $shippingCharges

        ]);
    }

    public function store(Request $request) {
       $validator = Validator::make($request->all(),[
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if ($validator->passes()) {
            $count = ShippingCharge::where('country_id',$request->country)->count();
            if($count > 0) {
                session()->flash('error', 'Shipping already exist');
                return response()->json([
                    'status' => true
                ]);
            }

            $shipping = new ShippingCharge;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();

            session()->flash('success','Shipping added successfully');
            return response()->json([
                'status' => true,
                'message'=> 'Shipping added successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors'=> $validator->errors()
            ]);
        }
    }

    public function edit($id) {

        $shippingCharge = ShippingCharge::find($id);
        $countries = Country::get();

        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;

        return view('admin.shipping.edit',$data);
    }


        public function update($id,Request $request) {

            $shipping = ShippingCharge::find($id);

            $validator = Validator::make($request->all(),[
                'country' => 'required',
                'amount' => 'required|numeric'
            ]);
    
            if ($validator->passes()) {

                if($shipping == null) {
                    session()->flash('error','Shipping not found');
                    return response()->json([
                        'status' => true
                    ]);
                }

                $shipping->country_id = $request->country;
                $shipping->amount = $request->amount;
                $shipping->save();
    
                session()->flash('success','Shipping update successfully');
                return response()->json([
                    'status' => true,
                    'message'=> 'Shipping update successfully'
                ]);
    
            } else {
                return response()->json([
                    'status' => false,
                    'errors'=> $validator->errors()
                ]);
            }
        }

        public function destroy($id) {
            $shippingCharge = ShippingCharge::find($id);
            if($shippingCharge == null) {
                session()->flash('error','Shipping not found');
                return response()->json([
                    'status' => true
                ]);
            }
            $shippingCharge->delete();

            session()->flash('success','Shipping deleted successfully');
            return response()->json([
                'status' => true
            ]);
        }
}
