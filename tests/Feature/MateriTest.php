<?php

use App\Enums\CourseStatus;
use App\Livewire\Admin\Materi\MateriData;
use App\Livewire\Admin\Materi\MateriDetail;
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
    $this->peserta = User::factory()->peserta()->create();

    // Permanent course (Curriculum always open)
    $this->permanentCourse = Course::create([
        'slug' => 'digital-leadership-ai-untuk-asn',
        'title' => 'Digital Leadership & AI untuk ASN',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => 'published',
        'created_by' => $this->admin->id,
    ]);

    // Batch course that has started (Curriculum frozen)
    $this->frozenBatchCourse = Course::create([
        'slug' => 'manajemen-perubahan-asn-angkatan-i',
        'title' => 'Manajemen Perubahan ASN Angkatan I',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'start_date' => now()->subDays(3)->toDateString(),
        'end_date' => now()->addDays(7)->toDateString(),
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

test('materi.editor route redirects to materi.detail with editor view query parameter', function () {
    $response = $this->actingAs($this->admin)->get(route('materi.editor', $this->permanentCourse->id));
    $response->assertRedirect(route('materi.detail', [
        'id' => $this->permanentCourse->id,
        'view' => 'editor',
    ]));
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

    // Edit Lesson via MateriDetail
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->call('openEditLesson', $lesson->id)
        ->assertSet('lessonForm.title', 'Materi Awal')
        ->set('lessonForm.title', 'Materi Hasil Pembaruan')
        ->set('lessonForm.version', 'Versi 1.1')
        ->set('lessonForm.body_text', '<p>Konten baru diperbarui</p>')
        ->call('saveLesson')
        ->assertDispatched('alert-show');

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
        ->test(MateriDetail::class, ['id' => $this->frozenBatchCourse->id])
        ->call('openCreateLesson', 1)
        ->assertDispatched('alert-show')
        ->call('saveLesson')
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
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->call('openCreateLesson', $chapter->id)
        ->set('lessonForm.title', '')
        ->set('lessonForm.body_text', '<p>Konten materi</p>')
        ->call('saveLesson')
        ->assertHasErrors(['lessonForm.title' => 'required'])
        ->assertDispatched('alert-show');

    // Validation error when title is too short (< 3 chars)
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->call('openCreateLesson', $chapter->id)
        ->set('lessonForm.title', 'ab')
        ->set('lessonForm.body_text', '<p>Konten materi</p>')
        ->call('saveLesson')
        ->assertHasErrors(['lessonForm.title' => 'min'])
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

test('admin can switch to inline editor, create lesson and return to silabus without page reload', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 1: Fondasi BerAKHLAK',
        'order' => 1,
    ]);

    $component = Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->assertSet('viewMode', 'silabus')
        ->assertSee('Struktur Bab')
        ->call('openCreateLesson', $chapter->id)
        ->assertSet('viewMode', 'editor')
        ->assertSet('editorChapterId', $chapter->id)
        ->assertSet('lessonForm.chapter_id', $chapter->id)
        ->assertSet('lessonForm.order', 1)
        ->assertSee('Tambah Materi Baru')
        ->set('lessonForm.title', 'Modul Inline: Kepemimpinan ASN Modern')
        ->set('lessonForm.body_text', '<p>Konten materi disusun via inline component editor.</p>')
        ->call('saveLesson')
        ->assertDispatched('alert-show')
        ->assertSet('viewMode', 'silabus')
        ->assertSee('Modul Inline: Kepemimpinan ASN Modern');

    $this->assertDatabaseHas('lessons', [
        'chapter_id' => $chapter->id,
        'title' => 'Modul Inline: Kepemimpinan ASN Modern',
        'order' => 1,
        'body_text' => '<p>Konten materi disusun via inline component editor.</p>',
    ]);
});

test('admin can switch to inline editor, edit lesson and return to silabus without page reload', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 2: Implementasi Layanan Publik',
        'order' => 2,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Judul Sebelum Edit',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Teks sebelum revisi</p>',
        'version' => 'Versi 1.0',
    ]);

    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->assertSet('viewMode', 'silabus')
        ->call('openEditLesson', $lesson->id)
        ->assertSet('viewMode', 'editor')
        ->assertSet('editorLessonId', $lesson->id)
        ->assertSet('lessonForm.title', 'Judul Sebelum Edit')
        ->assertSee('Edit Materi Pembelajaran')
        ->set('lessonForm.title', 'Judul Sesudah Revisi Cepat')
        ->set('lessonForm.body_text', '<p>Teks sesudah revisi cepat tanpa reload page.</p>')
        ->call('saveLesson')
        ->assertDispatched('alert-show')
        ->assertSet('viewMode', 'silabus')
        ->assertSee('Judul Sesudah Revisi Cepat');

    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Judul Sesudah Revisi Cepat',
        'body_text' => '<p>Teks sesudah revisi cepat tanpa reload page.</p>',
    ]);
});

test('inline editor validates title and body in MateriDetail', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 3: Etika Birokrasi',
        'order' => 3,
    ]);

    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        ->call('openCreateLesson', $chapter->id)
        ->set('lessonForm.title', '')
        ->set('lessonForm.body_text', '')
        ->call('saveLesson')
        ->assertHasErrors(['lessonForm.title' => 'required'])
        ->assertDispatched('alert-show');
});

test('switching between silabus and editor resets validation error bag completely', function () {
    $chapter = Chapter::create([
        'course_id' => $this->permanentCourse->id,
        'title' => 'Bab 4: Integritas Pelayanan',
        'order' => 4,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Validasi Bersih',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten siap edit</p>',
        'version' => 'Versi 1.0',
    ]);

    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $this->permanentCourse->id])
        // 1. Open create and trigger validation error
        ->call('openCreateLesson', $chapter->id)
        ->set('lessonForm.title', '')
        ->call('saveLesson')
        ->assertHasErrors(['lessonForm.title' => 'required'])
        // 2. Return to silabus
        ->call('closeEditor')
        ->assertSet('viewMode', 'silabus')
        ->assertHasNoErrors()
        // 3. Open edit lesson -> Error bag must be completely clear!
        ->call('openEditLesson', $lesson->id)
        ->assertSet('viewMode', 'editor')
        ->assertSet('lessonForm.title', 'Materi Validasi Bersih')
        ->assertHasNoErrors()
        // 4. Return to silabus and open create again -> Error bag must still be clear!
        ->call('closeEditor')
        ->call('openCreateLesson', $chapter->id)
        ->assertHasNoErrors();
});

test('admin can publish draft course directly from materi detail curriculum page', function () {
    $draftCourse = Course::create([
        'title' => 'Pelatihan Draft Belum Terbit',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Draft,
        'created_by' => $this->admin->id,
    ]);

    expect($draftCourse->isDraft())->toBeTrue();

    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, ['id' => $draftCourse->id])
        ->assertSee('Terbitkan Kelas')
        ->assertSee(route('kelas.data'))
        ->assertSee(route('kelas.edit', $draftCourse->id))
        ->call('publishCourse')
        ->assertDispatched('alert-show');

    expect($draftCourse->fresh()->status)->toBe(CourseStatus::Published);
    expect($draftCourse->fresh()->isPublished())->toBeTrue();
});
