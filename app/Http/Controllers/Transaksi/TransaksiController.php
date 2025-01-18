<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Kepala Staf')) {
            return view('transaksi.index');
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return view('errors.error', compact('title', 'message', 'redirectUrl'));
        }
    }

    public function getData(Request $request)
    {
        $transaksis = Transaksi::select('kode_transaksi', 'created_at')
            ->groupBy('kode_transaksi', 'created_at')
            ->orderBy('created_at', 'desc');

        // Cek apakah ada filter start_date dan end_date
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay(); // Awal hari
            $endDate = Carbon::parse($request->end_date)->endOfDay(); // Akhir hari
            $transaksis->whereBetween('created_at', [$startDate, $endDate]);
        } else {
            // Jika tidak ada filter, tampilkan data hanya untuk hari ini
            $today = Carbon::today();
            $transaksis->whereDate('created_at', $today);
        }

        return DataTables::of($transaksis)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })
            ->addColumn('actions', function ($row) {
                $detailUrl = route('transaksi.show', $row->kode_transaksi);
                return '<a href="' . $detailUrl . '" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getReportingData(Request $request)
    {
        try {
            // Log untuk debugging
            // Log::info('Filter tanggal:', [
            //     'start_date' => $request->start_date,
            //     'end_date' => $request->end_date,
            // ]);

            // Filter tanggal
            $startDate = Carbon::parse($request->start_date ?? now())->startOfDay();
            $endDate = Carbon::parse($request->end_date ?? now())->endOfDay();

            // Query dengan ROLLUP untuk mendapatkan data menu terjual
            $menus = DB::table('transaksi')
                ->join('menu', 'transaksi.menu_id', '=', 'menu.menu_id')
                ->selectRaw('menu.nama_menu,
                            SUM(transaksi.jumlah) AS jumlah_terjual,
                            CAST(SUM(transaksi.total_harga) AS UNSIGNED) AS total_harga')
                ->whereBetween('transaksi.created_at', [$startDate, $endDate])
                ->groupByRaw('menu.nama_menu WITH ROLLUP')
                ->get();

            // Debug hasil query dengan ROLLUP
            Log::info('Hasil query ROLLUP:', ['menus' => $menus]);

            // Pisahkan data menu terjual dan total keseluruhan
            $totalKeseluruhan = null;
            $data = [];
            foreach ($menus as $menu) {
                if (is_null($menu->nama_menu)) {
                    $totalKeseluruhan = $menu;
                } else {
                    $data[] = $menu;
                }
            }

            // Query menu yang belum pernah terjual pada tanggal yang difilter
            $unboughtMenus = DB::table('menu')
                ->leftJoin('transaksi', function ($join) use ($startDate, $endDate) {
                    $join->on('menu.menu_id', '=', 'transaksi.menu_id')
                        ->whereBetween('transaksi.created_at', [$startDate, $endDate]);
                })
                ->leftJoin('kategory', 'menu.kategory_id', '=', 'kategory.kategory_id')
                ->select('menu.nama_menu', 'kategory.nama_kategory')
                ->whereNull('transaksi.transaksi_id')
                ->orderBy('kategory.nama_kategory')
                ->get()
                ->groupBy('nama_kategory');

            // Debug hasil query untuk menu yang belum pernah terjual
            // Log::info('Menu belum terjual:', ['unboughtMenus' => $unboughtMenus]);

            // Cari menu terlaris dan tersedikit
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
            Log::error('Error in getReportingData: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    public function show($kode_transaksi)
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'Kepala Staf')) {
            $transaksis = Transaksi::with(['menu', 'user'])
                ->where('kode_transaksi', $kode_transaksi)
                ->get();
            $kasir = $transaksis->first()->user->nama_lengkap ?? 'Tidak Diketahui';
            return view('transaksi.detail', compact('transaksis', 'kode_transaksi', 'kasir'));
        } else {
            $title = "Akses Ditolak";
            $message = "Anda tidak memiliki izin untuk mengakses halaman ini.";
            $redirectUrl = route('home');
            return view('errors.error', compact('title', 'message', 'redirectUrl'));
        }
    }
}
