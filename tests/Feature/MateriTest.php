<?php

use App\Livewire\Admin\Materi\MateriData;
use App\Livewire\Admin\Materi\MateriDetail;
use App\Livewire\Admin\Materi\MateriEditor;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
    $component = Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->assertSet('isFrozen', false)
        ->call('openCreateChapter')
        ->assertSet('showChapterModal', true)
        ->set('chapterForm.title', 'Bab 1: Pengawasan Kinerja ASN')
        ->set('chapterForm.order', 1)
        ->call('saveChapter')
        ->assertDispatched('alert-show')
        ->assertSet('showChapterModal', false);

    $this->assertDatabaseHas('chapters', [
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 1: Pengawasan Kinerja ASN',
        'order' => 1,
    ]);

    $chapter = Chapter::where('course_id', $this->permanentCourse->id)->first();

    // Edit Chapter
    $component->call('openEditChapter', $chapter->id)
        ->assertSet('chapterForm.title', 'Bab 1: Pengawasan Kinerja ASN')
        ->set('chapterForm.title', 'Bab 1: Pengawasan & Akuntabilitas ASN')
        ->call('saveChapter')
        ->assertDispatched('alert-show');

    $this->assertDatabaseHas('chapters', [
        'id' => $chapter->id,
        'title' => 'Bab 1: Pengawasan & Akuntabilitas ASN',
    ]);

    // Delete Chapter
    $component->call('deleteChapter', $chapter->id)
        ->assertDispatched('alert-show');

    $this->assertDatabaseMissing('chapters', [
        'id' => $chapter->id,
    ]);
});

test('admin can open dedicated form editor and save lesson to database', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 1: Fondasi BerAKHLAK',
        'order' => 1,
    ]);

    $response = $this->actingAs($this->admin)->get(route('materi.editor', $this->permanentCourse->id));
    $response->assertOk();
    $response->assertSee('Tambah Materi Baru');
    $response->assertSee('Tambah Materi');

    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, ['course_id' => $this->permanentCourse->id])
        ->set('lesson.chapter_id', $chapter->id)
        ->set('lesson.title', 'Modul Utama: Kebijakan Transformasi Birokrasi ASN')
        ->set('lesson.order', 1)
        ->set('lesson.content_type', 'article')
        ->set('lesson.version', 'Versi 1.0')
        ->set('lesson.body_text', '<p>Teks materi pembelajaran ASN</p>')
        ->call('save')
        ->assertSessionHas('alert-show')
        ->assertRedirect(route('materi.detail', $this->permanentCourse->id));

    $this->assertDatabaseHas('lessons', [
        'chapter_id' => $chapter->id,
        'title' => 'Modul Utama: Kebijakan Transformasi Birokrasi ASN',
        'order' => 1,
        'content_type' => 'article',
        'version' => 'Versi 1.0',
    ]);
});

test('admin can edit and delete lesson in database', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 1: Modul Dasar',
        'order' => 1,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Awal',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten lama</p>',
        'version' => 'Versi 1.0',
    ]);

    // Edit Lesson via MateriEditor
    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, [
            'course_id' => $this->permanentCourse->id,
            'lesson_id' => $lesson->id,
        ])
        ->assertSet('lesson.title', 'Materi Awal')
        ->set('lesson.title', 'Materi Hasil Pembaruan')
        ->set('lesson.version', 'Versi 1.1')
        ->set('lesson.body_text', '<p>Konten baru diperbarui</p>')
        ->call('save')
        ->assertSessionHas('alert-show')
        ->assertRedirect(route('materi.detail', $this->permanentCourse->id));

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Materi Hasil Pembaruan',
        'version' => 'Versi 1.1',
    ]);

    // Delete Lesson via MateriDetail
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->call('deleteLesson', $chapter->id, $lesson->id)
        ->assertDispatched('alert-show');

    $this->assertDatabaseMissing('lessons', [
        'id' => $lesson->id,
    ]);
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

test('admin can upload media and document attachments via upload-media endpoint', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('slide_modul_lanjutan.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($this->admin)->post(route('materi.upload-media'), [
        'file' => $file,
    ]);

    $response->assertOk();
    $response->assertJson([
        'success' => true,
        'filename' => 'slide_modul_lanjutan.pdf',
        'extension' => 'pdf',
    ]);

    Storage::disk('public')->assertExists($response->json('path'));
});

test('upload-media rejects oversized image and document with 422 status', function () {
    Storage::fake('public');

    // 3 MB image (> 2 MB)
    $oversizedImage = UploadedFile::fake()->create('large_banner.png', 3072, 'image/png');
    $resImage = $this->actingAs($this->admin)->postJson(route('materi.upload-media'), [
        'file' => $oversizedImage,
    ]);
    $resImage->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Ukuran gambar melebihi batas maksimal (Maksimal 2 MB).',
        ]);

    // 12 MB document (> 10 MB)
    $oversizedDoc = UploadedFile::fake()->create('huge_slide.pdf', 12288, 'application/pdf');
    $resDoc = $this->actingAs($this->admin)->postJson(route('materi.upload-media'), [
        'file' => $oversizedDoc,
    ]);
    $resDoc->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Ukuran dokumen melebihi batas maksimal (Maksimal 10 MB).',
        ]);
});

test('materi editor validates title with min 3 chars and dispatches toast alert-show', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 1: Fondasi',
        'order' => 1,
    ]);

    // Validation error when title is empty
    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, [
            'course_id' => $this->permanentCourse->id,
            'chapter_id' => $chapter->id,
        ])
        ->set('lesson.title', '')
        ->set('lesson.body_text', '<p>Konten materi</p>')
        ->call('save')
        ->assertHasErrors(['lesson.title' => 'required'])
        ->assertDispatched('alert-show');

    // Validation error when title is too short (< 3 chars)
    Livewire::actingAs($this->admin)
        ->test(MateriEditor::class, [
            'course_id' => $this->permanentCourse->id,
            'chapter_id' => $chapter->id,
        ])
        ->set('lesson.title', 'ab')
        ->set('lesson.body_text', '<p>Konten materi</p>')
        ->call('save')
        ->assertHasErrors(['lesson.title' => 'min'])
        ->assertDispatched('alert-show');
});

test('chapter and lesson deletion via reusable modal hooks', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab Uji Hapus',
        'order' => 1,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Uji Hapus',
        'order' => 1,
        'content_type' => 'article',
        'version' => 'Versi 1.0',
    ]);

    $component = Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id]);

    // Test hook modal delete chapter
    $component->call('hookModalDeleteChapter', $chapter->id, $chapter->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->assertDispatched('showModal');

    // Test hook modal delete lesson
    $component->call('hookModalDeleteLesson', $lesson->id, $lesson->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->assertDispatched('showModal');

    // Execute delete via listener
    $component->dispatch('MateriDetail-deleteLesson', ['id' => $lesson->id])
        ->assertDispatched('closeModal')
        ->assertDispatched('alert-show');

    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);

    $component->dispatch('MateriDetail-deleteChapter', ['id' => $chapter->id])
        ->assertDispatched('closeModal')
        ->assertDispatched('alert-show');

    $this->assertDatabaseMissing('chapters', ['id' => $chapter->id]);
});
