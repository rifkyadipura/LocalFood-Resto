<?php

namespace App\Http\Controllers\Pemesanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use App\Models\Transaction;
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
            $menuItems = MenuItem::all();
            return view('pemesanan.index', compact('menuItems'));
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
        $menuItems = MenuItem::where('status', 1)->get();

        return view('pemesanan.index', [
            'menuItems' => $menuItems,
            'cart' => $cart,
            'showPaymentModal' => true
        ]);
    }

    public function prosesPembayaran(Request $request)
    {
        $request->validate([
            'cart' => 'required|json',
            'amount_paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:Cash,QRIS',
        ]);

        $cart = json_decode($request->input('cart'), true);
        if (!$cart || count($cart) === 0) {
            return redirect()->back()->withErrors(['message' => 'Keranjang belanja kosong!']);
        }

        // Hitung subtotal, pajak, dan total setelah pajak
        $subtotal = collect($cart)->sum('total'); // Total harga sebelum pajak
        $tax = $subtotal * 0.1; // Pajak 10%
        $total_price_taxed = $subtotal + $tax; // Total setelah pajak

        $amountPaid = $request->input('amount_paid');
        $paymentMethod = $request->input('payment_method');
        $transactionCode = Transaction::generateTransactionCode();

        if ($paymentMethod === 'Cash' && $amountPaid < $total_price_taxed) {
            return redirect()->back()->withErrors(['message' => 'Uang yang dibayarkan kurang!']);
        }

        $changeAmount = $amountPaid - $total_price_taxed;

        try {
            DB::beginTransaction();

            foreach ($cart as $item) {
                $menuItem = MenuItem::find($item['menu_item_id']);
                if (!$menuItem || $menuItem->stock < $item['quantity']) {
                    throw new \Exception('Menu tidak valid atau stok tidak mencukupi.');
                }

                Transaction::create([
                    'transaction_code' => $transactionCode,
                    'menu_item_id' => $menuItem->menu_item_id,
                    'user_id' => auth()->user()->user_id,
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total'], // Harga sebelum pajak
                    'total_price_taxed' => $item['total'] + ($item['total'] * 0.1), // Harga setelah pajak
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'payment_method' => $paymentMethod,
                ]);

                $menuItem->stock -= $item['quantity'];
                if ($menuItem->stock <= 0) {
                    $menuItem->status = 0;
                }
                $menuItem->save();
            }

            DB::commit();

            return view('pemesanan.struk', compact(
                'transactionCode',
                'cart',
                'subtotal',
                'tax',
                'total_price_taxed',
                'amountPaid',
                'changeAmount',
                'paymentMethod'
            ));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
