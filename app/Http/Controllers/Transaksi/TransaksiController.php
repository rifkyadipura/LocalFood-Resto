<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
