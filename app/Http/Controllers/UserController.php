<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Tambahkan import ini
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(): View
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return view('users.index', compact('users'));
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create(): View
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('roles'));
    }
  
    /**
     * Menyimpan user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:Admin Master,Admin,Client'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        
        // Assign Spatie Role
        try {
            $user->assignRole($validated['role']);
        } catch (\Exception $e) {
            // Jika role belum diseder, abaikan agar tidak error fatal saat testing awal
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user): View
    {
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Mengupdate user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username,' . $user->id,
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
            ],
            'role' => ['required', 'in:Admin Master,Admin,Client'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['string', 'min:8'],
            ]);

            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        try {
            $user->syncRoles([$validated['role']]);
        } catch (\Exception $e) {
            // Abaikan jika belum di-seed
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Admin Master tidak boleh menghapus dirinya sendiri.
        if ($user->id === Auth::id()) { // <-- Diubah di sini
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
