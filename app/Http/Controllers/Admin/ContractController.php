<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;

class ContractController extends Controller
{
    /**
     * Display the contract details page.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function viewContract($id)
    {
        $contract = Contract::with(['tenant.dues', 'apartment.building'])->findOrFail($id);

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
