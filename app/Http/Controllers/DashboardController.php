<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role->role;

        $jumlahPermintaan = 0;  
        $total = [];            
        $label = [];  

        // Data umum
        $barang = Barang::count();
        $barangMasuk = BarangMasuk::count();
        $barangKeluar = BarangKeluar::count();
        $userCount = User::count();

        // Data barang dengan stok minimum
        $barangMinimum = Barang::select('kode', 'nama_barang', 'stok', 'stok_minimum')
            ->whereRaw('stok <= stok_minimum')
            ->orderBy('stok', 'asc')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->status_stok = $item->stok <= ($item->stok_minimum / 2) ? 'danger' : 'warning';
                return $item;
            });

        // Data chart barang masuk & keluar
        $barangMasukData = BarangMasuk::select(
            DB::raw("DATE_FORMAT(tanggal_masuk, '%Y-%m') as date"),
            DB::raw('count(*) as total')
        )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        $barangKeluarData = BarangKeluar::select(
            DB::raw("DATE_FORMAT(tanggal_keluar, '%Y-%m') as date"),
            DB::raw('count(*) as total')
        )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get();

        // Data untuk Kepala Divisi
        $ordersKadiv = collect();
        if ($userRole === 'kepala divisi') {
            $ordersKadiv = Order::where('status', 'Pending')
                                ->whereNull('tanggal_approve_kadiv')
                                ->where('department_id', $user->department_id)
                                ->get();
        }

        // Data untuk Kepala Gudang
        $orders = collect();
        if ($userRole === 'kepala gudang') {
            $orders = Order::where('status', 'Approved by Kadiv')
                         ->whereNull('tanggal_approve_kagud')
                         ->get();
        }

        // Data untuk Admin Gudang
        $ordersAdmin = collect();
        if ($userRole === 'admin gudang') {
            $ordersAdmin = Order::where('status', 'Approved by Kagud')
                               ->whereDoesntHave('barangKeluar')
                               ->get();
        }

        // Fetch department requests within the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $departmentRequests = DB::table('orders')
            ->join('departments', 'orders.department_id', '=', 'departments.id')
            ->select('departments.nama_departemen as department_name', DB::raw('COUNT(orders.id) as request_count'))
            ->whereBetween('orders.created_at', [$startOfMonth, $endOfMonth])
            ->groupBy('departments.nama_departemen')
            ->orderBy('request_count', 'desc')
            ->get();

        $departmentRequestLabels = $departmentRequests->pluck('department_name')->toArray();
        $departmentRequestData = $departmentRequests->pluck('request_count')->toArray();
        $currentMonth = Carbon::now()->format('F Y');

        // Define permintaan based on user's department
        $permintaan = Order::where('department_id', $user->department_id)->get();

        $popularItemsData = [];
        if ($userRole === 'admin divisi' || $userRole === 'kepala divisi' || $userRole === 'superadmin') {
            $jumlahPermintaan = Order::where('department_id', $user->department_id)->count();
            
            // Query barang terpopuler
            $barangPalingBanyakDiminta = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('barangs', 'order_items.barang_id', '=', 'barangs.id')
                ->select('barangs.nama_barang', DB::raw('SUM(order_items.quantity) as total'))
                ->where('orders.department_id', $user->department_id)
                ->where('orders.status', 'Completed') 
                ->groupBy('barangs.nama_barang')
                ->orderBy('total', 'desc')
                ->take(5)
                ->get();

            $total = $barangPalingBanyakDiminta->pluck('total')
                ->map(function ($value) {
                    return (int) $value;
                })->toArray();
            $label = $barangPalingBanyakDiminta->pluck('nama_barang')->toArray();
        }

        return view('dashboard', [
            'barang' => $barang,
            'barangMasuk' => $barangMasuk,
            'barangKeluar' => $barangKeluar,
            'userCount' => $userCount,
            'barangMinimum' => $barangMinimum,
            'barangMasukData' => $barangMasukData,
            'barangKeluarData' => $barangKeluarData,
            'ordersKadiv' => $ordersKadiv,
            'orders' => $orders,
            'ordersAdmin' => $ordersAdmin,
            'jumlahPermintaan' => $permintaan->count(),
            'permintaanPending' => $permintaan->where('status', 'Pending')->count(),
            'permintaanProses' => $permintaan->whereIn('status', ['Approved by Kadiv', 'Approved by Kagud', 'Ready'])->count(),
            'permintaanSelesai' => $permintaan->where('status', 'Completed')->count(),
            'total' => $total,
            'label' => $label,
            'departmentRequestLabels' => $departmentRequestLabels,
            'departmentRequestData' => $departmentRequestData,
            'currentMonth' => $currentMonth
        ]);
    }
}