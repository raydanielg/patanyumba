<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|regex:/^0[6-7]\d{8}$/|max:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'tenant',
            'is_active' => true,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'token' => $token,
            'user' => $this->userResponse($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $this->userResponse($user),
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $this->userResponse($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'region' => 'sometimes|nullable|string|max:255',
            'district' => 'sometimes|nullable|string|max:255',
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $this->userResponse($user->fresh()),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully',
        ]);
    }

    private function userResponse(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role,
            'avatar_url' => $user->avatar_url,
            'region' => $user->region,
            'district' => $user->district,
            'kyc_status' => $user->kyc_status,
            'verification_level' => $user->verification_level,
            'business_name' => $user->business_name,
            'address' => $user->address,
            'is_active' => $user->is_active,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
        ];
    }

    public function becomeLandlord(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|in:landlord,agent',
            'business_name' => 'nullable|string|max:255',
            'region' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'sometimes|nullable|string|max:20',
        ]);

        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admins cannot change role this way.',
            ], 422);
        }

        if ($user->role === 'landlord' || $user->role === 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'You are already a ' . $user->role . '.',
            ], 422);
        }

        $user->update([
            'role' => $validated['role'],
            'business_name' => $validated['business_name'] ?? null,
            'region' => $validated['region'],
            'district' => $validated['district'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? $user->phone,
            'kyc_status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You are now a ' . $validated['role'] . '. Please complete KYC verification to list properties.',
            'user' => $this->userResponse($user->fresh()),
        ]);
    }

    public function myKycStatus(Request $request)
    {
        $user = $request->user();
        $documents = $user->kycDocuments()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => [
                'kyc_status' => $user->kyc_status,
                'verification_level' => $user->verification_level,
                'role' => $user->role,
                'documents' => $documents,
            ],
        ]);
    }
}
