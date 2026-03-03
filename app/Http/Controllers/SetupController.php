<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetupRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SetupController extends Controller
{
    public function index()
    {
        if (User::role('admin')->exists()) {
            return redirect()->route('login');
        }

        return Inertia::render('Setup/Index');
    }

    public function store(SetupRequest $request)
    {
        if (User::role('admin')->exists()) {
            return redirect()->route('login');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('admin');
        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }
}
