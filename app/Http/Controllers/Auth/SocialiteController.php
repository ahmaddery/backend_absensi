<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Import Str untuk menghasilkan string acak
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialiteController extends Controller
{
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callback(){
        $socialUser = Socialite::driver('google')->user();
        
        // Cek apakah pengguna sudah terdaftar
        $registeredUser = User::where("google_id", $socialUser->id)->first();

        if (!$registeredUser) {
            // Generate random password
            $randomPassword = Str::random(16);

            // Buat pengguna baru jika belum terdaftar
            $user = User::updateOrCreate([
                'google_id' => $socialUser->id,
            ], [
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => Hash::make($randomPassword), // Hash password yang dihasilkan secara acak
                'google_token' => $socialUser->token,
                'google_refresh_token' => $socialUser->refreshToken,
            ]);
         
            Auth::login($user);
        } else {
            // Login pengguna yang sudah terdaftar
            Auth::login($registeredUser);
        }
        
        // Pengecekan userType setelah login
        if (Auth::check()) {
            $usertype = Auth::user()->userType;
            
            if ($usertype == 'user') {
                return redirect()->route('dashboard');
            } else if ($usertype == 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->back();
            }
        }
        
        return redirect()->route('login'); // Redirect ke login jika tidak terautentikasi
    }
}
