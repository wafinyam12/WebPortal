<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\ToolsTransaction;
use App\Models\ToolsShipments;
use App\Models\Tools;
use App\Models\Projects;

class ToolsTransactionController extends Controller
{
    /**
     * Create a new controller instance. 
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view tools transactions', ['only' => ['index']]);
        $this->middleware('permission:show tools transactions', ['only' => ['show']]);
        $this->middleware('permission:print tools transactions', ['only' => ['generateDN']]);
        $this->middleware('permission:create tools transactions', ['only' => ['create', 'store']]);
        $this->middleware('permission:update tools transactions', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete tools transactions', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = ToolsTransaction::select('id', 'document_code', 'delivery_date', 'type', 'status')->get();
        return view('tools.transaction.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource from backend.
     */
    public function create()
    {
        $projects = Projects::all();
        return view('tools.transaction.create', compact('projects'));
    }

    /**
     * Show the form for creating a new resource from backend. (fail frontend)
     */
    public function createFrontend()
    {
        $projects = Projects::all();
        return view('tools.transaction.createfront', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the form data
        $request->validate([
            'type_delivery' => 'required|string|in:Delivery Note,Transfer,Return',
            'driver_name' => 'required|string|max:255',
            'source_project_id' => 'required|exists:projects,id',
            'driver_phone' => 'required|string|max:15',
            'delivery_date' => 'required|date',
            'destination_project_id' => 'required|exists:projects,id|different:source_project_id',
            'notes' => 'nullable|string|max:1000',
            'tools' => 'required|array',
            'tools.*' => 'required|json',
        ]);

        // Generate kode transaksi
        $typeMap = [
            'Delivery Note' => 'DN',
            'Transfer' => 'TR',
            'Return' => 'RT'
        ];

        $romanMonth = [
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
            'December' => 'XII'
        ];

        // Generate code based on type and month
        $transactionCount = ToolsTransaction::where('type', $request->type_delivery)
            ->count() + 1;
        $code = $typeMap[$request->type_delivery] . '/' . date('Y') . '/' . $romanMonth[date('F')] . '/' . date('d') . '/' . str_pad($transactionCount, 4, '0', STR_PAD_LEFT);

        // get user id from frontend (fail if from frontend)
        // $user = User::whereHas('employe', function ($query) use ($request) {
        //     $query->where('code', $request->codeEmploye);
        // })->with('employe')->firstOrFail();

        // Start database transaction
        DB::beginTransaction();
        try {

            $deliveryNote = ToolsTransaction::create([
                'user_id' => auth()->user()->id,
                'source_project_id' => $request->source_project_id,
                'destination_project_id' => $request->destination_project_id,
                'document_code' => $code,
                'document_date' => date('Y-m-d'),
                'delivery_date' => $request->delivery_date,
                'ppic' => auth()->user()->full_name,
                'driver' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                // 'transportation' => $request->transportation, (next)
                // 'plat_number' => $request->plat_number, (next)
                'status' => 'In Progress',
                'type' => $request->type_delivery,
                'notes' => $request->notes,
            ]);

            // Loop dan simpan setiap tools
            foreach ($request->tools as $tool) {
                $toolData = json_decode($tool, true);

                if (!isset($toolData['code'], $toolData['location'])) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Invalid tool data.');
                }

                $toolCode = Tools::where('code', $toolData['code'])->first();

                if (!$toolCode) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Tool code ' . $toolData['code'] . ' not found.');
                }

                if ($toolCode->status == 'Maintenance' || $toolCode->status == 'Inactive' || $toolCode->condition == 'Broken') {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Tool code ' . $toolData['code'] . ' is in maintenance.');
                }

                ToolsShipments::create([
                    'transactions_id' => $deliveryNote->id,
                    'tool_id' => $toolCode->id,
                    'quantity' => $toolCode->quantity,
                    'unit' => $toolCode->unit,
                    'last_location' => $toolData['location'],
                ]);

                // Update kondisi tool
                if ($request->type_delivery === 'Return') {
                    $toolCode->update(['condition' => 'New']);
                } else {
                    $toolCode->update(['condition' => 'Used']);
                }
            }
            DB::commit();
            return redirect()->back()->with('success', 'Transaction ' . $code . ' created successfully.');
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
        $deliveryNote = ToolsTransaction::with(['user', 'tools.tool', 'sourceTransactions', 'destinationTransactions'])->findOrFail($id);
        return view('tools.transaction.show', compact('deliveryNote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $projects = Projects::all();
        $deliveryNote = ToolsTransaction::with(['user', 'tools.tool', 'sourceTransactions', 'destinationTransactions'])->findOrFail($id);
        return view('tools.transaction.edit', compact('deliveryNote', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data input
        $request->validate([
            'type_delivery' => 'required|string|in:Delivery Note,Transfer,Return',
            'driver_name' => 'required|string|max:255',
            'source_project_id' => 'required|exists:projects,id',
            'driver_phone' => 'required|string|max:15',
            'delivery_date' => 'required|date',
            'destination_project_id' => 'required|exists:projects,id|different:source_project_id',
            'notes' => 'nullable|string|max:1000',
            'tools' => 'required|array',
            'tools.*' => 'required'
        ]);

        DB::beginTransaction();
        try {
            // Ambil data transaksi utama berdasarkan ID
            $deliveryNote = ToolsTransaction::findOrFail($id);

            // Update transaksi utama
            $deliveryNote->update([
                'user_id' => auth()->user()->id,
                'source_project_id' => $request->source_project_id,
                'destination_project_id' => $request->destination_project_id,
                'delivery_date' => $request->delivery_date,
                'ppic' => auth()->user()->full_name,
                'driver' => $request->driver_name,
                'driver_phone' => $request->driver_phone,
                'status' => 'In Progress',
                'type' => $request->type_delivery,
                'notes' => $request->notes,
            ]);

            // Kumpulkan tool codes dari request
            $newToolCodes = [];
            foreach ($request->tools as $toolData) {
                $toolInfo = json_decode($toolData, true);
                $newToolCodes[] = $toolInfo['code'];
            }

            // Hapus items yang tidak ada di request
            ToolsShipments::where('transactions_id', $deliveryNote->id)
                ->whereHas('tool', function ($query) use ($newToolCodes) {
                    $query->whereNotIn('code', $newToolCodes);
                })
                ->delete();

            // Update atau create item baru
            foreach ($request->tools as $toolData) {
                $toolInfo = json_decode($toolData, true);

                // Cari tool berdasarkan code
                $tool = Tools::where('code', $toolInfo['code'])->first();
                if (!$tool) {
                    return redirect()->back()->with('error', 'Tool with code ' . $toolInfo['code'] . ' not found.');
                }

                if ($tool->status == 'Maintenance' || $tool->status == 'Inactive' || $tool->condition == 'Broken') {
                    return redirect()->back()->with('error', 'Tool code ' . $toolInfo['code'] . ' is in maintenance.');
                }

                ToolsShipments::updateOrCreate(
                    [
                        'transactions_id' => $deliveryNote->id,
                        'tool_id' => $tool->id,
                        'quantity' => $tool->quantity,
                        'unit' => $tool->unit,
                        'last_location' => $toolInfo['location']
                    ],
                );
            }

            // Update kondisi tool
            foreach ($request->tools as $toolData) {
                $toolInfo = json_decode($toolData, true);
                $toolCode = Tools::where('code', $toolInfo['code'])->first();
                if ($request->type_delivery === 'Return') {
                    $toolCode->update(['condition' => 'New']);
                } else {
                    $toolCode->update(['condition' => 'Used']);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transaction ' . $deliveryNote->document_code . ' updated successfully.');
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
            $deliveryNote = ToolsTransaction::findOrFail($id);
            $getItemDeliveryNote = ToolsShipments::where('transactions_id', $deliveryNote->id)->get();
            foreach ($getItemDeliveryNote as $item) {
                $item->delete();
            }
            $deliveryNote->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Transaction ' . $deliveryNote->document_code . ' deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error',  $e->getMessage());
        }
    }

    /**
     * Generate Delivery Note
     */
    public function generateDN(String $id)
    {
        $deliveryNote = ToolsTransaction::with(['user', 'tools.tool', 'sourceTransactions', 'destinationTransactions'])->findOrFail($id);
        $qrcodeData = "Document Code: " . $deliveryNote->document_code . "\n" .
            "Document Date: " . $deliveryNote->document_date . "\n" .
            "Created By : " . $deliveryNote->user->fullName . "\n";
        $qrcode = QrCode::generate($qrcodeData);
        $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($qrcode);

        $pdf = Pdf::loadView('tools.transaction.dn', compact('deliveryNote', 'qrCodeDataUri'));

        // Return the PDF stream
        if ($deliveryNote->first()->type === 'Delivery Note') {
            return $pdf->stream('Delivery-Note-' . date('d-m-Y') . '.pdf');
        }

        return $pdf->stream('Return-Note-' . date('d-m-Y') . '.pdf');
    }
}
