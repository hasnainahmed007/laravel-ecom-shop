<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cart;
use Auth;
use App\Models\Product;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CustomerAddress;
use App\Models\ProductImage;
use Validator;


class CartController extends Controller
{
    public function addToCart(Request $request) {
        $product = Product::with('product_images')->find($request->id);

        if($product == null) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
        }
        if (cart::count() > 0) {
            $cartContent = Cart::content();
            $productAlreadyExist = false;
            
            foreach($cartContent as $item) {
                if($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }
            
            if($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
                session()->flash('success',$message);
            } else {
                $status = false;
                $message = $product->title.'already added in cart';
            }

        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['ProductImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            
                $status = true;
                $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
                session()->flash('success',$message);
            
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
        
    }
 
    public function cart() {
        $cartContent = Cart::content();
        //dd($cartContent);
         $data['cartContent'] = $cartContent;
        return view('front.cart',$data);
    }

    public function updateCart(Request $request) {
       $rowId = $request->rowId;
       $qty = $request->qty;

       $itemInfo = Cart::get($rowId);
       $product = Product::find($itemInfo->id);

       //check qty available in stock
       if($product->track_qty == 'Yes') {
        if($qty <= $product->qty ) {
            Cart::update($rowId, $qty);
            $message = 'Cart Update Successfully';
            $status = true;
            session()->flash('success',$message);
        } else {
            $message = 'Requested qty('.$qty.') not available in stock';
            $status = false;
            session()->flash('error',$message);
        }
       } else{
          Cart::update($rowId, $qty);
          $message = 'Cart Update Successfully';
          $status = true;
          session()->flash('success',$message);
       }
    
       return response()->json([
        'status' => $status,
        'message' => $message
       ]); 
    }

    public function deleteItem(Request $request) {
     
        $itemInfo = Cart::get($request->rowId);

        if($itemInfo == null) {
            $erroMessage = 'Item not found';
            session()->flash('error',$erroMessage);
            return response()->json([
                'status' => false,
                'message' => $erroMessage
            ]);
        }

        Cart::remove($request->rowId);

        $successMessage = 'Item removed from cart successfully';
        session()->flash('success',$successMessage);
        return response()->json([
            'status' => true,
            'message' => $successMessage
        ]);
    }


    public function checkout() {
        if(Cart::count() == 0) {
            return redirect()->route('front.cart');
        } 

        if(Auth::check() == false) {

            if(!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('account.login');
        }

        $customerAddress = CustomerAddress::where('user_id',Auth::user()->id);

        session()->forget('url.intended');

        $countries = Country::orderBy('name','ASC')->get();

        //calculate shipping here
        if($customerAddress != '') {
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();

            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach(Cart::content() as $item) {
                $totalQty+= $item->qty;
            }

            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = Cart::subtotal(2,'.','')+$totalShippingCharge;
        } else {
            $grandTotal = Cart::subtotal(2,'.','');
            $totalShippingCharge = 0;
        }
        

        return view('front.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'grandTotal' => $grandTotal
        ]);
    }


    public function processCheckout(Request $request) {
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:5',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:30',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required'

        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Please fix the error',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // step-2 save user address
        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],

            [
              'user_id' => $user->id,
              'first_name' => $request->first_name,
              'last_name' => $request->last_name,
              'email' => $request->email,
              'mobile' => $request->mobile,
              'country_id' => $request->country,
              'address' => $request->address,
              'apartment' => $request->apartment,
              'city' => $request->city,
              'state' => $request->state,
              'zip' => $request->zip

            ]
            );

            //step-3 store data in orders table

            if($request->payment_method == 'cod') {
                $shipping = 0;
                $discount = 0;
                $subTotal = Cart::subtotal(2,'.','');

                $shippingInfo = ShippingCharge::where('country_id',$request->country)->first();

                $totalQty = 0;
                foreach(Cart::content() as $item) {
                    $totalQty+= $item->qty;
                }

                //calculate shipping charge
                if($shippingInfo != null) {
                    $shipping = $totalQty*$shippingInfo->amount;
    
                    $grandTotal = $subTotal+$shipping;
    
                } else {
                    $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
    
                    $shipping = $totalQty*$shippingInfo->amount;
    
                    $grandTotal = $subTotal+$shipping;
                  
                }

                $order = new Order;
                $order->subtotal = $subTotal;
                $order->shipping = $shipping;
                $order->grand_total = $grandTotal;

                $order->user_id = $user->id;
                $order->first_name = $request->first_name;
                $order->last_name = $request->last_name;
                $order->email = $request->email;
                $order->mobile = $request->mobile;
                $order->address = $request->address;
                $order->apartment = $request->apartment;
                $order->state = $request->state;
                $order->city = $request->city;
                $order->zip = $request->zip;
                $order->notes = $request->notes;
                $order->country_id = $request->country;
                $order->save();

                // step-4 store order item in database
                foreach(Cart::content() as $item) {
                    $orderItem = new OrderItem;
                    $orderItem->product_id = $item->id;
                    $orderItem->order_id = $order->id;
                    $orderItem->name = $item->name;
                    $orderItem->qty = $item->qty;
                    $orderItem->price = $item->price;
                    $orderItem->total = $item->price*$item->qty;
                    $orderItem->save();

                    session()->flash('success','You have successfully placed your order');
                    Cart::destroy();
                    return response()->json([
                        'message' => 'Order save successfully',
                        'orderId' => $order->id,
                        'status' => true,
                        
                    ]);

                }
            } else {

            }
    }

    public function thankyou($id) {
        return view('front.thanks',[
            'id' => $id
        ]);
    }

    public function getOrderSummery(Request $request) {

        $subTotal = Cart::subtotal(2,'.','');

        if ($request->country_id > 0) {
            $shippingInfo = ShippingCharge::where('country_id',$request->country_id)->first();
            
            $totalQty = 0;
            foreach(Cart::content() as $item) {
                $totalQty+= $item->qty;
            }

            if($shippingInfo != null) {
                $shippingCharge = $totalQty*$shippingInfo->amount;

                $grandTotal = $subTotal+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);

            } else {
                $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();

                $shippingCharge = $totalQty*$shippingInfo->amount;

                $grandTotal = $subTotal+$shippingCharge;

                return response()->json([
                    'status' => true,
                    'grandTotal' => number_format($grandTotal,2),
                    'shippingCharge' => number_format($shippingCharge,2)
                ]);
            }

        } else {
            return response()->json([
                'status' => true,
                'grandTotal' => number_format($subTotal,2),
                'shippingCharge' => number_format(0,2)
            ]);
        }
    }
}
