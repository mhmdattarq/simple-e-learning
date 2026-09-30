<?php

use App\Livewire\Landing\KontakIndex;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest can access contact page and see contact information', function () {
    $this->get(route('kontak'))
        ->assertOk()
        ->assertSee('Hubungi Kami -')
        ->assertSee('Kantor BKPSDM')
        ->assertSee('Layanan WhatsApp')
        ->assertSee('Formulir Kontak');
});

test('contact form requires valid inputs', function () {
    Livewire::test(KontakIndex::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('subject', '')
        ->set('message', 'short')
        ->call('sendMessage')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'email',
            'subject' => 'required',
            'message' => 'min',
        ]);
});

test('contact form can be submitted successfully by guest', function () {
    Livewire::test(KontakIndex::class)
        ->set('name', 'Budi Santoso')
        ->set('email', 'budi@example.com')
        ->set('phone', '081234567890')
        ->set('subject', 'Informasi Pendaftaran Kelas')
        ->set('message', 'Halo, saya ingin menanyakan jadwal pendaftaran untuk kelas kepemimpinan berikutnya.')
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertSee('Pesan Anda berhasil dikirim!')
        ->assertSet('isSubmitted', true)
        ->assertSet('message', '');
});

test('authenticated user has contact info prefilled and sends audit log', function () {
    $user = User::factory()->create([
        'name' => 'Cut Meurah',
        'email' => 'cutmeurah@acehtimurkab.go.id',
        'phone_number' => '085299998888',
    ]);

    $this->actingAs($user);

    Livewire::test(KontakIndex::class)
        ->assertSet('name', 'Cut Meurah')
        ->assertSet('email', 'cutmeurah@acehtimurkab.go.id')
        ->assertSet('phone', '085299998888')
        ->set('subject', 'Kendala Akun & Login')
        ->set('message', 'Mohon bantuan untuk reset verifikasi akun saya.')
        ->call('sendMessage')
        ->assertHasNoErrors()
        ->assertSee('Pesan Anda berhasil dikirim!');

    $log = AuditLog::where('action', 'contact.message_sent')
        ->where('user_id', $user->id)
        ->latest('id')
        ->first();

    expect($log)->not->toBeNull();
    expect($log->new_values['subject'])->toBe('Kendala Akun & Login');
});

test('contact page route is rate limited after too many requests', function () {
    RateLimiter::clear('127.0.0.1');

    for ($i = 0; $i < 5; $i++) {
        $this->get(route('kontak'))->assertOk();
    }

    // 6th request within a minute triggers 429 Too Many Requests
    $this->get(route('kontak'))->assertStatus(429);
});
