<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategory;
use NumberFormatter;

class MenuController extends Controller
{
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
                ->select(['id', 'name', 'harga', 'stok', 'status', 'kategory_id'])
                ->orderBy('created_at', 'desc');

        return DataTables::of($menu)
            ->addIndexColumn()
            ->addColumn('kategori', function ($menu) {
                return $menu->kategori ? $menu->kategori->name : '-';
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
                            <a href="' . route('show.menu', $menu->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Lihat</a>
                            <a href="' . route('edit.menu', $menu->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>'
                            . (auth()->user()->role === 'admin' ? '
                            <form action="' . route('destroy.menu', $menu->id) . '" method="POST" class="d-inline">
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
        $kategories = Kategory::all(); // Mengambil semua kategori
        // dd("$kategories");
        return view('menu.create', compact('kategories'));
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
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        // Upload foto ke folder public/uploads/menu
        $filePath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            // Nama file berdasarkan nama menu
            $fileName = preg_replace('/\s+/', '_', $request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menu'), $fileName); // Pindahkan ke folder public/uploads/menu
            $filePath = 'uploads/menu/' . $fileName; // Path relatif untuk database
        }

        Menu::create([
            'name' => $request->name,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategory_id' => $request->kategory_id,
            'status' => $request->status,
            'foto' => $filePath,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('index.menu')->with('success', 'Menu berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = Menu::findOrFail($id);
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
        $kategories = Kategory::all(); // Mengambil semua kategori
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

        // Cek role pengguna
        if (auth()->user()->role === 'admin') {
            // Validasi untuk admin
            $request->validate([
                'name' => 'string|max:255',
                'harga' => 'numeric|min:0',
                'stok' => 'required|integer|min:0',
                'status' => 'boolean',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'deskripsi' => 'nullable|string',
            ]);

            // Proses upload foto baru jika ada
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = preg_replace('/\s+/', '_', $request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/menu'), $fileName);
                $filePath = 'uploads/menu/' . $fileName;

                // Hapus foto lama jika ada
                if ($menu->foto && file_exists(public_path($menu->foto))) {
                    unlink(public_path($menu->foto));
                }

                $menu->foto = $filePath;
            }

            // Update semua kolom (admin)
            $menu->update([
                'name' => $request->name,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'kategory_id' => $request->kategory_id,
                'status' => $request->status,
                'deskripsi' => $request->deskripsi,
                'foto' => $menu->foto ?? null,
            ]);
        } elseif (auth()->user()->role === 'pegawai') {
            // Validasi untuk pegawai
            $request->validate([
                'stok' => 'required|integer|min:0',
            ]);

            // Update hanya stok (pegawai)
            $menu->update([
                'stok' => $request->stok,
            ]);
        } else {
            // Jika role tidak sesuai
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('index.menu')->with('success', 'Menu berhasil diperbarui!');
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

        return redirect()->route('index.menu')->with('success', 'Menu berhasil dihapus!');
    }
}
