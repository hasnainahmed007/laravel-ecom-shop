<?php

namespace App\Http\Controllers;
use App\Models\Product;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(){
        $featuredProduct = Product::where('is_featured','Yes')
        ->orderBy('id','DESC')
        ->take(8)
        ->where('status',1)->get();

        $data['featuredProduct'] = $featuredProduct;
        

        $latestProduct = Product::orderBy('id','DESC')
        ->where('status',1)->take(8)->get();

        $data['latestProduct'] = $latestProduct;

        return view('front.home',$data);
    }
}
