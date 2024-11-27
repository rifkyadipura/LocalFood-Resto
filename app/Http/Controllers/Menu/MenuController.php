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

        // Simpan data ke database
        Menu::create([
            'name' => $request->name,
            'stok' => $request->stok,
            'status' => $request->status,
            'foto' => $filePath, // Simpan path relatif
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
