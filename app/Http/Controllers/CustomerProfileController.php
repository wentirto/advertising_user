<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    /**
     * Tampilkan halaman profile customer.
     * Karena belum ada login/auth, ambil customer pertama (id=1) sebagai demo.
     */
    public function show(Request $request)
    {
        $id = $request->query('id', 1);
        $customer = Customer::with('playlists')->findOrFail($id);

        // Statistik singkat buat header profile
        $totalPlaylist = $customer->playlists->count();

        return view('customer.profile', [
            'customer'      => $customer,
            'totalPlaylist' => $totalPlaylist,
        ]);
    }

    /**
     * Update data profile customer.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name'   => 'required|string|max:150',
            'last_name'    => 'nullable|string|max:150',
            'job_position' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:500',
            'address'      => 'nullable|string|max:500',
            'city'         => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:25',
            'email'        => 'required|email|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->only([
            'first_name',
            'last_name',
            'job_position',
            'company_name',
            'address',
            'city',
            'phone_number',
            'email',
        ]));

        return redirect()
            ->route('user.profile', ['id' => $id])
            ->with('success', 'Profile berhasil diperbarui!');
    }
}
