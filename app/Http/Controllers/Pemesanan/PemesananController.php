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
        $menus = Menu::where('status', 1)->get();

        return view('pemesanan.index', [
            'menus' => $menus,
            'cart' => $cart,
            'showPaymentModal' => true
        ]);
    }

    public function prosesPembayaran(Request $request)
    {
        $cart = json_decode($request->input('cart'), true); // Decode cart JSON
        $totalBayar = collect($cart)->sum('total'); // Hitung total harga
        $uangDibayar = $request->input('uang_dibayar') ?? $totalBayar; // Default sesuai total bayar
        $metode = $request->input('metode'); // Metode pembayaran

        // Buat kode transaksi unik menggunakan model
        $kodeTransaksi = Transaksi::generateKodeTransaksi();

        // Validasi jika uang yang dibayar kurang
        if ($metode === 'Cash' && $uangDibayar < $totalBayar) {
            return redirect()->back()->withErrors(['message' => 'Uang yang dibayarkan kurang!']);
        }

        $uangKembalian = $uangDibayar - $totalBayar;

        foreach ($cart as $item) {
            Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
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

        return view('pemesanan.struk', compact('kodeTransaksi', 'cart', 'totalBayar', 'uangDibayar', 'uangKembalian', 'metode'));
    }
}
