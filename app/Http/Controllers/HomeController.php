<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use App\Models\Tools;
use App\Models\Vehicle;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $users = User::count();
        $contracts = Contract::count();
        $tools = Tools::count();
        $vehicles = Vehicle::count();

        $widget = [
            'users' => $users,
            'contracts' => $contracts,
            'tools' => $tools,
            'vehicles' => $vehicles
        ];

        return view('home', compact('widget'));
    }
}
