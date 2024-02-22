<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function loginWithGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callBackFromGoogle()
    {
        try {
            $user = Socialite::driver('google')->user();

            $authUser = User::where('email', $user->email)->first();
            if ($authUser) {
                Auth::login($authUser);
                return redirect()->intended('/home');
            }
            
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_avatar' => $user->avatar,
                'password' => Hash::make($user->name . '@' . $user->getId()),
            ]);

            Auth::login($newUser);
            return redirect()->intended('/home');
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}
