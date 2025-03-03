<?php

namespace App\Http\Controllers\BussinesPartner;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SAPServices;

class BussinesMasterController extends Controller
{
    protected $sapServices;
    /**
     * Create a new controller instance.
     */
    public function __construct(SAPServices $sapServices)
    {
        $this->middleware('auth');
        $this->sapServices = $sapServices;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->sapServices->connect();

        // Siapkan parameter untuk filter, limit, dan sorting
        $params = [
            '$select'  => 'CardCode,CardName,CardType,CreditLimit',
            '$filter'  => "startswith(CardCode, 'C00') and CardType eq 'C'",
            // '$orderby' => 'CardCode asc',
            '$orderby' => 'CardCode desc',
        ];

        // Gunakan Cache::remember agar data disimpan selama 5 menit
        $bussinesMasters = Cache::remember('bussinesMasters', now()->addMinutes(5), function () use ($params) {
            return $this->sapServices->get('BusinessPartners', $params);
        });

        // Cache::forget('bussinesMasters');
        // Hapus cache jika tombol "Refresh" ditekan
        // if ($request->has('refresh')) {
        //     Cache::forget('bussinesMasters');
        //      return redirect()->route('bussines-master.index');
        // }

        // Kirim data ke view
        return view('bussinesPartner.bussinesMaster.index', compact('bussinesMasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->sapServices->connect();

        // Grup bisnis
        $paramsBussinesGroups = [
            '$select'  => 'Code,Name,Type',
        ];
        $bussinesGroups = $this->sapServices->get('BusinessPartnerGroups', $paramsBussinesGroups);

        // Properti bisnis
        $paramsBussinesProperties = [
            '$select'  => 'PropertyCode,PropertyName',
        ];
        $bussinesProperties = $this->sapServices->get('BusinessPartnerProperties');

        // Sales person
        $paramsSalesPerson = [
            '$select'  => 'SalesEmployeeCode,SalesEmployeeName',
        ];
        $salesPersons = $this->sapServices->get('SalesPersons', $paramsSalesPerson);

        // Country
        $paramsCountry = [
            '$select'  => 'Code,Name',
        ];
        $countries = $this->sapServices->get('Countries', $paramsCountry);

        // Currency
        $paramsCurrency = [
            '$select'  => 'Code,Name,DocumentsCode,InternationalDescription',
        ];
        $currencies = $this->sapServices->get('Currencies', $paramsCurrency);


        $cust = $this->sapServices->get('CustomsGroups');
        return $cust;

        return view('bussinesPartner.bussinesMaster.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->sapServices->connect();

        // request validation
        $request->validate([
            'CardCode' => 'required',
            'CardName' => 'required',
            'CardType' => 'required',
            'CreditLimit' => 'required',
        ]);

        try {
            $data = [
                'CardCode' => $request->CardCode,
                'CardName' => $request->CardName,
                'CardType' => $request->CardType,
                'CreditLimit' => $request->CreditLimit,
                'BPAddresses' => [
                    "AddressName" => "BILL TO",
                    'Address' => $request->BPAddresses,
                ],
                'BPAddresses' => [
                    "AddressName" => "SHIP TO",
                    'Address' => $request->BPAddresses,
                ],
                'ContactEmployees' => [],
            ];

            $this->sapServices->post('BusinessPartners', $data);
            return redirect()->route('bussines-master.index')->with('success', 'Bussines Partner created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->sapServices->connect();

        $params = [
            '$select' => 'CardCode,CardName,CardType,CreditLimit,BPAddresses,ContactEmployees,BPWithholdingTaxCollection',
        ];

        $bussinesMasters = $this->sapServices->getById('BusinessPartners', $id, $params);

        return view('bussinesPartner.bussinesMaster.show', compact('bussinesMasters'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->sapServices->connect();

        $params = [
            '$select' => 'CardCode,CardName,CardType,CreditLimit,BPAddresses,ContactEmployees,BPWithholdingTaxCollection',
        ];

        $bussinesMasters = $this->sapServices->getById('BusinessPartners', $id, $params);

        return view('bussinesPartner.bussinesMaster.edit', compact('bussinesMasters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->sapServices->connect();

        // request validation
        $request->validate([
            'CardCode' => 'required',
            'CardName' => 'required',
            'CardType' => 'required',
            'CreditLimit' => 'required',
            'BPAddresses' => 'required',
            'ContactEmployees' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'CardCode' => $request->CardCode,
                'CardName' => $request->CardName,
                'CardType' => $request->CardType,
                'CreditLimit' => $request->CreditLimit,
                'BPAddresses' => $request->BPAddresses,
                'ContactEmployees' => $request->ContactEmployees,
            ];

            $bussinesMasters = $this->sapServices->getById('BusinessPartners', $id);

            if ($bussinesMasters) {
                $this->sapServices->put('BusinessPartners(' . $bussinesMasters['CardCode'] . ')', $data);
                DB::commit();
                return redirect()->route('bussines-master.index')->with('success', 'Bussines Partner updated successfully.');
            }
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
        //
    }
}
