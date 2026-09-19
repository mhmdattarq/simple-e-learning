<?php

namespace App\Livewire\Landing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('SIMPEL E-Learning - Portal Pelatihan Digital ASN & Aparatur')]
class LandingIndex extends Component
{
    public string $searchQuery = '';

    public string $selectedCategory = 'all';

    public function filterCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function render()
    {
        return view('mods.landing.landing-index');
    }
}
