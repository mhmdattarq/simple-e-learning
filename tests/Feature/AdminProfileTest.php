<?php

use App\Livewire\Admin\Profile\AdminProfileIndex;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin can access profile page and view current data', function () {
    $admin = User::factory()->create([
        'name' => 'Fauzan Administrator',
        'email' => 'fauzan@acehtimurkab.go.id',
        'phone_number' => '081298765432',
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.profil'))
        ->assertOk()
        ->assertSee('Profil Administrator')
        ->assertSee('Fauzan Administrator')
        ->assertSee('fauzan@acehtimurkab.go.id')
        ->assertSee('081298765432');
});

test('admin can update name, email, and phone number successfully', function () {
    $admin = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@acehtimurkab.go.id',
        'phone_number' => '08111111111',
        'role' => 'admin',
    ]);

    $this->actingAs($admin);

    Livewire::test(AdminProfileIndex::class)
        ->set('form.name', 'New Admin Name')
        ->set('form.email', 'newadmin@acehtimurkab.go.id')
        ->set('form.phone_number', '08222222222')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil admin Anda berhasil disimpan dan diperbarui.');

    $admin->refresh();

    expect($admin->name)->toBe('New Admin Name');
    expect($admin->email)->toBe('newadmin@acehtimurkab.go.id');
    expect($admin->phone_number)->toBe('08222222222');

    // Verify AuditLog was recorded
    $log = AuditLog::where('action', 'user.profile_updated')
        ->where('user_id', $admin->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->new_values['name'])->toBe('New Admin Name');
    expect($log->new_values['email'])->toBe('newadmin@acehtimurkab.go.id');
});

test('admin can update password with confirmation', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    // Mismatched confirmation should fail
    Livewire::test(AdminProfileIndex::class)
        ->set('form.password', 'new-secret-password')
        ->set('form.password_confirmation', 'different-password')
        ->call('save')
        ->assertHasErrors(['form.password' => 'confirmed']);

    // Matching confirmation should pass
    Livewire::test(AdminProfileIndex::class)
        ->set('form.password', 'new-secret-password')
        ->set('form.password_confirmation', 'new-secret-password')
        ->call('save')
        ->assertHasNoErrors();

    $admin->refresh();
    expect(Hash::check('new-secret-password', $admin->password))->toBeTrue();
});

test('admin cannot use another user email', function () {
    User::factory()->create(['email' => 'existing@acehtimurkab.go.id']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);

    Livewire::test(AdminProfileIndex::class)
        ->set('form.email', 'existing@acehtimurkab.go.id')
        ->call('save')
        ->assertHasErrors(['form.email' => 'unique']);
});

test('guest cannot access admin profile page', function () {
    $this->get(route('admin.profil'))
        ->assertRedirect(route('login'));
});
