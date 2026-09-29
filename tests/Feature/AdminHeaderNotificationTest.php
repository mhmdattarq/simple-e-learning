<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin header displays unread notification badge and activities', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    // Create an audit log for this admin
    AuditLog::log(
        action: 'category.created',
        newValues: ['name' => 'Teknologi Informasi'],
        notes: 'Admin menambahkan kategori baru: Teknologi Informasi',
        userId: $admin->id
    );

    $this->actingAs($admin);

    Livewire::test('admin.header')
        ->assertSee('Aktivitas Saya')
        ->assertSee('1 Baru')
        ->assertSee('Kategori Ditambahkan')
        ->assertSee('Teknologi Informasi')
        ->call('markAsRead')
        ->assertDontSee('1 Baru');
});
