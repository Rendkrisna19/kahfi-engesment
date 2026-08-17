<?php

namespace App\Http\Controllers;

use App\Models\KategoriCreator;
use Illuminate\Http\Request;

class KategoriCreatorController extends Controller
{
    public function index()
    {
        $kategori = KategoriCreator::orderBy('id', 'desc')->get();
        return view('kategori-creator.index', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        KategoriCreator::create($request->only('nama'));
        return back()->with('success', 'Kategori Creator berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriCreator $kategori_creator)
    {
        $request->validate(['nama' => 'required|string|max:100']);
        $kategori_creator->update($request->only('nama'));
        return back()->with('success', 'Kategori Creator berhasil diperbarui.');
    }

    public function destroy(KategoriCreator $kategori_creator)
    {
        $kategori_creator->delete();
        return back()->with('success', 'Kategori Creator berhasil dihapus.');
    }
}
