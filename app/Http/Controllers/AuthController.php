<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Hash;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login() {
        return view('front.account.login');
    }

    public function register() {
        return view('front.account.register');
    }

    public function processRegeister(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'

        ]);

        if($validator->passes()) {
            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success','You have been registerd successfully');
            return response()->json([
                'status' => true,
                'message' => 'Registerd successfully'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(),[
          'email' => 'required|email',
          'password' => 'required'

        ]);

        if($validator->passes()) {
            
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
               
                if(session()->has('url.intended')) {
                   return redirect(session()->get('url.intended'));
                }

                return redirect()->route('account.profile');
            } else {
                session()->flash('error', 'Either email/password is incorrect');
                return redirect()->route('account.login')
                ->withInput($request->only('email'));
            }

        } else {
            return redirect()->route('account.login')->withErrors($validator)
            ->withInput($request->only('email'));
        }
    }


    public function profile() {
        return view('front.account.profile');
    }


    public function logout() {
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success','You have successfully logged out!');
        
    }

    
}
