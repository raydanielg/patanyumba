<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,landlord,agent,tenant',
        ]);

        $validated['password'] = \Hash::make($validated['password']);
        $validated['is_active'] = true;
        $validated['email_verified_at'] = now();

        $user = User::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User created successfully', 'user' => $user]);
        }
        return back()->with('status', 'User created successfully');
    }

    public function show(User $user)
    {
        $user->load('properties', 'kycDocuments', 'payments', 'subscriptions');
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        }
        return back()->with('status', 'User deleted');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:users,id']);

        $count = User::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $count . ' users deleted successfully']);
        }
        return back()->with('status', $count . ' users deleted');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $user->is_active ? 'User activated' : 'User suspended', 'is_active' => $user->is_active]);
        }
        return back()->with('status', $user->is_active ? 'User activated' : 'User suspended');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:admin,landlord,agent,tenant']);
        $user->update(['role' => $request->role]);
        return back()->with('status', 'Role updated');
    }
}
