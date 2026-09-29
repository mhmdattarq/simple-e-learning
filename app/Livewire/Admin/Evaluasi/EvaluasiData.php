<?php

namespace App\Livewire\Admin\Evaluasi;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Data Evaluasi & Kuis - SIMPEL BKPSDM')]
class EvaluasiData extends Component
{
    public function render()
    {
        return view('mods.admin.evaluasi.evaluasi-data');
    }
}
