<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
   public $metrics = [];

    public function mount()
    {
        $this->metrics = [
            'total_reports' => 120,
            'rescued_animals' => 85,
            'adopted_animals' => 40,
            'pending_cases' => 12,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
