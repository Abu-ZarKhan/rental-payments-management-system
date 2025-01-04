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
    
    return view('livewire-components.admin.contracts.contract-download', compact('contract'));
}

}