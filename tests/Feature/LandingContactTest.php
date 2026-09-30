<?php

use App\Livewire\Landing\KontakIndex;
use App\Models\AuditLog;
use App\Models\ContactFaq;
use App\Models\ContactMessage;
use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('guest sees empty state when contact information is not configured', function () {
    $this->get(route('kontak'))
        ->assertOk()
        ->assertSee('Hubungi Kami -')
        ->assertSee('Informasi Kontak Belum Tersedia')
        ->assertDontSee('Formulir Kontak');
});

test('contact form requires valid inputs', function () {
    ContactSetting::getSettings()->update([
        'email' => 'admin@acehtimurkab.go.id',
    ]);

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
    ContactSetting::getSettings()->update([
        'email' => 'admin@acehtimurkab.go.id',
    ]);

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

    $msg = ContactMessage::where('email', 'budi@example.com')->first();
    expect($msg)->not->toBeNull();
    expect($msg->name)->toBe('Budi Santoso');
    expect($msg->status)->toBe('unread');
    expect($msg->phone)->toBe('081234567890');
});

test('contact page loads dynamic settings and active faqs', function () {
    ContactSetting::getSettings()->update([
        'office_title' => 'Kantor Pusat BKPSDM Aceh Timur',
        'whatsapp_number' => '081122334455',
    ]);

    ContactFaq::create([
        'question' => 'Apakah ada sertifikat elektronik?',
        'answer' => 'Ya, sertifikat digital diterbitkan langsung setelah lulus.',
        'order' => 1,
        'is_active' => true,
    ]);

    $this->get(route('kontak'))
        ->assertOk()
        ->assertDontSee('Informasi Kontak Belum Tersedia')
        ->assertSee('Kantor Pusat BKPSDM Aceh Timur')
        ->assertSee('081122334455')
        ->assertSee('Apakah ada sertifikat elektronik?')
        ->assertSee('Ya, sertifikat digital diterbitkan langsung setelah lulus.');
});

test('authenticated user has contact info prefilled and sends audit log', function () {
    ContactSetting::getSettings()->update([
        'email' => 'admin@acehtimurkab.go.id',
    ]);

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
