<?php

use App\Livewire\Admin\Kontak\KontakPesanData;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('non-admin cannot access admin contact messages page', function () {
    $peserta = User::factory()->create(['role' => 'peserta']);

    $this->actingAs($peserta)
        ->get(route('kontak.pesan.data'))
        ->assertForbidden();
});

test('admin can access contact messages page and see summary cards', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    ContactMessage::create([
        'name' => 'Fauzi Rahman',
        'email' => 'fauzi@example.com',
        'phone' => '081234567890',
        'subject' => 'Pendaftaran Pelatihan Teknis',
        'message' => 'Kapan pendaftaran dibuka untuk OPD luar?',
        'status' => 'unread',
    ]);

    $this->actingAs($admin)
        ->get(route('kontak.pesan.data'))
        ->assertOk()
        ->assertSee('Kotak Masuk Pesan')
        ->assertSee('Daftar Pertanyaan &amp; Pesan Masuk', false)
        ->assertSee('Total Pesan')
        ->assertSee('Belum Dibaca');
});

test('admin can fetch contact messages via datatables json endpoint', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    ContactMessage::create([
        'name' => 'Nurlina',
        'email' => 'nurlina@example.com',
        'phone' => '085211223344',
        'subject' => 'Kendala Download Sertifikat',
        'message' => 'Sertifikat saya tidak bisa diunduh di sistem.',
        'status' => 'unread',
    ]);

    $response = $this->actingAs($admin)
        ->getJson(route('kontak.pesan.dt'));

    $response->assertOk()
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);

    $data = $response->json('data');
    expect($data)->not->toBeEmpty();
    expect($data[0]['name'])->toBe('Nurlina');
    expect($data[0]['subject'])->toBe('Kendala Download Sertifikat');
});

test('admin can open message detail and it marks message as read via reusable modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $message = ContactMessage::create([
        'name' => 'Zulkifli',
        'email' => 'zulkifli@example.com',
        'phone' => '081377889900',
        'subject' => 'Pertanyaan Kuis Evaluasi',
        'message' => 'Berapa batas waktu pengerjaan ujian akhir?',
        'status' => 'unread',
    ]);

    $this->actingAs($admin);

    Livewire::test(KontakPesanData::class)
        ->call('openDetail', $message->id)
        ->assertDispatched('modal-detail-pesan-set', ['id' => $message->id]);

    Livewire::test('admin.modal')
        ->dispatch('modal-detail-pesan-set', ['id' => $message->id])
        ->assertDispatched('openModal', id: 'modalDetailPesan')
        ->assertDispatched('reloadDT')
        ->assertSee('Zulkifli')
        ->assertSee('Berapa batas waktu pengerjaan ujian akhir?');

    $message->refresh();
    expect($message->status)->toBe('read');
});

test('admin can update message status to replied via reusable modal', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $message = ContactMessage::create([
        'name' => 'Safrizal',
        'email' => 'safrizal@example.com',
        'subject' => 'Jadwal Diklat',
        'message' => 'Jadwal diklat kepemimpinan kapan ya?',
        'status' => 'read',
    ]);

    $this->actingAs($admin);

    Livewire::test('admin.modal')
        ->dispatch('modal-detail-pesan-set', ['id' => $message->id])
        ->set('pesanAdminNotes', 'Telah dihubungi via WA')
        ->call('changePesanStatus', 'replied')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT');

    $message->refresh();
    expect($message->status)->toBe('replied');
    expect($message->replied_at)->not->toBeNull();
    expect($message->admin_notes)->toBe('Telah dihubungi via WA');
});

test('admin can delete message via hook delete', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $message = ContactMessage::create([
        'name' => 'Spam Bot',
        'email' => 'spam@test.com',
        'subject' => 'Spam Message',
        'message' => 'Promo pinjaman tanpa jaminan...',
        'status' => 'unread',
    ]);

    $this->actingAs($admin);

    Livewire::test(KontakPesanData::class)
        ->call('delete', $message->id)
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT');

    expect(ContactMessage::find($message->id))->toBeNull();
});

test('admin can trigger modal delete and execute message deletion', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $message = ContactMessage::create([
        'name' => 'Spam Bot 2',
        'email' => 'spam2@test.com',
        'subject' => 'Spam Message 2',
        'message' => 'Promo...',
        'status' => 'unread',
    ]);

    $this->actingAs($admin);

    Livewire::test('admin.modal')
        ->dispatch('modal-delete-setDeleteId', [
            'data' => [
                'id' => $message->id,
                'title' => 'Konfirmasi Hapus Pesan',
                'msg' => 'Hapus pesan ini?',
                'dispatch' => 'KontakPesanData-delete',
            ],
        ])
        ->assertDispatched('openModal', id: 'modalDelete')
        ->call('process')
        ->assertDispatched('KontakPesanData-delete');

    Livewire::test(KontakPesanData::class)
        ->dispatch('KontakPesanData-delete', id: $message->id)
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT');

    expect(ContactMessage::find($message->id))->toBeNull();
});

test('deleting non-existent contact message returns friendly error without throwing exception', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin);

    Livewire::test(KontakPesanData::class)
        ->call('delete', 999999)
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show', fn ($event, $params) => $params['data']['type'] === 'danger' && str_contains($params['data']['message'], 'tidak ditemukan'))
        ->assertDispatched('reloadDT');
});
