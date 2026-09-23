<?php

use App\Livewire\Admin\Materi\MateriData;
use App\Livewire\Admin\Materi\MateriDetail;
use App\Livewire\Admin\Materi\MateriEditor;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->category = Category::first();

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create();
    $this->peserta = User::factory()->peserta()->create();

    // Permanent course (Curriculum always open)
    $this->permanentCourse = Course::create([
        'code' => 'TIK-2026-PERM',
        'title' => 'Digital Leadership & AI untuk ASN',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 50,
        'status' => 'published',
        'created_by' => $this->admin->id,
    ]);

    // Batch course that has started (Curriculum frozen)
    $this->frozenBatchCourse = Course::create([
        'code' => 'TIK-2026-BATCH-FROZEN',
        'title' => 'Manajemen Perubahan ASN Angkatan I',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'start_date' => now()->subDays(3)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => 'published',
        'created_by' => $this->admin->id,
    ]);
});

test('unauthorized users cannot access materi routes', function () {
    // Guest redirected to login
    $this->get(route('materi.data'))->assertRedirect(route('login'));
    $this->get(route('materi.detail', $this->permanentCourse->id))->assertRedirect(route('login'));
    $this->get(route('materi.editor', $this->permanentCourse->id))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $this->actingAs($this->peserta)->get(route('materi.data'))->assertStatus(403);
    $this->actingAs($this->peserta)->get(route('materi.detail', $this->permanentCourse->id))->assertStatus(403);
    $this->actingAs($this->peserta)->get(route('materi.editor', $this->permanentCourse->id))->assertStatus(403);
});

test('admin can access materi catalog and curriculum detail', function () {
    $this->actingAs($this->admin)->get(route('materi.data'))
        ->assertOk()
        ->assertSee('Tahap 6: Ruang Materi')
        ->assertSee('Digital Leadership');

    Livewire::actingAs($this->admin)
        ->test(MateriData::class)
        ->assertOk()
        ->assertSee('Digital Leadership');

    $this->actingAs($this->admin)->get(route('materi.detail', $this->permanentCourse->id))
        ->assertOk()
        ->assertSee('Digital Leadership')
        ->assertSee('Alur Silabus Sekuensial');
});

test('batch course that has started activates curriculum freeze', function () {
    expect($this->frozenBatchCourse->isCurriculumFrozen())->toBeTrue();
    expect($this->permanentCourse->isCurriculumFrozen())->toBeFalse();

    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->frozenBatchCourse->id])
        ->assertSet('isFrozen', true)
        ->assertSee('Curriculum Freeze Aktif')
        ->assertSee('dikunci secara otomatis');
});

test('admin can add, edit, and delete chapter on open curriculum course', function () {
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->assertSet('isFrozen', false)
        ->call('openCreateChapter')
        ->assertSet('showChapterModal', true)
        ->set('chapterForm.title', 'Bab 3: Pengawasan Kinerja ASN')
        ->set('chapterForm.order', 3)
        ->call('saveChapter')
        ->assertDispatched('alert-show')
        ->assertSet('showChapterModal', false);
});

test('admin can open dedicated form editor and save lesson with editor', function () {
    $response = $this->actingAs($this->admin)->get(route('materi.editor', $this->permanentCourse->id));
    $response->assertOk();
    $response->assertSee('Form Konten');
    $response->assertSee('Tambah Konten');

    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, ['course_id' => $this->permanentCourse->id])
        ->set('lesson.title', 'Modul Utama: Kebijakan Transformasi Birokrasi ASN')
        ->set('lesson.chapter_id', 1)
        ->set('lesson.order', 2)
        ->set('lesson.version', 'Versi 1.0')
        ->set('lesson.body_text', '{"blocks":[{"type":"paragraph","data":{"text":"Teks materi"}}]}')
        ->call('save')
        ->assertDispatched('alert-show')
        ->assertRedirect(route('materi.detail', $this->permanentCourse->id));
});

test('frozen batch course denies adding new chapter or saving in editor', function () {
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->frozenBatchCourse->id])
        ->call('openCreateChapter')
        ->assertDispatched('alert-show')
        ->assertSet('showChapterModal', false);

    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, ['course_id' => $this->frozenBatchCourse->id])
        ->call('save')
        ->assertDispatched('alert-show');
});
