<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategory;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MenuController extends Controller
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
        return view('menu.index');
    }

    public function getData()
    {
        $menu = Menu::with('kategori')
                    ->select(['menu_id', 'nama_menu', 'harga', 'stok', 'status', 'kategory_id'])
                    ->orderBy('created_at', 'desc');

        return DataTables::of($menu)
            ->addIndexColumn()
            ->addColumn('kategori', function ($menu) {
                return $menu->kategori ? $menu->kategori->nama_kategory : '-';
            })
            ->addColumn('harga', function ($menu) {
                return 'Rp ' . number_format($menu->harga, 0, ',', '.');
            })
            ->addColumn('status', function ($menu) {
                return $menu->status == 1
                    ? '<span class="badge bg-success">Tersedia</span>'
                    : '<span class="badge bg-danger">Tidak Tersedia</span>';
            })
            ->addColumn('actions', function ($menu) {
                return '<div class="text-center">
                            <a href="' . route('menu.show', $menu->menu_id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Lihat</a>
                            <a href="' . route('menu.edit', $menu->menu_id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>'
                            . (in_array(auth()->user()->role, ['admin', 'Kepala Staf']) ? '
                            <form action="' . route('menu.destroy', $menu->menu_id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus menu ini?\')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>' : '') . '
                        </div>';
            })
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Kepala Staf')) {
            $kategories = Kategory::all();
            return view('menu.create', compact('kategories'));
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return response()->view('errors.error', compact('title', 'message', 'redirectUrl'), 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = preg_replace('/\s+/', '_', $request->nama_menu) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menu'), $fileName);
            $filePath = 'uploads/menu/' . $fileName;
        }

        Menu::create([
            'nama_menu' => $request->nama_menu,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategory_id' => $request->kategory_id,
            'status' => $request->status,
            'foto' => $filePath,
            'deskripsi' => $request->deskripsi,
            'dibuat_oleh' => Auth::user()->users_id,
            'diperbarui_oleh' => Auth::user()->users_id,
        ]);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::with(['pembuat', 'pengupdate', 'kategori'])->findOrFail($id);
        $menu->nama_kategory = $menu->kategori->nama_kategory ?? 'Tidak ada kategori';
        return view('menu.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $kategories = Kategory::all();
        return view('menu.edit', compact('menu', 'kategories'));
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
        $menu = Menu::findOrFail($id);

        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Kepala Staf')) {
            $request->validate([
                'nama_menu' => 'string|max:255',
                'harga' => 'numeric|min:0',
                'stok' => 'required|integer|min:0',
                'status' => 'boolean',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'deskripsi' => 'nullable|string',
            ]);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = preg_replace('/\s+/', '_', $request->nama_menu) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/menu'), $fileName);
                $filePath = 'uploads/menu/' . $fileName;

                if ($menu->foto && file_exists(public_path($menu->foto))) {
                    unlink(public_path($menu->foto));
                } else {
                    Log::warning('Logika unlink tidak dijalankan karena foto tidak ditemukan atau kosong.');
                }

                $menu->foto = $filePath;
            }

            $menu->update([
                'nama_menu' => $request->nama_menu,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'kategory_id' => $request->kategory_id,
                'status' => ($request->stok > 0) ? 1 : 0,
                'deskripsi' => $request->deskripsi,
                'foto' => $menu->foto ?? null,
                'diperbarui_oleh' => Auth::user()->users_id,
            ]);
        } elseif (auth()->user()->role === 'Kasir') {
            $request->validate([
                'stok' => 'required|integer|min:0',
            ]);

            $menu->update([
                'stok' => $request->stok,
                'status' => ($request->stok > 0) ? 1 : 0,
                'diperbarui_oleh' => Auth::user()->users_id,
            ]);
        } else {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        if ($menu->foto && file_exists(public_path($menu->foto))) {
            unlink(public_path($menu->foto));
        }
        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}
