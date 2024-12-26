<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
        date_default_timezone_set("Asia/Jakarta");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Kepala Staf')) {
            return view('users.index');
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return view('errors.error', compact('title', 'message', 'redirectUrl'));
        }
    }

    public function getData()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $query = User::select(['users_id', 'nama_lengkap', 'email', 'role', 'created_at']);
            if ($user->role === 'Kepala Staf') {
                $query->where('role', '!=', 'admin');
            }
            $users = $query->orderBy('users_id', 'desc');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('actions', function ($user) {
                    $editButton = '<a href="' . route('users.edit', $user->users_id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>';
                    $deleteButton = '<form action="' . route('users.destroy', $user->users_id) . '" method="POST" style="display:inline-block;">
                                        ' . csrf_field() . method_field('DELETE') . '
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus user ini?\')"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>';

                    return $editButton . ' ' . $deleteButton;
                })
                ->addColumn('role', function ($user) {
                    return $user->role;
                })
                ->rawColumns(['actions'])
                ->make(true);

        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Kepala Staf')) {
            $user = User::find($id);

            if (!$user) {
                return redirect()->route('users.index')->with('error', 'User tidak ditemukan!');
            }

            return view('users.edit', compact('user'));
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return view('errors.error', compact('title', 'message', 'redirectUrl'));
        }
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
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',users_id',
            'role' => 'required|in:Kepala Staf,Kasir',
        ]);

        $user = User::findOrFail($id);

        $user->nama_lengkap = $request->nama_lengkap;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->save();

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}
