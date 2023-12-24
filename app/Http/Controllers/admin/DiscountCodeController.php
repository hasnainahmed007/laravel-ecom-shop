<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\DiscountCoupon;
use Validator;

class DiscountCodeController extends Controller
{
    public function index(Request $request) {
        $discountCoupons = DiscountCoupon::latest();
        if(!empty($request->get('keyword'))){
            $discountCoupons = $discountCoupons->where('name','like','%'.$request->get('keyword').'%');
        }

        $discountCoupons = $discountCoupons->paginate(10);
        // dd($categories);
        $data['discountCoupons'] = $discountCoupons;
        return view('admin.coupon.list', compact('discountCoupons'));
    }

    public function create() {

     return view('admin.coupon.create');

    }


    public function store(Request $request) {
       $validator = Validator::make($request->all(),[
            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required'

        ]);

        if($validator->passes()) {

            if(!empty($request->starts_at)) {
                $now = Carbon::now();
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if($startAt->lte($now) == true) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['starts_at' => 'Start date can not be less than current date time']
                    ]);
                }
            }

            if(!empty($request->starts_at) && !empty($request->expire_at)) {
                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->expire_at);
                $startAt = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if($expireAt->gt($startAt) == false) {
                    return response()->json([
                        'status' => false,
                        'errors' => ['expire_at' => 'Expires date must be greater start date time']
                    ]);
                }
            }

            $discountCode = new DiscountCoupon;
            $discountCode->code = $request->code;
            $discountCode->name = $request->name;
            $discountCode->description = $request->description;
            $discountCode->max_uses = $request->max_uses;
            $discountCode->max_uses_user = $request->max_uses_user;
            $discountCode->type = $request->type;
            $discountCode->discount_amount = $request->discount_amount;
            $discountCode->min_amount = $request->min_amount;
            $discountCode->status = $request->status;
            $discountCode->starts_at = $request->starts_at;
            $discountCode->expire_at = $request->expire_at;
            $discountCode->save();

            $message = 'Discount coupon added successfully.';
            session()->flash('success', $message);

             return response()->json([
                'status' => true,
                'message' =>  $message
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit(Request $request, $id) {
        $coupon = DiscountCoupon::find($id);

        if($coupon == null) {
            session()->flash('error','Record not found');
            return redirect()->route('coupons.index');
        }
        return view('admin.coupon.edit',compact('coupon'));
        
    }

    public function update() {
        
    }


    public function destroy() {
        
    }

}
