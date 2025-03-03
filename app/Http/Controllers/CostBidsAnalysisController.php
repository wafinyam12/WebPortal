<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\CostbidsNotificationEmail;
use App\Models\User;
use App\Models\Branch;
use App\Models\CostBids;
use App\Models\CostBidsItems;
use App\Models\CostBidsVendor;
use App\Models\CostBidsAnalysis;
use App\Models\Notifications;

class CostBidsAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view bids analysis', ['only' => ['index']]);
        $this->middleware('permission:show bids analysis', ['only' => ['show']]);
        $this->middleware('permission:create bids analysis', ['only' => ['create', 'store']]);
        $this->middleware('permission:update bids analysis', ['only' => ['edit', 'update']]);
        $this->middleware('permission:print bids analysis', ['only' => ['exportPdf']]);
        $this->middleware('permission:delete bids analysis', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bids = CostBids::all();
        return view('bids.analysis.index', compact('bids'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::select('id', 'name')->get();
        $vendors = CostBidsVendor::select('id', 'name', 'email', 'phone')->get();
        return view('bids.analysis.create', compact('vendors', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'project_name' => 'required|string|max:255',
            'bid_date' => 'required|date',
            'vendor_names' => 'required|array',
            'vendor_names.*' => 'required',

            // Existing vendor fields
            'vendor_email' => 'array',
            'vendor_email.*' => 'nullable|email',
            'vendor_phone' => 'array',
            'vendor_phone.*' => 'nullable|string|max:20',

            // New vendor fields
            'new_vendor_names' => 'array',
            'new_vendor_names.*' => 'required_if:vendor_names.*,new|string|max:255',
            'new_vendor_email' => 'array',
            'new_vendor_email.*' => 'nullable|email',
            'new_vendor_phone' => 'array',
            'new_vendor_phone.*' => 'nullable|string|max:20',

            // Common fields for all vendors
            'vendor*_grand_total' => 'required|numeric|min:0',
            'vendor*_discount' => 'nullable|numeric|min:0|max:100',
            'vendor*_final_total' => 'required|numeric|min:0',
            'terms_of_payment_vendor*' => 'nullable|string|max:255',
            'lead_time_vendor*' => 'nullable|string|max:255',
            'notes_vendor*' => 'nullable|string|max:1000',

            'file' => 'nullable|file|mimes:pdf|max:2048',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer',
            'items.*.uom' => 'required|string',
        ]);

        $romanMonths = [
            'January' => 'I',
            'February' => 'II',
            'March' => 'III',
            'April' => 'IV',
            'May' => 'V',
            'June' => 'VI',
            'July' => 'VII',
            'August' => 'VIII',
            'September' => 'IX',
            'October' => 'X',
            'November' => 'XI',
            'December' => 'XII',
        ];

        $newFile = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $newFile = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/bids/attachment', $newFile);
        }

        DB::beginTransaction();
        try {
            // Generate random token
            $token = bin2hex(random_bytes(10));

            // Simpan data bid
            $costbid = CostBids::create([
                'code' => 'UD/CBA/' . date('Y') . '/' . $romanMonths[date('F')] . '/' . str_pad(CostBids::count() + 1, 4, '0', STR_PAD_LEFT),
                'branch_id' => $request->branch_id,
                'project_name' => $request->project_name,
                'document_date' => now(),
                'bid_date' => $request->bid_date,
                'selected_vendor' => $request->selected_vendor,
                'attachment' => $newFile,
                'notes' => $request->notes,
                'token' => $token,
                'created_by' => auth()->user()->id
            ]);

            // Simpan data vendor
            $vendorIds = [];
            $newVendorIndex = 0; // Index tracker for new vendor arrays

            foreach ($request->vendor_names as $index => $vendorName) {
                if ($vendorName === 'new') {
                    // Handle new vendor creation
                    if (empty($request->new_vendor_names[$newVendorIndex])) {
                        throw new \Exception("Vendor name is required for new vendor at position " . ($index + 1));
                    }

                    $vendor = CostBidsVendor::create([
                        'cost_bids_id' => $costbid->id,
                        'name' => $request->new_vendor_names[$newVendorIndex],
                        'email' => $request->new_vendor_email[$newVendorIndex] ?? null,
                        'phone' => $request->new_vendor_phone[$newVendorIndex] ?? null,
                        'grand_total' => $request->input("vendor{$index}_grand_total", 0),
                        'discount' => $request->input("vendor{$index}_discount", 0),
                        'final_total' => $request->input("vendor{$index}_final_total", 0),
                        'terms_of_payment' => $request->input("terms_of_payment_vendor{$index}"),
                        'lead_time' => $request->input("lead_time_vendor{$index}"),
                        'notes' => $request->input("notes_vendor{$index}"),
                    ]);

                    $vendorIds[$index] = $vendor->id;
                    $newVendorIndex++; // Increment the new vendor index counter
                } else {
                    // Handle existing vendor
                    if (!is_numeric($vendorName)) {
                        throw new \Exception("Invalid vendor ID provided at position " . ($index + 1));
                    }

                    // Update existing vendor with new bid information
                    $vendor = CostBidsVendor::where('id', $vendorName)->first();
                    if (!$vendor) {
                        throw new \Exception("Vendor not found with ID: {$vendorName}");
                    }

                    // Clone the existing vendor for this specific bid
                    $vendorBid = CostBidsVendor::create([
                        'cost_bids_id' => $costbid->id,
                        'name' => $vendor->name,
                        'email' => $request->vendor_email[$index] ?? $vendor->email,
                        'phone' => $request->vendor_phone[$index] ?? $vendor->phone,
                        'grand_total' => $request->input("vendor{$index}_grand_total", 0),
                        'discount' => $request->input("vendor{$index}_discount", 0),
                        'final_total' => $request->input("vendor{$index}_final_total", 0),
                        'terms_of_payment' => $request->input("terms_of_payment_vendor{$index}"),
                        'lead_time' => $request->input("lead_time_vendor{$index}"),
                        'notes' => $request->input("notes_vendor{$index}"),
                    ]);

                    $vendorIds[$index] = $vendorBid->id;
                }
            }

            // Simpan data items
            foreach ($request->items as $itemIndex => $item) {
                if ($item['description'] || $item['quantity']) {
                    $costBidItem = CostBidsItems::create([
                        'cost_bids_id' => $costbid->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'uom' => $item['uom'],
                    ]);

                    // Simpan harga setiap vendor untuk item ini
                    for ($i = 0; $i < $request->vendor_count; $i++) {
                        if (isset($vendorIds[$i]) && isset($item["vendor{$i}_price"])) {
                            CostBidsAnalysis::create([
                                'cost_bids_item_id' => $costBidItem->id,
                                'cost_bids_vendor_id' => $vendorIds[$i],
                                'price' => $item["vendor{$i}_price"] ?? 0,
                            ]);
                        }
                    }
                }
            }

            // Kirim email
            // $this->sendEmail($costbid);

            DB::commit();
            return redirect()->back()->with('success', 'Cost Bids ' . $costbid->code . ' created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $branches = Branch::select('id', 'name')->get();
        $costbid = CostBids::with('items.costBidsAnalysis', 'vendors', 'createdBy', 'approvedBy', 'rejectedBy')->find($id);
        return view('bids.analysis.show', compact('costbid', 'branches'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $branches = Branch::select('id', 'name')->get();
        $bidAnalysis = CostBids::with('items.costBidsAnalysis', 'vendors')->findOrFail($id);
        return view('bids.analysis.edit', compact('bidAnalysis', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'branch' => 'required|exists:branches,id',
            'project_name' => 'nullable|string|max:255',
            'bid_date' => 'required|date',
            'vendor_names' => 'required|array',
            'vendor_emails' => 'required|array',
            'vendor_phones' => 'required|array',
            'selected_vendor' => 'required|string',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'items' => 'required|array',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer',
            'items.*.uom' => 'required|string',
        ]);

        $newFile = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $newFile = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/bids/attachment', $newFile);
        }

        DB::beginTransaction();
        try {
            // Cari cost bid berdasarkan ID
            $costBid = CostBids::findOrFail($id);

            // Generate random token
            $token = bin2hex(random_bytes(10));

            // Update data cost bid
            $costBid->update([
                'branch_id' => $request->branch,
                'project_name' => $request->project_name,
                'bid_date' => $request->bid_date,
                'selected_vendor' => $request->selected_vendor,
                'attachment' => $newFile,
                'notes' => $request->notes,
                'created_by' => auth()->user()->id,
                'status' => 'Open',
                'token' => $token
            ]);

            // Update atau tambah vendor
            $existingVendorIds = $costBid->vendors->pluck('id')->toArray();
            $updatedVendorIds = [];
            foreach ($request->vendor_names as $index => $vendorName) {
                // Pastikan vendor_name, email, dan phone ada sebelum melakukan update
                if ($vendorName || $request->vendor_emails[$index] || $request->vendor_phones[$index] || $request->grand_total[$index] || $request->final_total[$index]) {
                    $vendorData = [
                        'cost_bids_id' => $costBid->id,
                        'name' => $vendorName,
                        'email' => $request->vendor_emails[$index],
                        'phone' => $request->vendor_phones[$index],
                        'grand_total' => $request->grand_total[$index],
                        'discount' => $request->input("vendor{$index}_discount", 0),
                        'final_total' => $request->final_total[$index],
                        'terms_of_payment' => $request->input("terms_of_payment_vendor{$index}", ''),
                        'lead_time' => $request->input("lead_time_vendor{$index}", ''),
                        'notes' => $request->input("vendor{$index}_notes", ''),
                    ];

                    if (isset($existingVendorIds[$index])) {
                        $vendor = CostBidsVendor::find($existingVendorIds[$index]);
                        $vendor->update($vendorData);
                        $updatedVendorIds[] = $vendor->id;
                    } else {
                        $vendor = CostBidsVendor::create($vendorData);
                        $updatedVendorIds[] = $vendor->id;
                    }
                }
            }

            // Hapus vendor yang tidak digunakan
            $vendorsToDelete = array_diff($existingVendorIds, $updatedVendorIds);
            CostBidsVendor::destroy($vendorsToDelete);

            // Update atau tambah items
            $existingItemIds = $costBid->items->pluck('id')->toArray();
            $updatedItemIds = [];
            foreach ($request->items as $itemIndex => $item) {
                if ($item['description'] || $item['quantity']) {
                    $itemData = [
                        'cost_bids_id' => $costBid->id,
                        'description' => $item['description'],
                        'quantity' => $item['quantity'],
                        'uom' => $item['uom'],
                    ];

                    if (isset($existingItemIds[$itemIndex])) {
                        $costBidItem = CostBidsItems::find($existingItemIds[$itemIndex]);
                        $costBidItem->update($itemData);
                        $updatedItemIds[] = $costBidItem->id;
                    } else {
                        $costBidItem = CostBidsItems::create($itemData);
                        $updatedItemIds[] = $costBidItem->id;
                    }

                    // Update harga vendor untuk item ini
                    for ($i = 0; $i < count($updatedVendorIds); $i++) {
                        $price = $item["vendor{$i}_price"] ?? 0;
                        $vendorId = $updatedVendorIds[$i] ?? null;

                        if ($vendorId && $costBidItem) {
                            $analysis = CostBidsAnalysis::where('cost_bids_item_id', $costBidItem->id)
                                ->where('cost_bids_vendor_id', $vendorId)
                                ->first();

                            if ($analysis) {
                                $analysis->update(['price' => $price]);
                            } else {
                                CostBidsAnalysis::create([
                                    'cost_bids_item_id' => $costBidItem->id,
                                    'cost_bids_vendor_id' => $vendorId,
                                    'price' => $price,
                                ]);
                            }
                        }
                    }
                }
            }

            // Hapus items yang tidak digunakan
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            CostBidsItems::destroy($itemsToDelete);

            // kirim email
            $this->sendEmail($costBid);

            DB::commit();
            return redirect()->back()->with('success', 'Cost Bids ' . $costBid->code . ' Updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $costbid = CostBids::find($id);
            $costbid->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Cost Bids ' . $costbid->code . ' Deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Generate Pdf Costbids Analysis 
     */
    public function exportPdf($id)
    {
        $costbid = CostBids::with('items.costBidsAnalysis', 'vendors')->findOrFail($id);
        $pdf = PDF::loadView('bids.analysis.pdf', compact('costbid'));
        $newFilename = 'costbids-' . str_replace('/', '-', $costbid->code) . '.pdf';
        return $pdf->stream($newFilename);
    }

    /**
     * Kirim Email Costbids Analysis 
     */
    private function sendEmail(CostBids $costbid)
    {
        try {
            // Create PDF
            $costbid = CostBids::with('items.costBidsAnalysis', 'vendors')->findOrFail($costbid->id);
            $pdf = PDF::loadView('bids.analysis.pdf', compact('costbid'));

            // Menentukan nama file
            $baseFilename = 'costbids-' . str_replace('/', '-', $costbid->code);
            $directory = public_path('storage/bids/analysis/');

            // Cek apakah file sudah ada, jika iya, tambahkan revisi
            $newFilename = $baseFilename . '.pdf';
            $revision = 1;

            while (file_exists($directory . $newFilename)) {
                $newFilename = $baseFilename . '-rev' . $revision . '.pdf';
                $revision++;
            }

            // Simpan file
            $pdf->save($directory . $newFilename);

            // Get user email based on roles
            $notification = Notifications::where('name', 'like', '%bids%')->first();
            if ($notification) {
                $roles = json_decode($notification->roles, true) ?? [];
                $users = User::with('roles')->whereHas('roles', function ($query) use ($roles) {
                    $query->whereIn('name', $roles);
                })->get();

                // Kirim email array cc
                $ccEmails = $users->pluck('email')->toArray();

                // Kirim email
                Mail::to(config('mail.from.address'))
                    ->cc($ccEmails)
                    ->send(new CostbidsNotificationEmail($costbid, $newFilename));
            }
        } catch (\Exception $e) {
            // Log error jika pengiriman email gagal
            Log::error('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Approve Costbids Analysis
     */
    public function approved($id)
    {
        DB::beginTransaction();
        try {
            $costbid = CostBids::find($id);
            $costbid->update([
                'status' => 'Approved',
                'approved_by' => auth()->user()->id,
                'approved_at' => now()
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Cost Bids ' . $costbid->code . ' Approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject Costbids Analysis
     */
    public function rejected(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            $costbid = CostBids::find($id);
            $costbid->update([
                'status' => 'Rejected',
                'rejected_by' => auth()->user()->id,
                'reason' => $request->reason,
                'rejected_at' => now()
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Cost Bids ' . $costbid->code . ' Rejected successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
