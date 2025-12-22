<?php

namespace App\Http\Controllers\Admin;

use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PermissionRequest;

class PermissionController extends Controller
{
    public function index()
    {
        return response()->json(['data' => Permission::all()]);
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create(['name' => $request->name]);
        return response()->json(['message' => 'Permission created successfully', 'data' => $permission]);
    }

    public function show(Permission $permission)
    {
        return response()->json(['data' => $permission]);
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);
        return response()->json(['message' => 'Permission updated successfully']);
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
