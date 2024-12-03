<?php

namespace App\Http\Controllers\Menu;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menu = Menu::all();
        return view('menu.index', compact('menu'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menu.create');
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
        return view('menu.edit', compact('menu'));
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
            'name' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deskripsi' => 'nullable|string',
        ]);

        $menu = Menu::findOrFail($id);

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

        $menu->update([
            'name' => $request->name,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
        ]);

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
