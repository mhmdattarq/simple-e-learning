<?php

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Enums\Role;
use App\Livewire\Peserta\Pendaftaran\PendaftaranCreate;
use App\Livewire\Peserta\Profile\ProfileIndex;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    Storage::fake('public');
});

test('user with incomplete asn profile is redirected to profile page when accessing course registration', function () {
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-GUARD-001',
        'title' => 'Pelatihan Transformasi Digital',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => CourseStatus::Published,
    ]);

    // Google user without NIP/OPD
    $userIncomplete = User::factory()->create([
        'role' => Role::Peserta,
        'google_id' => '1234567890',
        'nip' => null,
        'opd_agency' => null,
        'position' => null,
        'rank_class' => null,
    ]);

    expect($userIncomplete->isAsnProfileComplete())->toBeFalse();

    // Direct HTTP request to course registration route
    $response = $this->actingAs($userIncomplete)->get(route('pelatihan.daftar', $course->id));

    $response->assertRedirect(route('peserta.profil'));
    $response->assertSessionHas('warning', 'Profil kepegawaian ASN Anda belum lengkap. Silakan lengkapi NIP dan data kepegawaian Anda terlebih dahulu sebelum mendaftar pelatihan.');

    // Following redirect to profile shows warning banner
    $profileResponse = $this->actingAs($userIncomplete)->get(route('peserta.profil'));
    $profileResponse->assertOk();
    $profileResponse->assertSee('Profil ASN Belum Lengkap');
});

test('livewire pendaftaran component redirects user with incomplete profile on mount', function () {
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-GUARD-002',
        'title' => 'Pelatihan Kearsipan Digital',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Published,
    ]);

    $userIncomplete = User::factory()->create([
        'role' => Role::Peserta,
        'nip' => '19900101', // Invalid length (<18)
    ]);

    Livewire::actingAs($userIncomplete)
        ->test(PendaftaranCreate::class, ['id' => $course->id])
        ->assertRedirect(route('peserta.profil'));
});

test('user can access course registration and register after completing asn profile', function () {
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-GUARD-003',
        'title' => 'Pelatihan Manajemen ASN Terpadu',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 25,
        'status' => CourseStatus::Published,
    ]);

    // Initially incomplete (e.g. registered via Google)
    $user = User::factory()->create([
        'name' => 'Faisal Reza, S.STP',
        'email' => 'faisal.reza@acehtimurkab.go.id',
        'role' => Role::Peserta,
        'google_id' => '10987654321',
        'nip' => null,
        'opd_agency' => null,
        'position' => null,
        'rank_class' => null,
    ]);

    expect($user->isAsnProfileComplete())->toBeFalse();

    // Complete profile via ProfileIndex component
    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', 'Faisal Reza, S.STP')
        ->set('form.nip', '199208152016011002')
        ->set('form.phone_number', '081234567899')
        ->set('form.opd_agency', 'Badan Pengelolaan Keuangan Daerah')
        ->set('form.position', 'Analis Keuangan Pusat dan Daerah')
        ->set('form.rank_class', 'Penata (III/c)')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->isAsnProfileComplete())->toBeTrue();

    // Now access course registration page - should succeed
    $response = $this->actingAs($user)->get(route('pelatihan.daftar', $course->id));
    $response->assertOk();
    $response->assertSee('Formulir Pendaftaran Pelatihan ASN');

    // Submit registration with fake letter
    $fakePdf = UploadedFile::fake()->create('rekomendasi.pdf', 300, 'application/pdf');

    Livewire::actingAs($user)
        ->test(PendaftaranCreate::class, ['id' => $course->id])
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('registrationSuccess', true);

    // Verify record in database
    expect(CourseUser::where('user_id', $user->id)->where('course_id', $course->id)->exists())->toBeTrue();
    $record = CourseUser::where('user_id', $user->id)->where('course_id', $course->id)->first();
    expect($record->status)->toBe(RegistrationStatus::Pending);
});
