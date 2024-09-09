<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        // Menampilkan semua pengguna termasuk yang ter-soft delete
        $users = User::withTrashed()->get();
        return view('admin.permissions.index', compact('users'));
    }

    public function edit($id, $name)
    {
        $name = urldecode($name);

        $user = User::withTrashed()
                    ->where('id', $id)
                    ->where('name', $name)
                    ->firstOrFail();

        if ($user->email === 'reizandid@gmail.com') {
            return redirect()->route('admin.permissions.index')->with('error', 'Cannot edit role for the permanent admin account.');
        }

        return view('admin.permissions.edit', compact('user'));
    }

    public function update(Request $request, $id, $name)
    {
        $name = urldecode($name);

        $user = User::withTrashed()
                    ->where('id', $id)
                    ->where('name', $name)
                    ->firstOrFail();

        if ($user->email === 'reizandid@gmail.com') {
            return redirect()->route('admin.permissions.index')->with('error', 'Cannot change the role for the permanent admin account.');
        }

        // Update name, email, dan userType
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->userType = $request->input('userType');
        $user->save();

        return redirect()->route('admin.permissions.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->email === 'reizandid@gmail.com') {
            return redirect()->route('admin.permissions.index')->with('error', 'Cannot delete the permanent admin account.');
        }

        $user->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'User soft deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.permissions.index')->with('success', 'User restored successfully.');
    }
}
