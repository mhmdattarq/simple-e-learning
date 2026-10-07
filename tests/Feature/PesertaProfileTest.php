<?php

use App\Livewire\Auth\Login;
use App\Livewire\Peserta\Profile\ProfileIndex;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        'phone_number' => null,
        'address' => null,
    ]);

    expect($user->isProfileComplete())->toBeFalse();

    $response = $this->actingAs($user)->get(route('peserta.profil'));
    $response->assertStatus(200);
    $response->assertSee('Profil Belum Lengkap');
    $response->assertSee('Data Profil Peserta');
    $response->assertSee('Google Linked');
});

test('user can update and complete their profile with audit logging', function () {
    $user = User::factory()->peserta()->create([
        'name' => 'Fauzan Akbar',
        'email' => 'fauzan@gmail.com',
        'phone_number' => null,
        'address' => null,
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', 'Fauzan Akbar, S.Kom')
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Jl. Medan - B. Aceh No. 12, Idi Rayeuk')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil Anda berhasil disimpan dan diperbarui.');

    $user->refresh();
    expect($user->isProfileComplete())->toBeTrue();
    expect($user->name)->toBe('Fauzan Akbar, S.Kom');
    expect($user->phone_number)->toBe('081234567890');
    expect($user->address)->toBe('Jl. Medan - B. Aceh No. 12, Idi Rayeuk');
    expect(Hash::check('password123', $user->password))->toBeTrue();

    // Audit log recorded
    $audit = AuditLog::where('action', 'user.profile_updated')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->new_values['phone_number'])->toBe('081234567890');
    expect($audit->new_values['address'])->toBe('Jl. Medan - B. Aceh No. 12, Idi Rayeuk');
});

test('profile validation requires name, phone number, address, and password', function () {
    $currentUser = User::factory()->peserta()->create([
        'phone_number' => null,
        'address' => null,
    ]);

    Livewire::actingAs($currentUser)
        ->test(ProfileIndex::class)
        ->set('form.name', '')
        ->set('form.phone_number', '')
        ->set('form.address', '')
        ->set('form.password', '')
        ->set('form.password_confirmation', '')
        ->call('save')
        ->assertHasErrors(['form.name', 'form.phone_number', 'form.address', 'form.password']);
});

test('navbar renders profile link and completion indicator for authenticated user', function () {
    $incompleteUser = User::factory()->peserta()->create([
        'phone_number' => null,
        'address' => null,
    ]);

    $response = $this->actingAs($incompleteUser)->get(route('landing'));
    $response->assertStatus(200);
    $response->assertSee(route('peserta.profil'));
    $response->assertSee('Profil Saya');
    $response->assertSee('Lengkapi Profil Anda');
});

test('profile page displays quiz evaluation history for authenticated participant', function () {
    $user = User::factory()->peserta()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $quiz = Quiz::factory()->finalQuiz()->create([
        'course_id' => $course->id,
        'title' => 'Ujian Akhir Spesialis ASN',
        'created_by' => $admin->id,
    ]);

    QuizAttempt::factory()->create([
        'quiz_id' => $quiz->id,
        'user_id' => $user->id,
        'total_earned_score' => 85,
        'total_possible_score' => 100,
        'percentage' => 85.0,
        'is_passed' => true,
    ]);

    $response = $this->actingAs($user)->get(route('peserta.profil'));
    $response->assertStatus(200);
    $response->assertSee('Riwayat Evaluasi &amp; Kuis', false);
    $response->assertSee('Ujian Akhir Spesialis ASN');
    $response->assertSee('85');
    $response->assertSee('100');
    $response->assertSee('85.0%');
    $response->assertSee('Lulus');
});

test('user can set password from profile and subsequently login with email and new password', function () {
    $user = User::factory()->peserta()->create([
        'name' => 'Peserta Google',
        'email' => 'peserta.google@gmail.com',
        'google_id' => 'google-999',
        'phone_number' => '081234567890',
        'address' => 'Banda Aceh',
        'password' => Hash::make(Str::random(32)),
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', 'Peserta Google')
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Banda Aceh')
        ->set('form.password', 'passwordbaru123')
        ->set('form.password_confirmation', 'passwordbaru123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil Anda berhasil disimpan dan diperbarui.');

    $user->refresh();
    expect(Hash::check('passwordbaru123', $user->password))->toBeTrue();

    // Logout and verify manual login works with new password
    auth()->logout();
    $this->assertGuest();

    Livewire::test(Login::class)
        ->set('identifier', 'peserta.google@gmail.com')
        ->set('password', 'passwordbaru123')
        ->call('authenticate')
        ->assertRedirect(route('landing'));

    $this->assertAuthenticatedAs($user);
});

test('profile validation enforces password requirement', function () {
    $user = User::factory()->peserta()->create([
        'name' => 'Nama Lama',
        'email' => 'peserta@gmail.com',
        'phone_number' => '081234567890',
        'address' => 'Banda Aceh',
        'password' => Hash::make('passwordlama123'),
    ]);

    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', 'Nama Baru')
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Langsa')
        ->set('form.password', '')
        ->set('form.password_confirmation', '')
        ->call('save')
        ->assertHasErrors(['form.password'])
        ->assertSee('Kata sandi wajib diisi.');
});

test('profile password validation enforces min 8 chars and matching confirmation', function () {
    $user = User::factory()->peserta()->create();

    // 1. Less than 8 characters
    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', $user->name)
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Alamat')
        ->set('form.password', '12345')
        ->set('form.password_confirmation', '12345')
        ->call('save')
        ->assertHasErrors(['form.password'])
        ->assertSee('Kata sandi minimal 8 karakter.');

    // 2. Mismatched confirmation
    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', $user->name)
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Alamat')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'different123')
        ->call('save')
        ->assertHasErrors(['form.password'])
        ->assertSee('Konfirmasi kata sandi tidak cocok.');
});

test('user can update nip in profile and use it to authenticate', function () {
    $user = User::factory()->peserta()->create([
        'nip' => null,
    ]);

    // Validation fails if not 18 digits
    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.nip', '12345')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('save')
        ->assertHasErrors(['form.nip'])
        ->assertSee('NIP harus berjumlah 18 digit angka.');

    // Successful update
    Livewire::actingAs($user)
        ->test(ProfileIndex::class)
        ->set('form.name', $user->name)
        ->set('form.nip', '199501012022011001')
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Alamat Lengkap')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSee('Profil Anda berhasil disimpan dan diperbarui.');

    $user->refresh();
    expect($user->nip)->toBe('199501012022011001');
});
