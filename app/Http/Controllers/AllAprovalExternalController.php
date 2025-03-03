<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CostBids;

class AllAprovalExternalController extends Controller
{
    public function costbidsAproved($id)
    {
        try {
            DB::beginTransaction();
            $costbid = CostBids::where('token', $id)->firstOrFail();
            $costbid->update([
                'status' => 'Approved',
                'approved_by' => 2, // Anggap yang diapprove adalah Direksi
                'approved_at' => now(),
                'token' => null
            ]);
            DB::commit();
            return redirect()->url('/')->with('success', 'Cost Bids ' . $costbid->code . ' Approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function costbidsRejected($id, Request $request)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        try {
            DB::beginTransaction();
            $costbid = CostBids::where('token', $id)->firstOrFail();
            $costbid->update([
                'status' => 'Rejected',
                'rejected_by' => 2, // Anggap yang direject adalah Direksi
                'reason' => $request->reason,
                'token' => null,
                'rejected_at' => now()
            ]);
            DB::commit();
            return redirect()->url('/')->with('success', 'Cost Bids ' . $costbid->code . ' Rejected successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
