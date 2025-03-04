<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
        date_default_timezone_set("Asia/Jakarta");
    }

    /**
     * Menampilkan daftar transaksi
     */
    public function index()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Head Staff')) {
            return view('transaksi.index');
        } else {
            return view('errors.error', [
                'title' => "Akses Ditolak",
                'message' => "Anda tidak memiliki izin untuk mengakses halaman ini.",
                'redirectUrl' => route('home')
            ]);
        }
    }

    /**
     * Mengambil data transaksi untuk DataTables
     */
    public function getData(Request $request)
    {
        $transaksis = Transaction::select('transaction_code', 'created_at')
            ->groupBy('transaction_code', 'created_at')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal jika ada
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $transaksis->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            $today = Carbon::today();
            $transaksis->whereDate('created_at', $today);
        }

        return DataTables::of($transaksis)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($row) {
                return '<a href="' . route('transaksi.show', $row->transaction_code) . '" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Mendapatkan data laporan transaksi
     */
    public function getReportingData(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date ?? now())->startOfDay();
            $endDate = Carbon::parse($request->end_date ?? now())->endOfDay();

            // Query transaksi dengan informasi menu
            $menus = DB::table('transactions')
                ->join('menu_items', 'transactions.menu_item_id', '=', 'menu_items.menu_item_id')
                ->selectRaw('menu_items.menu_name,
                            SUM(transactions.quantity) AS jumlah_terjual,
                            CAST(SUM(transactions.total_price) AS UNSIGNED) AS total_harga')
                ->whereBetween('transactions.created_at', [$startDate, $endDate])
                ->groupByRaw('menu_items.menu_name WITH ROLLUP')
                ->get();

            Log::info('Hasil query transaksi:', ['menus' => $menus]);

            $totalKeseluruhan = null;
            $data = [];
            foreach ($menus as $menu) {
                if (is_null($menu->menu_name)) {
                    $totalKeseluruhan = $menu;
                } else {
                    $data[] = $menu;
                }
            }

            // Query menu yang belum terjual pada periode tertentu
            $unboughtMenus = DB::table('menu_items')
                ->leftJoin('transactions', function ($join) use ($startDate, $endDate) {
                    $join->on('menu_items.menu_item_id', '=', 'transactions.menu_item_id')
                        ->whereBetween('transactions.created_at', [$startDate, $endDate]);
                })
                ->leftJoin('categories', 'menu_items.category_id', '=', 'categories.category_id')
                ->select('menu_items.menu_name', 'categories.category_name')
                ->whereNull('transactions.transaction_id')
                ->orderBy('categories.category_name')
                ->get()
                ->groupBy('category_name');

            $menuTerlaris = collect($data)->sortByDesc('jumlah_terjual')->first();
            $menuTersedikit = collect($data)->sortBy('jumlah_terjual')->first();

            return response()->json([
                'data' => $data,
                'total_keseluruhan' => $totalKeseluruhan,
                'menu_terlaris' => $menuTerlaris,
                'menu_tersedikit' => $menuTersedikit,
                'menu_belum_terjual' => $unboughtMenus,
            ]);
        } catch (\Exception $e) {
            Log::error('Error dalam laporan transaksi: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Menampilkan detail transaksi berdasarkan kode transaksi
     */
    public function show($transaction_code)
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Head Staff')) {
            $transaksis = Transaction::with(['menuItem', 'user'])
                ->where('transaction_code', $transaction_code)
                ->get();

            if ($transaksis->isEmpty()) {
                return redirect()->route('transaksi.index')->with('error', 'Transaksi tidak ditemukan.');
            }

            $kasir = $transaksis->first()->user->full_name ?? 'Tidak Diketahui';

            return view('transaksi.detail', compact('transaksis', 'transaction_code', 'kasir'));
        } else {
            return view('errors.error', [
                'title' => "Akses Ditolak",
                'message' => "Anda tidak memiliki izin untuk mengakses halaman ini.",
                'redirectUrl' => route('home')
            ]);
        }
    }
}
