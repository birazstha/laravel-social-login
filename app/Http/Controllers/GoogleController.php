<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
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

    public function reactGoogleLogin(Request $request)
    {
        $googleToken = $request->input('token');

        try {
            $providerUser = Socialite::driver('google')->userFromToken('ya29.a0AcM612y1TsT4xw8MLBuai2rC_qQ7O8Z-JOl41D45m560HnTMEDLes87DYmcDzGEhnMB-e1hiXRemK2UVZxv663im9_KwMd9ikC5zdEKo-KEV9vCT5NXdI0F-cvEPLQE0p7JfweZjwYfSTum_9Nu_HdKShyakcs4lDKcaCgYKAYwSARESFQHGX2MinyKM3e2ZFqkT-AgsuZ6xdA0170');
            dd($providerUser);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
