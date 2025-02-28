<?php

namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Department;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class LaporanPermintaanController extends Controller
{
    public function index()
    {
        // Mengakses view untuk laporan permintaan
        $departments = Department::all();
        return view('laporan-permintaan.index', compact('departments'));
        
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = Order::with(['department', 'orderItems.barang'])
            ->where('status', 'completed')
            ->where('orders.department_id', $user->department_id)
            ->when($request->tanggal_mulai, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->tanggal_selesai, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->tanggal_selesai);
            })
            ->when($request->department_id, function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            })
            ->get();

        $data = [];
        foreach ($query as $order) {
            foreach ($order->orderItems as $item) {
                $data[] = [
                    'tanggal' => $order->created_at,
                    'department' => $order->department->nama_departemen,
                    'nama_barang' => $item->barang->nama_barang,
                    'jumlah_permintaan' => $item->quantity,
                    'status' => $order->status,
                    'keterangan' => $order->catatan,
                ];
            }
        }

        return response()->json($data);
    }

    public function printPermintaan(Request $request)
    {
        $user = Auth::user();
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        // Query untuk mendapatkan order dengan items-nya
        $orders = Order::with(['orderItems.barang'])
            ->where('orders.department_id', $user->department_id)
            ->where('status', 'Completed');

        // Filter berdasarkan tanggal
        if ($tanggalMulai && $tanggalSelesai) {
            $orders->whereBetween('created_at', [$tanggalMulai, $tanggalSelesai]);
        }

        $orders = $orders->get();

        // Menyiapkan data untuk view
        $groupedData = [];
        
        foreach ($orders as $order) {
            $tanggal = Carbon::parse($order->created_at)->format('d F Y');
            
            if (!isset($groupedData[$tanggal])) {
                $groupedData[$tanggal] = [];
            }
            
            // Menambahkan items ke dalam array tanggal
            foreach ($order->orderItems as $item) {
                $groupedData[$tanggal][] = [
                    'nama_barang' => $item->barang->nama_barang,
                    'quantity' => $item->quantity
                ];
            }
        }

        // Generate PDF
        $dompdf = new Dompdf();
        $html = view('laporan-permintaan.print-permintaan', compact('groupedData', 'tanggalMulai', 'tanggalSelesai'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('print-permintaan.pdf', ['Attachment' => false]);
    }

}
