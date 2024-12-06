<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = User::all();
        // dd($users);
        return view('users.index');
    }

    public function getData()
    {
        $users = User::select(['id', 'name', 'email', 'role', 'created_at'])
                    ->orderBy('created_at', 'desc');

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('actions', function ($user) {
                return '<a href="' . route('users.edit', $user->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                        <form action="' . route('users.destroy', $user->id) . '" method="POST" style="display:inline-block;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus user ini?\')"><i class="fas fa-trash"></i> Hapus</button>
                        </form>';
            })
            ->addColumn('role', function ($user) {
                return $user->role;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Ambil data user berdasarkan ID
        $user = User::findOrFail($id);

        // Kembalikan ke halaman edit dengan data user
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id, // Memastikan email unik kecuali pada user yang sedang diedit
            'role' => 'required|in:pegawai,admin',
        ]);

        // Ambil data user berdasarkan ID
        $user = User::findOrFail($id);

        // Update data user
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save(); // Simpan perubahan

        // Redirect kembali ke halaman users dengan pesan sukses
        return redirect()->route('users')->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Ambil data user berdasarkan ID
        $user = User::findOrFail($id);

        // Hapus user
        $user->delete();

        // Redirect ke halaman daftar users dengan pesan sukses
        return redirect()->route('users')->with('success', 'User berhasil dihapus!');
    }
}
