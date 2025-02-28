<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Order;

class LaporanPermintaanDepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('orders')->get();
        return view('laporan-permintaan-department.index', compact('departments'));
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);
        $orders = Order::where('department_id', $id)
                    ->with(['requester', 'approvedByKadiv', 'approvedByKagud', 'orderItems.barang'])
                    ->latest()
                    ->paginate(10);
        
        return view('laporan-permintaan-department.show', compact('department', 'orders'));
    }
}
