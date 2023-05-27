<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Inventory;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Menampilkan pemesanan berdasarkan user seller yang sedang login
    public function index()
    {
        $user = Auth::user();
        $order = Order::where('userIdSeller', $user->id)->get();

        if($order)
            return ResponseFormatter::success(
                $order,
                'Data pemesanan berhasil ditampilkan'
            );
        else
            return ResponseFormatter::error(
                null,
                'Data pemesanan tidak ada',
                404
            );
    }

    // Menampilkan detail pemesanan berdasarkan user seller yang sedang login dan id pemesanan
    public function show(Request $request, $id)
    {
        $user = Auth::user();

        $order = Order::where('id', $id)
            ->where('userSeller', $user->id)
            ->first();

        if($order)
            return ResponseFormatter::success(
                $order,
                'Data pemesanan berhasil ditampilkan'
            );
        else
            return ResponseFormatter::error(
                null,
                'Data pemesanan tidak ada',
                404
            );
    }

    // Menambahkan pemesanan dari user customer berdasarkan user seller yang sedang login
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'orders' => ['required','array'],
            'orders.*.inventoriesId' => ['required','exists:inventories,id'],
            'orders.*.quantity' => ['required','numeric'],
        ]);

        $orderArray = $request->input('orders');

        foreach ($orderArray as $orderItem) {
            $inventory = Inventory::find($orderItem['inventoriesId']);

            if (!$inventory) {
                return response()->json(['message' => 'Inventaris tidak ditemukan'], 404);
            }

            $order = new Order();
            $order->userIdCustomer = $user->id;
            $order->userIdSeller = $request->user()->id;
            $order->id_inventaris = $orderItem['inventarisId'];
            $order->date = Carbon::now();
            $order->status = 'waiting';
            $order->quantity = $orderItem['quantity'];
            $order->totalPrice = $inventory->harga * $orderItem['quantity'];
            $order->save();
        }

        return ResponseFormatter::success(
            $order,
            'Data pemesanan berhasil dibuat'
        );
    }

    // Mengubah status pemesanan berdasarkan user seller yang sedang login dan id pemesanan
    public function update(Request $request, $id)
    {
        $user = $request->user();

        // Validasi input
        $request->validate([
            'status' => 'required|in:waiting,on process,shipping,delivered',
        ]);

        $order = Order::where('id', $id)
            ->where('userIdSeller', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        $status = $request->input('status');

        if ($status === 'waiting') {
            // Periksa apakah pesanan sudah melewati 1 minggu
            $oneWeekAgo = Carbon::now()->subWeek();
            if ($order->status === 'waiting' && $order->tanggal < $oneWeekAgo) {
                $order->status = 'expired';
            } else {
                return response()->json(['message' => 'Tidak dapat mengubah status menjadi waiting'], 400);
            }
        } elseif ($status === 'on process' || $status === 'shipping' || $status === 'delivered') {
            $order->status = $status;
        }

        $order->save();

        return response()->json(['message' => 'Status pemesanan berhasil diubah']);
    }

    // Menghapus pemesanan berdasarkan user seller yang sedang login dan id pemesanan
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $order = Order::where('id', $id)
            ->where('userIdSeller', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        $order->delete();

        return ResponseFormatter::success(
            $order,
            'Data inventaris berhasil dihapus'
        );
    }
}
