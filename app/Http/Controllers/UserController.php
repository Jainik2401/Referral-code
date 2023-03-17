<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Session;

class UserController extends Controller
{
    public function loadSignup()
    {
        return view('register');
    }
    public function Signup(Request $Request)
    {
        $Request->validate([
            'name' => 'required|min:2|max:50',
            'email' => 'required|min:2|max:50|email|unique:users',
            'password' => 'required|min:2|max:50'
        ]);

        $referral_code = Str::random(6);

        if (isset($Request->referral)) {
            $user = User::where('referral_code', $Request->referral)->get();
            if (count($user) > 0) {
                $user_id = User::insertGetId([
                    'name' => $Request->name,
                    'email' => $Request->email,
                    'password' => Hash::make($Request->password),
                    'referral_code' => $referral_code
                ]);
                Referral::insert([
                    'referral_code' => $Request->name,
                    'user_id' => $user_id,
                    'parent_user_id' => $user[0]['id']
                ]);
            } else {
                return redirect('/signin');
                // return back()->with('error', 'Enter valied referral code!');
            }
        } else {
            User::insert([
                'name' => $Request->name,
                'email' => $Request->email,
                'password' => Hash::make($Request->password),
                'referral_code' => $referral_code
            ]);
        }
        return redirect('/signin');
        // return back()->with('success', 'Your regisration has been successfull!');
    }
}
