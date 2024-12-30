<?php

namespace App\Http\Livewire\Admin\Buildings;

use App\Models\Apartment;
use App\Models\Building;
use Livewire\Component;

class CreateBuilding extends Component
{
    public $open = false;

    public $address = [];
    public $number;
    public $floors;
    public $apartments_on_floor;
    public $basement = false;
    public $executive_table;
    public $executive_chair;
    public $guest_chair;
    public $staff_workstations;
    public $staff_chairs;
    public $cabinet;

    protected $listeners = [
        'openCreateBuildingModal' => 'openModal',
        'closeCreateBuildingModal' => 'closeModal',
    ];

    protected $rules = [
        'address' => 'required|array',
        'executive_table' => 'nullable|string|max:255',
        'executive_chair' => 'nullable|string|max:255',
        'guest_chair' => 'nullable|string|max:255',
        'staff_workstations' => 'nullable|string|max:255',
        'staff_chairs' => 'nullable|string|max:255',
        'cabinet' => 'nullable|string|max:255',
    ];

    public function openModal()
    {
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
        $this->resetValidation();
        $this->reset(["address", "number", "floors", "apartments_on_floor", "basement", "executive_table", "executive_chair", "staff_workstations", "staff_chairs", "cabinet",]);
    }

    public function storeBuilding()
    {
        $this->validate([
            "address.en" => "required|max:255|string",
            "address.*" => "nullable|max:255|string",
            "number" => "required|string",
            "floors" => "required|min:1|integer",
            "apartments_on_floor" => "required|min:1|integer",
            "basement" => "nullable|boolean"
        ]);

        $building = Building::create([
            "address" => [
                'en' => $this->address['en'],
                'ar' => $this->address['ar'] ?? $this->address['en'],
            ],
            "number" => $this->number,
            "executive_table" => $this->executive_table,
            "executive_chair" => $this->executive_chair,
            "guest_chair" => $this->guest_chair,
            "staff_workstations" => $this->staff_workstations,
            "staff_chairs" => $this->staff_chairs,
            "cabinet" => $this->cabinet,
        ]);

        $apartments = [];

        if ($this->basement) {
                $apartments[] = new Apartment([
                'floor' => 0,
                'number' => 1,
            ]);
        }

        foreach (range(1, $this->floors) as $floor) {
            foreach (range(1, $this->apartments_on_floor) as $apartment) {
                $apartments[] = new Apartment([
                    'floor' => $floor,
                    'number' => $apartment,
                ]);
            }
        }

        $building->apartments()->saveMany($apartments);

        $this->closeModal();
        $this->emit("success", __('messages.building_created'));
    }

    public function render()
    {
        return view('livewire-components.admin.buildings.create-building');
    }
}