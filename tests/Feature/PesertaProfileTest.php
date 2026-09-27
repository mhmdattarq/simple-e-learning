<?php

use App\Livewire\Peserta\Profile\ProfileIndex;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
});

test('guest cannot access peserta profile page and is redirected to login', function () {
    $response = $this->get(route('peserta.profil'));
    $response->assertRedirect(route('login'));
});

test('authenticated google user can access profile page and see incomplete status warning', function () {
    $user = User::factory()->peserta()->create([
        'google_id' => 'google-12345',
        'nip' => null,
        'opd_agency' => null,
        'position' => null,
        'rank_class' => null,
    ]);

    expect($user->isAsnProfileComplete())->toBeFalse();

    $response = $this->actingAs($user)->get(route('peserta.profil'));
    $response->assertStatus(200);
    $response->assertSee('Profil ASN Belum Lengkap');
    $response->assertSee('Data Kepegawaian ASN');
    $response->assertSee('Google Linked');
});

test('user can update and complete their asn profile with audit logging', function () {
    $user = User::factory()->peserta()->create([
        'name' => 'Fauzan Akbar',
        'email' => 'fauzan@gmail.com',
        'nip' => null,
        'opd_agency' => null,
        'position' => null,
        'rank_class' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', 'Fauzan Akbar, S.STP')
        ->set('form.nip', '199508172020121002')
        ->set('form.phone_number', '081234567890')
        ->set('form.opd_agency', 'Badan Kepegawaian dan Pengembangan SDM')
        ->set('form.position', 'Pranata Komputer Ahli Pertama')
        ->set('form.rank_class', 'Penata Muda - III/a')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil kepegawaian ASN Anda berhasil disimpan dan diperbarui.');

    $user->refresh();
    expect($user->isAsnProfileComplete())->toBeTrue();
    expect($user->nip)->toBe('199508172020121002');
    expect($user->opd_agency)->toBe('Badan Kepegawaian dan Pengembangan SDM');

    // Audit log recorded
    $audit = AuditLog::where('action', 'user.profile_updated')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->new_values['nip'])->toBe('199508172020121002');
});

test('profile validation rejects non-18 digit nip and duplicate nip', function () {
    $existingUser = User::factory()->peserta()->create([
        'nip' => '199001012015011001',
    ]);

    $currentUser = User::factory()->peserta()->create([
        'nip' => null,
    ]);

    // 1. Invalid nip length
    Livewire::actingAs($currentUser)
        ->test(ProfileIndex::class)
        ->set('form.nip', '12345')
        ->call('save')
        ->assertHasErrors(['form.nip']);

    // 2. Duplicate nip belonging to another user
    Livewire::actingAs($currentUser)
        ->test(ProfileIndex::class)
        ->set('form.nip', '199001012015011001')
        ->call('save')
        ->assertHasErrors(['form.nip']);
});

test('navbar renders profile link and completion indicator for authenticated user', function () {
    $incompleteUser = User::factory()->peserta()->create([
        'nip' => null,
    ]);

    $response = $this->actingAs($incompleteUser)->get(route('landing'));
    $response->assertStatus(200);
    $response->assertSee(route('peserta.profil'));
    $response->assertSee('Profil Saya');
    $response->assertSee('Lengkapi Data ASN Anda');
});
