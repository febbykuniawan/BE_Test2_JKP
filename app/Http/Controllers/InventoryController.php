<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    // Menampilkan inventaris berdasarkan user seller yang sedang login
    public function index()
    {
        $user = Auth::user();
        $inventory = Inventory::with(['user'])->where('userIdSeller', $user->id)->get();

        if($inventory)
            return ResponseFormatter::success(
                $inventory,
                'Data inventaris berhasil ditampilkan'
            );
        else
            return ResponseFormatter::error(
                null,
                'Data inventaris tidak ada',
                404
            );
    }

    public function show($id)
    {
        $user = Auth::user();

        $inventory = Inventory::where('id', $id)
            ->where('userIdSeller', $user->id)
            ->first();

        if($inventory)
            return ResponseFormatter::success(
                $inventory,
                'Data inventaris berhasil ditampilkan'
            );
        else
            return ResponseFormatter::error(
                null,
                'Data inventaris tidak ada',
                404
            );
    }

    // Menambahkan inventaris berdasarkan user seller yang sedang login
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Validasi input
        $request->validate([
            'name' => ['required','string', Rule::unique('inventories')->where(function ($query) {
                return $query->where('userIdSeller', Auth::id());
            }),],
            'desc' => ['required','string'],
            'price' => ['required','numeric'],
            'stock' => ['required','numeric'],
        ]);

        $inventory = new Inventory();
        $inventory->userIdSeller = $user->id;
        $inventory->name = $request->input('name');
        $inventory->desc = $request->input('desc');
        $inventory->price = $request->input('price');
        $inventory->stock = $request->input('stock');
        $inventory->save();

        return ResponseFormatter::success(
            $inventory,
            'Data inventaris berhasil ditambahkan'
        );
    }

    // Mengubah inventaris berdasarkan user seller yang sedang login dan id inventaris
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'name' => ['required','string', Rule::unique('inventories')->where(function ($query) {
                return $query->where('userIdSeller', Auth::id());
            }),],
            'desc' => ['required','string'],
            'price' => ['required','numeric'],
            'stock' => ['required','numeric'],
        ]);

        $inventory = Inventory::where('id', $id)
            ->where('userIdSeller', $user->id)
            ->first();

        if (!$inventory) {
            return response()->json(['message' => 'Data inventaris tidak ditemukan'], 404);
        }

        $inventory->userIdSeller = $user->id;
        $inventory->name = $request->input('name');
        $inventory->desc = $request->input('desc');
        $inventory->price = $request->input('price');
        $inventory->stock = $request->input('stock');
        $inventory->save();

        return ResponseFormatter::success(
            $inventory,
            'Data inventaris berhasil ditambahkan'
        );
    }

    // Menghapus inventaris berdasarkan user seller yang sedang login dan id inventaris
    public function destroy($id)
    {
        $user = Auth::user();

        $inventory = Inventory::where('id', $id)
            ->where('userIdSeller', $user->id)
            ->first();

        if (!$inventory) {
            return response()->json(['message' => 'Data inventaris tidak ditemukan'], 404);
        }

        $inventory->delete();

        return ResponseFormatter::success(
            $inventory,
            'Data inventaris berhasil dihapus'
        );
    }
}
