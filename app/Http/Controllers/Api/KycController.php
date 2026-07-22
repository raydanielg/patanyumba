<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $query = KycDocument::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $documents = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    public function myDocuments(Request $request)
    {
        $documents = KycDocument::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents,
        ]);
    }

    public function show(KycDocument $document)
    {
        $document->load('user', 'reviewer');

        return response()->json([
            'success' => true,
            'data' => $document,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|in:national_id,passport,voters_id,business_license,brela_cert,tin_cert,utility_bill,title_deed,sale_agreement,authorization_letter,selfie',
            'document_number' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $path = $request->file('file')->store('kyc', 'public');

        $document = KycDocument::create([
            'user_id' => $request->user()->id,
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'] ?? null,
            'file_path' => $path,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KYC document uploaded successfully',
            'data' => $document,
        ], 201);
    }

    public function destroy(Request $request, KycDocument $document)
    {
        if ($request->user()->id !== $document->user_id && !$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'KYC document deleted successfully',
        ]);
    }

    public function approve(Request $request, KycDocument $document)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $document->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        $user = $document->user;
        $pendingDocs = $user->kycDocuments()->where('status', 'pending')->count();
        if ($pendingDocs === 0) {
            $approvedDocs = $user->kycDocuments()->where('status', 'approved')->count();
            $user->update([
                'kyc_status' => 'approved',
                'verification_level' => $approvedDocs >= 5 ? 'full' : 'basic',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'KYC document approved',
            'data' => $document->fresh(),
        ]);
    }

    public function reject(Request $request, KycDocument $document)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['rejection_reason' => 'required|string']);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $user = $document->user;
        $pendingDocs = $user->kycDocuments()->where('status', 'pending')->count();
        $approvedDocs = $user->kycDocuments()->where('status', 'approved')->count();
        if ($pendingDocs === 0 && $approvedDocs === 0) {
            $user->update(['kyc_status' => 'rejected']);
        }

        return response()->json([
            'success' => true,
            'message' => 'KYC document rejected',
            'data' => $document->fresh(),
        ]);
    }
}
