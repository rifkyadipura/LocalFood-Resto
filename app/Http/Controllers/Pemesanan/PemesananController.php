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
            $menus = Menu::select('menu_id', 'nama_menu', 'stok', 'status', 'harga', 'foto', 'deskripsi', 'kategory_id')
                        ->where('status', 1)->get();
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
        // Validasi Input
        $request->validate([
            'cart' => 'required|json', // Cart harus dalam format JSON
            'uang_dibayar' => 'required|numeric|min:0',
            'metode' => 'required|string|in:Cash,QRIS',
        ]);

        // Decode cart JSON
        $cart = json_decode($request->input('cart'), true);
        if (!$cart || count($cart) === 0) {
            return redirect()->back()->withErrors(['message' => 'Keranjang belanja kosong!']);
        }

        // Hitung total harga
        $totalBayar = collect($cart)->sum('total');
        $uangDibayar = $request->input('uang_dibayar') ?? $totalBayar;
        $metode = $request->input('metode');

        // Buat kode transaksi unik
        $kodeTransaksi = Transaksi::generateKodeTransaksi();

        // Validasi jika uang yang dibayar kurang
        if ($metode === 'Cash' && $uangDibayar < $totalBayar) {
            return redirect()->back()->withErrors(['message' => 'Uang yang dibayarkan kurang!']);
        }

        // Hitung uang kembalian
        $uangKembalian = $uangDibayar - $totalBayar;

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            foreach ($cart as $item) {
                // Validasi menu_id dan cek stok
                $menu = Menu::where('menu_id', $item['menu_id'])->first(); // Menggunakan menu_id
                if (!$menu) {
                    throw new \Exception('Menu dengan ID ' . $item['menu_id'] . ' tidak ditemukan!');
                }

                if ($menu->stok < $item['quantity']) {
                    throw new \Exception('Stok menu ' . $menu->nama_menu . ' tidak mencukupi!');
                }

                // Simpan transaksi
                Transaksi::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'menu_id' => $menu->menu_id, // Menggunakan menu_id
                    'users_id' => auth()->user()->users_id,
                    'jumlah' => $item['quantity'],
                    'total_harga' => $item['total'],
                    'uang_dibayar' => $uangDibayar,
                    'uang_kembalian' => $uangKembalian,
                    'metode_pembayaran' => $metode,
                ]);

                // Kurangi stok
                $menu->stok -= $item['quantity'];
                $menu->save();
            }

            // Commit transaksi database
            DB::commit();

            // Tampilkan struk
            $kasir = auth()->user()->nama_lengkap ?? 'Tidak Diketahui';
            return view('pemesanan.struk', compact(
                'kodeTransaksi',
                'cart',
                'totalBayar',
                'uangDibayar',
                'uangKembalian',
                'metode',
                'kasir'
            ));
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
