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

        $documents = $query->latest()->paginate(15);
        return view('admin.kyc.index', compact('documents'));
    }

    public function approve(KycDocument $document)
    {
        $document->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
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
        return back()->with('status', 'KYC document rejected');
    }
}
