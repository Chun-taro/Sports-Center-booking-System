<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $role = $request->input('role');
        $email = strtolower($request->input('email', ''));

        if ($role === 'admin' || str_contains($email, 'admin') || str_contains($email, 'staff')) {
            session(['static_role' => 'admin']);
            $user = new User([
                'id' => 1,
                'name' => 'Apex Admin',
                'email' => $email ?: 'admin@apexsports.com',
                'phone' => '+1 (555) 019-2834',
                'role' => 'admin',
                'is_active' => true,
            ]);
            $user->exists = true;
            Auth::login($user);
            return redirect()->route('admin.dashboard')->with('success', 'Logged in to Admin Management Portal.');
        }

        session(['static_role' => 'customer']);
        $user = new User([
            'id' => 2,
            'name' => 'Alex Johnson',
            'email' => $email ?: 'customer@apexsports.com',
            'phone' => '+1 (555) 019-9876',
            'role' => 'customer',
            'is_active' => true,
        ]);
        $user->exists = true;
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Logged in as Customer.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $name = $request->input('name', 'New Member');
        $email = $request->input('email', 'member@apexsports.com');

        session(['static_role' => 'customer']);
        $user = new User([
            'id' => rand(10, 999),
            'name' => $name,
            'email' => $email,
            'phone' => $request->input('phone', '+1 555 0192'),
            'role' => 'customer',
            'is_active' => true,
        ]);
        $user->exists = true;
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Welcome to ApexSports! Account registered successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->forget('static_role');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }

    public function profile()
    {
        $user = Auth::user();
        if (! $user) {
            $user = new User([
                'id' => 2,
                'name' => 'Alex Johnson',
                'email' => 'customer@apexsports.com',
                'phone' => '+1 (555) 019-9876',
                'role' => 'customer',
                'is_active' => true,
            ]);
        }

        return view('customer.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        return back()->with('success', 'Profile updated successfully.');
    }
}
