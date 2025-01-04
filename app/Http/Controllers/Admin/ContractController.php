<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function show($contract)
    {
        return view('admin.contracts.show', compact('contract'));
    }
    public function viewContract($id)
{
    $contract = Contract::with('tenant.dues')->findOrFail($id);
    $building = $contract->apartment->building ?? (object) [
        'executive_table' => '-',
        'executive_chair' => '-',
        'guest_chair' => '-',
        'staff_workstations' => '-',
        'staff_chairs' => '-',
        'cabinet' => '-',
        'conference_room' => '-',
        'sofa' => '-',
        'cleaning' => '-',
        'parking' => '-',
        'drinking_water' => '-',
        'electricity' => '-',
        'internet' => '-',
    ];

    $contract->building = $building;
    
    return view('livewire-components.admin.contracts.contract-download', compact('contract'));
}

}