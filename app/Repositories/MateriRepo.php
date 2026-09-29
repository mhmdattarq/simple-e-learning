<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Models\Chapter;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MateriRepo
{
    /**
     * Mengambil struktur kurikulum lengkap (Bab dan Unit Materi) untuk suatu kelas.
     */
    public static function getCurriculumByCourse(int $courseId): Collection
    {
        return Chapter::query()
            ->where('course_id', $courseId)
            ->with(['lessons' => function ($query) {
                $query->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Mengambil daftar bab sederhana untuk dropdown pada form editor materi.
     */
    public static function getChaptersList(int $courseId): Collection
    {
        return Chapter::query()
            ->where('course_id', $courseId)
            ->orderBy('order', 'asc')
            ->get(['id', 'title', 'order', 'course_id']);
    }

    /**
     * Mengambil satu data bab berdasarkan ID.
     */
    public static function getChapterById(int $id): ?Chapter
    {
        return Chapter::with('course')->find($id);
    }

    /**
     * Menyimpan bab baru ke dalam kurikulum.
     */
    public static function createChapter(array $data): ?Chapter
    {
        try {
            if (empty($data['order'])) {
                $maxOrder = Chapter::where('course_id', $data['course_id'])->max('order') ?? 0;
                $data['order'] = $maxOrder + 1;
            }

            $chapter = Chapter::create([
                'course_id' => $data['course_id'],
                'title' => trim($data['title']),
                'order' => (int) $data['order'],
            ]);

            AuditLog::log(
                action: 'chapter.created',
                auditable: $chapter,
                newValues: ['title' => $chapter->title, 'course_id' => $chapter->course_id],
                notes: 'Admin menambahkan bab materi: '.$chapter->title
            );

            return $chapter;
        } catch (\Exception $e) {
            Log::error('Insert data bab kurikulum materi gagal', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Memperbarui bab kurikulum.
     */
    public static function updateChapter(int $id, array $data): bool
    {
        try {
            $chapter = Chapter::findOrFail($id);
            $chapter->update([
                'title' => trim($data['title']),
                'order' => (int) $data['order'],
            ]);

            AuditLog::log(
                action: 'chapter.updated',
                auditable: $chapter,
                newValues: ['title' => $chapter->title],
                notes: 'Admin mengubah bab materi: '.$chapter->title
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Update data bab kurikulum materi gagal', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Menghapus bab beserta seluruh materi di dalamnya (cascade).
     */
    public static function deleteChapter(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $chapter = Chapter::with('lessons')->findOrFail($id);

                // Bersihkan attachment materi jika ada
                foreach ($chapter->lessons as $lesson) {
                    if ($lesson->attachment_path && Storage::disk('public')->exists($lesson->attachment_path)) {
                        Storage::disk('public')->delete($lesson->attachment_path);
                    }
                }

                AuditLog::log(
                    action: 'chapter.deleted',
                    auditable: $chapter,
                    oldValues: ['title' => $chapter->title, 'course_id' => $chapter->course_id],
                    notes: 'Admin menghapus bab materi: '.$chapter->title
                );

                $chapter->delete();

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Hapus data bab kurikulum materi gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mengambil satu data materi pembelajaran berdasarkan ID.
     */
    public static function getLessonById(int $id): ?Lesson
    {
        return Lesson::with(['chapter.course'])->find($id);
    }

    /**
     * Menyimpan unit materi pembelajaran baru.
     */
    public static function createLesson(array $data): ?Lesson
    {
        try {
            if (empty($data['order'])) {
                $maxOrder = Lesson::where('chapter_id', $data['chapter_id'])->max('order') ?? 0;
                $data['order'] = $maxOrder + 1;
            }

            $lesson = Lesson::create([
                'chapter_id' => $data['chapter_id'],
                'title' => trim($data['title']),
                'order' => (int) $data['order'],
                'content_type' => $data['content_type'] ?? 'article',
                'video_url' => ! empty($data['video_url']) ? trim($data['video_url']) : null,
                'body_text' => $data['body_text'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'version' => ! empty($data['version']) ? trim($data['version']) : 'Versi 1.0',
                'version_notes' => ! empty($data['version_notes']) ? trim($data['version_notes']) : null,
            ]);

            AuditLog::log(
                action: 'lesson.created',
                auditable: $lesson,
                newValues: [
                    'title' => $lesson->title,
                    'chapter_id' => $lesson->chapter_id,
                    'content_type' => $lesson->content_type,
                ],
                notes: 'Admin menambahkan materi: '.$lesson->title
            );

            return $lesson;
        } catch (\Exception $e) {
            Log::error('Insert unit materi pembelajaran gagal', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Memperbarui unit materi pembelajaran.
     */
    public static function updateLesson(int $id, array $data): bool
    {
        try {
            $lesson = Lesson::findOrFail($id);

            $oldValues = [
                'title' => $lesson->title,
                'chapter_id' => $lesson->chapter_id,
                'content_type' => $lesson->content_type,
            ];

            // Bersihkan file lampiran lama jika ada file pengganti
            if (isset($data['attachment_path']) && $lesson->attachment_path && $data['attachment_path'] !== $lesson->attachment_path) {
                if (Storage::disk('public')->exists($lesson->attachment_path)) {
                    Storage::disk('public')->delete($lesson->attachment_path);
                }
            }

            $lesson->update([
                'chapter_id' => $data['chapter_id'] ?? $lesson->chapter_id,
                'title' => trim($data['title']),
                'order' => (int) $data['order'],
                'content_type' => $data['content_type'] ?? $lesson->content_type,
                'video_url' => ! empty($data['video_url']) ? trim($data['video_url']) : null,
                'body_text' => $data['body_text'] ?? $lesson->body_text,
                'attachment_path' => array_key_exists('attachment_path', $data) ? $data['attachment_path'] : $lesson->attachment_path,
                'version' => ! empty($data['version']) ? trim($data['version']) : 'Versi 1.0',
                'version_notes' => ! empty($data['version_notes']) ? trim($data['version_notes']) : null,
            ]);

            AuditLog::log(
                action: 'lesson.updated',
                auditable: $lesson,
                oldValues: $oldValues,
                newValues: [
                    'title' => $lesson->title,
                    'chapter_id' => $lesson->chapter_id,
                    'content_type' => $lesson->content_type,
                ],
                notes: 'Admin memperbarui materi: '.$lesson->title
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Update unit materi pembelajaran gagal', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Menghapus unit materi pembelajaran.
     */
    public static function deleteLesson(int $id): bool
    {
        try {
            $lesson = Lesson::findOrFail($id);

            AuditLog::log(
                action: 'lesson.deleted',
                auditable: $lesson,
                oldValues: ['title' => $lesson->title, 'chapter_id' => $lesson->chapter_id],
                notes: 'Admin menghapus materi: '.$lesson->title
            );

            if ($lesson->attachment_path && Storage::disk('public')->exists($lesson->attachment_path)) {
                Storage::disk('public')->delete($lesson->attachment_path);
            }

            $lesson->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Hapus unit materi pembelajaran gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
