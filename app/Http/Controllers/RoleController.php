<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($data) {
            return explode('.', $data->name)[0]; // Kelompokkan berdasarkan awalan (misal: dashboard, master-data, dll)
        });
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($data) {
            return explode('.', $data->name)[0];
        });
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        } else {
            $role->syncPermissions([]); // Kosongkan jika tidak ada yang diceklis
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, ['Admin Master', 'Admin', 'Client'])) {
            return redirect()->route('roles.index')->with('error', 'Role bawaan sistem tidak boleh dihapus!');
        }
        
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus');
    }
}
