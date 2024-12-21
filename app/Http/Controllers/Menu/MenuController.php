<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Kategory;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;

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
                            <a href="' . route('menu.show', $menu->id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Lihat</a>
                            <a href="' . route('menu.edit', $menu->id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>'
                            . (auth()->user()->role === 'admin' ? '
                            <form action="' . route('menu.destroy', $menu->id) . '" method="POST" class="d-inline">
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
        if (Auth::check() && Auth::user()->role === 'admin') {
            $kategories = Kategory::all();
            return view('menu.create', compact('kategories'));
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return view('errors.error', compact('title', 'message', 'redirectUrl'));
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
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = preg_replace('/\s+/', '_', $request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menu'), $fileName);
            $filePath = 'uploads/menu/' . $fileName;
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

        if (auth()->user()->role === 'admin') {
            $request->validate([
                'name' => 'string|max:255',
                'harga' => 'numeric|min:0',
                'stok' => 'required|integer|min:0',
                'status' => 'boolean',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'deskripsi' => 'nullable|string',
            ]);

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = preg_replace('/\s+/', '_', $request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/menu'), $fileName);
                $filePath = 'uploads/menu/' . $fileName;

                if ($menu->foto && file_exists(public_path($menu->foto))) {
                    unlink(public_path($menu->foto));
                }

                $menu->foto = $filePath;
            }

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
            $request->validate([
                'stok' => 'required|integer|min:0',
            ]);

            $menu->update([
                'stok' => $request->stok,
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
