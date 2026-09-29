<?php

use App\Livewire\Admin\Notifikasi\NotifikasiIndex;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can access notifikasi history page and view their audit logs', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    AuditLog::log(
        action: 'category.created',
        newValues: ['name' => 'Tata Kelola'],
        notes: 'Admin menambahkan kategori baru: Tata Kelola',
        userId: $admin->id
    );

    AuditLog::log(
        action: 'course.created',
        newValues: ['title' => 'Pelatihan Kepemimpinan'],
        notes: 'Admin menambahkan kelas baru: Pelatihan Kepemimpinan',
        userId: $admin->id
    );

    $this->actingAs($admin)
        ->get(route('admin.notifikasi'))
        ->assertOk()
        ->assertSee('Riwayat Aktivitas & Notifikasi')
        ->assertSee('Tata Kelola')
        ->assertSee('Pelatihan Kepemimpinan');
});

test('admin can search and filter notifikasi by module and view detail modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $categoryLog = AuditLog::log(
        action: 'category.created',
        newValues: ['name' => 'Kategori Khusus'],
        notes: 'Catatan Kategori Khusus',
        userId: $admin->id
    );

    AuditLog::log(
        action: 'course.created',
        newValues: ['title' => 'Kelas Spesifik'],
        notes: 'Catatan Kelas Spesifik',
        userId: $admin->id
    );

    $this->actingAs($admin);

    Livewire::test(NotifikasiIndex::class)
        ->assertSee('Kategori Khusus')
        ->assertSee('Kelas Spesifik')
        // Filter by module
        ->set('module', 'kategori')
        ->assertSee('Kategori Khusus')
        ->assertDontSee('Kelas Spesifik')
        // Filter by search
        ->set('module', 'all')
        ->set('search', 'Kelas Spesifik')
        ->assertSee('Kelas Spesifik')
        ->assertDontSee('Kategori Khusus')
        // View detail modal
        ->call('showDetail', $categoryLog->id)
        ->assertSee('Detail Riwayat Audit #'.$categoryLog->id)
        ->assertSee('Kategori Khusus')
        ->call('closeDetail')
        ->assertDontSee('Detail Riwayat Audit #'.$categoryLog->id);
});

test('guest cannot access admin notifikasi page', function () {
    $this->get(route('admin.notifikasi'))
        ->assertRedirect(route('login'));
});
