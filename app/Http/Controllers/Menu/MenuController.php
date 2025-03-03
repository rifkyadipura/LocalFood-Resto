<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Category;
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
        $menu = MenuItem::with('category')
            ->select(['menu_item_id', 'menu_name', 'price', 'stock', 'status', 'category_id'])
            ->orderBy('created_at', 'desc');

        return DataTables::of($menu)
            ->addIndexColumn()
            ->addColumn('category', function ($menu) {
                return $menu->category ? $menu->category->category_name : '-';
            })
            ->addColumn('price', function ($menu) {
                return 'Rp ' . number_format($menu->price, 0, ',', '.');
            })
            ->addColumn('status', function ($menu) {
                return $menu->status == 1
                    ? '<span class="badge bg-success">Tersedia</span>'
                    : '<span class="badge bg-danger">Tidak Tersedia</span>';
            })
            ->addColumn('actions', function ($menu) {
                return '<div class="text-center">
                            <a href="' . route('menu.show', $menu->menu_item_id) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                            <a href="' . route('menu.edit', $menu->menu_item_id) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>'
                            . (in_array(auth()->user()->role, ['admin', 'Head Staff']) ? '
                            <form action="' . route('menu.destroy', $menu->menu_item_id) . '" method="POST" class="d-inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Apakah Anda yakin ingin menghapus menu ini?\')">
                                    <i class="fas fa-trash"></i> Delete
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
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Head Staff')) {
            $categories = Category::all();
            return view('menu.create', compact('categories'));
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
            'menu_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = preg_replace('/\s+/', '_', $request->menu_name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/menu'), $fileName);
            $filePath = 'uploads/menu/' . $fileName;
        }

        MenuItem::create([
            'menu_name' => $request->menu_name,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'image' => $filePath,
            'description' => $request->description,
            'created_by' => Auth::user()->user_id,
            'updated_by' => Auth::user()->user_id,
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
        $menu = MenuItem::with(['creator', 'updater', 'category'])->findOrFail($id);
        $menu->category_name = $menu->category->category_name ?? 'No category';
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
        $menu = MenuItem::findOrFail($id);
        $categories = Category::all();
        return view('menu.edit', compact('menu', 'categories'));
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
        $menu = MenuItem::findOrFail($id);

        if (auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'Head Staff')) {
            $request->validate([
                'menu_name' => 'string|max:255',
                'price' => 'numeric|min:0',
                'stock' => 'required|integer|min:0',
                'status' => 'boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'nullable|string',
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = preg_replace('/\s+/', '_', $request->menu_name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/menu'), $fileName);
                $filePath = 'uploads/menu/' . $fileName;

                if ($menu->image && file_exists(public_path($menu->image))) {
                    unlink(public_path($menu->image));
                } else {
                    Log::warning('Logika unlink tidak dijalankan karena foto tidak ditemukan atau kosong.');
                }

                $menu->image = $filePath;
            }

            $menu->update([
                'menu_name' => $request->menu_name,
                'price' => $request->price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'status' => ($request->stock > 0) ? 1 : 0,
                'description' => $request->description,
                'image' => $menu->image ?? null,
                'updated_by' => Auth::user()->user_id,
            ]);
        } elseif (auth()->user()->role === 'Cashier') {
            $request->validate([
                'stock' => 'required|integer|min:0',
            ]);

            $menu->update([
                'stock' => $request->stock,
                'status' => ($request->stock > 0) ? 1 : 0,
                'updated_by' => Auth::user()->user_id,
            ]);
        } else {
            abort(403, 'Unauthorized action.');
        }

        return redirect()->route('menu.index')->with('success', 'Menu Item berhasil dihapus!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = MenuItem::findOrFail($id);

        // Hapus file gambar jika ada
        if ($menu->image && file_exists(public_path($menu->image))) {
            unlink(public_path($menu->image));
        }

        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu item successfully deleted!');
    }
}
