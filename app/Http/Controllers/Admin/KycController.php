<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use App\Models\User;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index(Request $request)
    {
        $query = KycDocument::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        $documents = $query->latest()->paginate(15);
        $users = User::where('is_active', true)->whereIn('role', ['landlord', 'agent', 'tenant'])->get();
        return view('admin.kyc.index', compact('documents', 'users'));
    }

    public function show(KycDocument $document)
    {
        $document->load('user', 'reviewer');
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'document' => $document]);
        }
        return view('admin.kyc.show', compact('document'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'document_type' => 'required|in:national_id,passport,voters_id,business_license,brela_cert,tin_cert,utility_bill,title_deed,sale_agreement,authorization_letter,selfie',
            'document_number' => 'nullable|string|max:255',
            'file_path' => 'required|string',
        ]);

        $validated['status'] = 'pending';
        $document = KycDocument::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'KYC document added successfully', 'document' => $document]);
        }
        return back()->with('status', 'KYC document added');
    }

    public function destroy(KycDocument $document)
    {
        $document->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'KYC document deleted successfully']);
        }
        return back()->with('status', 'KYC document deleted');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:kyc_documents,id']);

        $count = KycDocument::whereIn('id', $request->ids)->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $count . ' documents deleted successfully']);
        }
        return back()->with('status', $count . ' documents deleted');
    }

    public function approve(KycDocument $document)
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
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

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'KYC document approved', 'status' => 'approved']);
        }
        return back()->with('status', 'KYC document approved');
    }

    public function reject(Request $request, KycDocument $document)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        $user = $document->user;
        $pendingDocs = $user->kycDocuments()->where('status', 'pending')->count();
        $rejectedDocs = $user->kycDocuments()->where('status', 'rejected')->count();
        $approvedDocs = $user->kycDocuments()->where('status', 'approved')->count();
        if ($pendingDocs === 0 && $approvedDocs === 0) {
            $user->update(['kyc_status' => 'rejected']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'KYC document rejected', 'status' => 'rejected']);
        }
        return back()->with('status', 'KYC document rejected');
    }
}
