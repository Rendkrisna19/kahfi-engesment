<?php

namespace App\Http\Controllers;

use App\Models\KategoriKonten;
use Illuminate\Http\Request;

class KategoriKontenController extends Controller
{
    public function index()
    {
        $kategori = KategoriKonten::orderBy('id', 'desc')->get();
        return view('kategori-konten.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        KategoriKonten::create($request->only('nama'));
        return back()->with('success', 'Kategori Konten berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriKonten $kategori_konten)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        $kategori_konten->update($request->only('nama'));
        return back()->with('success', 'Kategori Konten berhasil diperbarui.');
    }

    public function destroy(KategoriKonten $kategori_konten)
    {
        $kategori_konten->delete();
        return back()->with('success', 'Kategori Konten berhasil dihapus.');
    }
}
