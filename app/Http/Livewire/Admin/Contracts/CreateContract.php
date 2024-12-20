<?php

namespace App\Http\Livewire\Admin\Contracts;

use Livewire\Component;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Apartment;

class CreateContract extends Component
{
    public $open = false;

    public $buildings = [];
    public $floors = [];
    public $apartments = [];

    public $tenant_id;
    public $company;
    public $start_date;
    public $duration;
    public $rent_amount;

    public $building_id;
    public $floor_no;
    public $apartment_id;
    public $landlord_name;
    public $land_location;
    public $tenant_name;
    public $location;
    public $trade_license;
    public $nationality;
    public $ejari;
    public $eid_no;
    public $contact_no;




    protected $listeners = [
        'openCreateContractModal' => 'openModal',
        'closeCreateContractModal' => 'closeModal',
    ];

    public function openModal($tenant_id)
    {
        $this->tenant_id = $tenant_id;
        $this->buildings = Building::all();
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
        $this->resetValidation();
        $this->reset([
            "buildings",
            "floors",
            "apartments",
            "tenant_id",
            "start_date",
            "duration",
            "apartment_id",
            "building_id",
            "floor_no",
            "landlord_name",
            "land_location",
            "tenant_name",
            "location",
            "trade_license",
            "nationality",
            "ejari",
            "contact_no",
        ]);
    }

    public function updatedBuildingId()
    {
        $this->validate([
            'building_id' => 'required|integer|exists:buildings,id',
        ]);

        $this->floor_no = null;
        $this->floors = range(1, Apartment::where('building_id', $this->building_id)->max('floor'));
    }

    public function updatedFloorNo()
    {
        $this->apartment_id = null;
        $this->apartments = Apartment::where('building_id', $this->building_id)
            ->where('floor', $this->floor_no)
            ->get();
    }

    public function storeDue()
    {
        $this->validate([
            "start_date" => "required|date",
            "duration" => "required|max:50|numeric",
            "company" => 'nullable|string|max:255',
            "rent_amount" => "required|numeric",
            "building_id" => "required|integer|exists:buildings,id",
            "floor_no" => "required|integer",
            "apartment_id" => "required|integer|exists:apartments,id",
            "tenant_id" => "required|integer|exists:tenants,id",
            'landlord_name' => 'nullable|string|max:255',
            'land_location' => 'nullable|string|max:255',
            "tenant_name" => "nullable|string|max:255",
            "location" => "nullable|string|max:255",
            "trade_license" => "nullable|string|max:255",
            "nationality" => "nullable|string|max:255",
            "eid_no" => "nullable|string|max:255",
            "ejari" => "integer",
            "contact_no" => "nullable|string|max:15 ",
        ]);

        Contract::create([
            "start_date" => $this->start_date,
            "duration" => $this->duration,
            "rent_amount" => $this->rent_amount,
            "apartment_id" => $this->apartment_id,
            "tenant_id" => $this->tenant_id,
            "company" => $this->company,
            "landlord_name" => $this->landlord_name,
            "land_location" => $this->land_location,
            "tenant_name" => $this->tenant_name,
            "location" => $this->location,
            "trade_license" => $this->trade_license,
            "nationality" => $this->nationality,
            "eid_no" => $this->eid_no,
            "ejari" => $this->ejari,
            "contact_no" => $this->contact_no,
            
        ]);

        $this->closeModal();
        $this->emit("success", __('messages.contract_created'));
    }

    public function render()
    {
        return view('livewire-components.admin.contracts.create-contract');
    }
}
