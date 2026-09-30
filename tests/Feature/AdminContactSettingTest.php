<?php

use App\Livewire\Admin\Kontak\KontakSettingData;
use App\Models\ContactFaq;
use App\Models\ContactSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('non-admin cannot access contact settings page', function () {
    $peserta = User::factory()->create(['role' => 'peserta']);

    $this->actingAs($peserta)
        ->get(route('kontak.setting.data'))
        ->assertForbidden();
});

test('admin can access contact settings page and view data', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('kontak.setting.data'))
        ->assertOk()
        ->assertSee('Pengaturan Kontak &amp; FAQ', false)
        ->assertSee('Informasi Kantor &amp; Kontak', false)
        ->assertSee('Kantor BKPSDM');
});

test('admin can update contact information settings', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);

    Livewire::test(KontakSettingData::class)
        ->set('office_title', 'Kantor Pusat BKPSDM Aceh Timur')
        ->set('address', 'Jl. Merdeka No. 100, Idi Rayeuk, Aceh Timur')
        ->set('whatsapp_number', '085299990000')
        ->set('whatsapp_label', 'Hotline Kepegawaian')
        ->set('email', 'helpdesk@acehtimurkab.go.id')
        ->set('service_days', 'Senin – Kamis')
        ->set('service_hours', '08.00 – 16.00 WIB')
        ->call('saveSettings')
        ->assertDispatched('alert-show');

    $setting = ContactSetting::getSettings();
    expect($setting->office_title)->toBe('Kantor Pusat BKPSDM Aceh Timur');
    expect($setting->whatsapp_number)->toBe('085299990000');
    expect($setting->whatsapp_label)->toBe('Hotline Kepegawaian');
    expect($setting->email)->toBe('helpdesk@acehtimurkab.go.id');
});

test('admin can create, update, toggle and delete FAQ items', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin);

    // 1. Create FAQ
    Livewire::test(KontakSettingData::class)
        ->call('openCreateFaqModal')
        ->assertDispatched('openModal', id: 'modalFaqForm')
        ->set('faqQuestion', 'Bagaimana cara mendapatkan sertifikat diklat?')
        ->set('faqAnswer', 'Sertifikat otomatis terbit pada menu profil setelah Anda lulus ujian akhir.')
        ->set('faqOrder', 10)
        ->set('faqIsActive', true)
        ->call('saveFaq')
        ->assertDispatched('closeModal', id: 'modalFaqForm')
        ->assertDispatched('alert-show');

    $faq = ContactFaq::where('question', 'Bagaimana cara mendapatkan sertifikat diklat?')->first();
    expect($faq)->not->toBeNull();
    expect($faq->order)->toBe(10);
    expect($faq->is_active)->toBeTrue();

    // 2. Edit FAQ
    Livewire::test(KontakSettingData::class)
        ->call('openEditFaqModal', $faq->id)
        ->assertDispatched('openModal', id: 'modalFaqForm')
        ->assertSet('faqId', $faq->id)
        ->set('faqQuestion', 'Bagaimana cara mendapatkan e-Sertifikat diklat?')
        ->call('saveFaq')
        ->assertDispatched('closeModal', id: 'modalFaqForm');

    $faq->refresh();
    expect($faq->question)->toBe('Bagaimana cara mendapatkan e-Sertifikat diklat?');

    // 3. Toggle Status Aktif
    Livewire::test(KontakSettingData::class)
        ->call('toggleFaqActive', $faq->id)
        ->assertDispatched('alert-show');

    $faq->refresh();
    expect($faq->is_active)->toBeFalse();

    // 4. Delete FAQ
    Livewire::test(KontakSettingData::class)
        ->call('deleteFaq', $faq->id)
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show');

    expect(ContactFaq::find($faq->id))->toBeNull();
});

test('admin can save maps embed url even when pasting full iframe tag', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    $iframeInput = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d953.7263242693963!2d101.42209344018!3d1.6798056380491941!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d3a95e566ab08b%3A0x7947abf61d84eda9!2sthrift.bro!5e1!3m2!1sen!2ssg!4v1790772112838!5m2!1sen!2ssg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';

    Livewire::test(KontakSettingData::class)
        ->set('maps_embed_url', $iframeInput)
        ->call('saveSettings')
        ->assertDispatched('alert-show');

    $setting = ContactSetting::getSettings();
    expect($setting->maps_embed_url)->toBe('https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d953.7263242693963!2d101.42209344018!3d1.6798056380491941!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d3a95e566ab08b%3A0x7947abf61d84eda9!2sthrift.bro!5e1!3m2!1sen!2ssg!4v1790772112838!5m2!1sen!2ssg');
});
