<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'business_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'region' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->fresh(),
            ]);
        }

        return back()->with('status', 'Profile updated successfully');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = auth()->user();

        if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'avatar_url' => $user->fresh()->avatar_url,
            ]);
        }

        return back()->with('status', 'Avatar uploaded successfully');
    }

    public function removeAvatar(Request $request)
    {
        $user = auth()->user();

        if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Avatar removed successfully',
            ]);
        }

        return back()->with('status', 'Avatar removed successfully');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update(['password' => $request->password]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);
        }

        return back()->with('status', 'Password changed successfully');
    }
}
