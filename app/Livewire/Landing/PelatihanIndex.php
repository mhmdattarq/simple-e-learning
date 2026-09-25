<?php

namespace App\Livewire\Landing;

use App\Models\Course;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Katalog Pelatihan – SIMPEL E-Learning BKPSDM Aceh Timur')]
class PelatihanIndex extends Component
{
    public function render()
    {
        $courses = Schema::hasTable('courses')
            ? Course::with('category')
                ->where('status', 'published')
                ->latest('id')
                ->get()
            : collect();

        return view('mods.landing.pelatihan-index', compact('courses'));
    }
}
