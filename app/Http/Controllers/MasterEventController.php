<?php

namespace App\Http\Controllers;

use App\Models\StoEvent;
use App\Models\StoItem;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class MasterEventController extends Controller
{
    public function index()
    {
        return view('sto.event.index', [
            'events' => StoEvent::all()
        ]);
    }

    public function getData()
    {
        $events = StoEvent::all();
        $data = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'nama_event' => $event->nama_event,
                'tanggal_mulai' => $event->tanggal_mulai,
                'tanggal_selesai' => $event->tanggal_selesai,
                'status' => $event->status,
                'reset_date' => $event->reset_date, // Tambahkan kolom reset_date
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sto.event.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_event'     => 'required',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'         => 'required|in:draft,active,closed',
        ],[
            'nama_event.required'       => 'Nama Event Wajib Di Isi !',
            'tanggal_mulai.required'    => 'Tanggal Mulai Wajib Di Isi !',
            'tanggal_mulai.date'        => 'Format Tanggal Mulai Tidak Valid !',
            'tanggal_selesai.required'  => 'Tanggal Selesai Wajib Di Isi !',
            'tanggal_selesai.date'      => 'Format Tanggal Selesai Tidak Valid !',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai !',
            'status.required'           => 'Status Wajib Di Isi !',
            'status.in'                 => 'Status Harus Berupa: draft, active, atau closed !',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $event = StoEvent::create([
            'nama_event'     => $request->nama_event,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan'     => $request->keterangan,
            'status'         => $request->status,
            'created_by'     => Auth::user()->id
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Event STO Berhasil Dibuat !',
            'data'      => $event
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $event = StoEvent::findOrFail($id);
        return view('sto.event.show', [
            'event' => $event
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $event = StoEvent::findOrFail($id);   
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Event STO',
            'data'    => $event
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $event = StoEvent::find($id);

        $validator = Validator::make($request->all(),[
            'nama_event'     => 'required',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status'         => 'required|in:draft,active,closed',
        ],[
            'nama_event.required'       => 'Nama Event Wajib Di Isi !',
            'tanggal_mulai.required'    => 'Tanggal Mulai Wajib Di Isi !',
            'tanggal_mulai.date'        => 'Format Tanggal Mulai Tidak Valid !',
            'tanggal_selesai.required'  => 'Tanggal Selesai Wajib Di Isi !',
            'tanggal_selesai.date'      => 'Format Tanggal Selesai Tidak Valid !',
            'tanggal_selesai.after_or_equal' => 'Tanggal Selesai Harus Setelah atau Sama Dengan Tanggal Mulai !',
            'status.required'           => 'Status Wajib Di Isi !',
            'status.in'                 => 'Status Harus Berupa: draft, active, atau closed !',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        $event->update([
            'nama_event'     => $request->nama_event,
            'tanggal_mulai'  => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan'     => $request->keterangan,
            'status'         => $request->status
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Event STO Berhasil Diupdate',
            'data'      => $event
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        StoEvent::find($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event STO Berhasil Dihapus!'
        ]);    
    }

    /**
     * Change event status.
     */
    public function resetStok($id)
    {
        try {
            // Ambil event dan pastikan statusnya active
            $event = StoEvent::findOrFail($id);
            
            if ($event->status !== 'active') {
                return response()->json([
                    'success' => false, 
                    'message' => 'Hanya event dengan status active yang dapat direset stoknya.'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Ambil semua item STO dari event ini
            $stoItems = StoItem::where('sto_event_id', $event->id)->get();
            
            foreach ($stoItems as $item) {
                // Update stok di tabel barang
                $barang = Barang::find($item->barang_id);
                if ($barang) {
                    $barang->stok = $item->stok_aktual;
                    $barang->save();
                }
                
                // Update status item menjadi close
                $item->status = 'close';
                $item->save();
            }
            
            // Update status event menjadi closed
            $event->status = 'closed';
            $event->reset_date = now();
            $event->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil direset dan event telah ditutup.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}