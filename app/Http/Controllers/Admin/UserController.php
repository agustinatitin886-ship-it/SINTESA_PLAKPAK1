<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('nama_pengguna')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'nama_pengguna' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,walikelas,kepsek'],
            'kelas_binaan' => ['nullable', 'string', 'max:50'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna ' . $validated['nama_pengguna'] . ' berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'nama_pengguna' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:admin,walikelas,kepsek'],
            'kelas_binaan' => ['nullable', 'string', 'max:50'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna ' . $user->nama_pengguna . ' berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $nama = $user->nama_pengguna;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna ' . $nama . ' berhasil dihapus.');
    }
}

