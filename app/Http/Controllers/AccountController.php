<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function dashboard()
    {
        return view('account.dashboard');
    }


    public function editProfile()
    {
        return view('account.profile');
    }


    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);


        $user->update($data);


        return redirect()
            ->route('account.profile')
            ->with('success', 'Perfil actualizado correctamente.');
    }


    public function orders(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->latest()
            ->get();


        return view('account.orders', compact('orders'));
    }
}
