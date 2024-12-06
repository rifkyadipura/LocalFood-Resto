<?php

namespace App\Http\Controllers\Pemesanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Transaksi;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::where('status', 1)->get();
        return view('pemesanan.index', compact('menus'));
    }

    public function pilihMetode(Request $request)
    {
        $cart = json_decode($request->input('cart'), true);

        if (empty($cart)) {
            return redirect()->route('index.pemesanan')->withErrors(['message' => 'Keranjang kosong!']);
        }

        return view('pemesanan.pilih-metode', compact('cart'));
    }

    public function prosesPembayaran(Request $request)
    {
        $cart = json_decode($request->input('cart'), true); // Decode cart JSON
        $totalBayar = collect($cart)->sum('total'); // Hitung total harga
        $uangDibayar = $request->input('uang_dibayar') ?? $totalBayar; // Default sesuai total bayar
        $metode = $request->input('metode'); // Metode pembayaran

        // Validasi jika uang yang dibayar kurang
        if ($metode === 'Cash' && $uangDibayar < $totalBayar) {
            return redirect()->back()->withErrors(['message' => 'Uang yang dibayarkan kurang!']);
        }

        $uangKembalian = $uangDibayar - $totalBayar;

        // Simpan transaksi dan update stok menu
        foreach ($cart as $item) {
            Transaksi::create([
                'menu_id' => $item['menu_id'],
                'jumlah' => $item['quantity'],
                'total_harga' => $item['total'],
                'uang_dibayar' => $uangDibayar,
                'uang_kembalian' => $uangKembalian,
                'metode_pembayaran' => $metode,
            ]);

            $menu = Menu::find($item['menu_id']);
            if ($menu) {
                $menu->stok -= $item['quantity'];
                $menu->save();
            }
        }

        // Return ke halaman struk
        return view('pemesanan.struk', compact('cart', 'totalBayar', 'uangDibayar', 'uangKembalian', 'metode'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
