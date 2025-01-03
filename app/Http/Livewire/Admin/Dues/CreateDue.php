<?php

namespace App\Http\Livewire\Admin\Dues;

use App\Models\Tenant;
use App\Models\Due;
use App\Models\DueCategory;
use Livewire\Component;

class CreateDue extends Component
{
    // public $amount = 0;
    // public $discount = 0;
    public $open = false;

    public $tenants;
    public $dues_categories;

    public $tenant_id;
    public $due_category_id;
    public $amount;
    public $paid_amount;
    public $discount;
    public $note;
    public $payment_method;

    protected $listeners = [
        'openCreateDueModal' => 'openModal',
        'closeCreateDueModal' => 'closeModal',
    ];

    protected $rules = [
        'note' => 'required|array',
    ];

    public function mount()
    {
        $this->tenants = Tenant::all();
        $this->dues_categories = DueCategory::all();
    }

    public function openModal($tenant_id = null)
    {
        $this->tenant_id = $tenant_id;
        $this->amount = 0.00;
        $this->paid_amount = 0.00;
        $this->discount = 0.00;
        $this->payment_method = '';
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
        $this->resetValidation();
        $this->reset([
            "amount",
            "paid_amount",
            "discount",
            "note",
            "due_category_id",
            "tenant_id",
            "payment_method",
        ]);
    }

    public function storeDue()
    {
        $this->validate([
            "amount" => "required|numeric|min:0.1",
            "paid_amount" => "required|numeric",
            "discount" => "required|numeric",
            "note.*" => "nullable|max:255|string",
            "due_category_id" => "required|integer|exists:due_categories,id",
            "tenant_id" => "required|integer|exists:tenants,id",
            "payment_method" => "required|in:online,cash,cheque",  // Add validation rule

        ]);

        Due::create([
            "amount" => $this->amount,
            "paid_amount" => $this->paid_amount,
            "discount" => $this->discount,
            "note" => [
                'en' => $this->note['en'] ?? null,
                'ar' => $this->note['ar'] ?? ($this->note['en'] ?? null),
            ],
            "due_category_id" => $this->due_category_id,
            "tenant_id" => $this->tenant_id,
            "payment_method" => $this->payment_method,  // Add to creation array

        ]);

        $this->closeModal();
        $this->emit("success", __('messages.due_created'));
    }

    public function render()
    {
        return view('livewire-components.admin.dues.create-due');
    }
}
