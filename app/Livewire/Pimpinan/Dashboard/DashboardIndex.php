<?php

namespace App\Livewire\Pimpinan\Dashboard;

use App\Enums\Role;
use Livewire\Component;

class DashboardIndex extends Component
{
    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, [Role::Pimpinan, Role::Admin], true)) {
            abort(403, 'Akses terbatas untuk peran Pimpinan.');
        }
    }

    public function render()
    {
        return view('mods.pimpinan.dashboard.dashboard-index');
    }
}
