<?php

namespace App\Http\Controllers\Pemesanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            $menus = Menu::all();
            return view('pemesanan.index', compact('menus'));
        } else {
            return redirect()->route('login')->withErrors(['message' => 'Silakan login terlebih dahulu untuk mengakses halaman ini!']);
        }
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
        $request->validate([
            'cart' => 'required|json',
            'uang_dibayar' => 'required|numeric|min:0',
            'metode' => 'required|string|in:Cash,QRIS',
        ]);

        $cart = json_decode($request->input('cart'), true);
        if (!$cart || count($cart) === 0) {
            return redirect()->back()->withErrors(['message' => 'Keranjang belanja kosong!']);
        }

        // Hitung subtotal, pajak, dan total setelah pajak
        $subtotal = collect($cart)->sum('total'); // Total harga sebelum pajak
        $tax = $subtotal * 0.1; // Pajak 10%
        $total_harga_pajak = $subtotal + $tax; // Total setelah pajak

        $uangDibayar = $request->input('uang_dibayar');
        $metode = $request->input('metode');
        $kodeTransaksi = Transaksi::generateKodeTransaksi();

        if ($metode === 'Cash' && $uangDibayar < $total_harga_pajak) {
            return redirect()->back()->withErrors(['message' => 'Uang yang dibayarkan kurang!']);
        }

        $uangKembalian = $uangDibayar - $total_harga_pajak;

        try {
            DB::beginTransaction();

            foreach ($cart as $item) {
                $menu = Menu::find($item['menu_id']);
                if (!$menu || $menu->stok < $item['quantity']) {
                    throw new \Exception('Menu tidak valid atau stok tidak mencukupi.');
                }

                Transaksi::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'menu_id' => $menu->menu_id,
                    'users_id' => auth()->user()->users_id,
                    'jumlah' => $item['quantity'],
                    'total_harga' => $item['total'], // Harga sebelum pajak
                    'total_harga_pajak' => $item['total'] + ($item['total'] * 0.1), // Harga setelah pajak
                    'uang_dibayar' => $uangDibayar,
                    'uang_kembalian' => $uangKembalian,
                    'metode_pembayaran' => $metode,
                ]);

                $menu->stok -= $item['quantity'];
                if ($menu->stok <= 0) {
                    $menu->status = 0;
                }
                $menu->save();
            }

            DB::commit();

            return view('pemesanan.struk', compact(
                'kodeTransaksi',
                'cart',
                'subtotal',
                'tax',
                'total_harga_pajak',
                'uangDibayar',
                'uangKembalian',
                'metode'
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
