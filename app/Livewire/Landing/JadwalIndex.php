<?php

namespace App\Livewire\Landing;

use App\Models\Course;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Jadwal Kelas – SIMPEL E-Learning BKPSDM Aceh Timur')]
class JadwalIndex extends Component
{
    public function render()
    {
        $jadwals = Schema::hasTable('courses')
            ? Course::with('category')
                ->where('status', 'published')
                ->where('type', 'batch')
                ->whereNotNull('start_date')
                ->orderBy('start_date', 'asc')
                ->get()
            : collect();

        return view('mods.landing.jadwal-index', compact('jadwals'));
    }
}
