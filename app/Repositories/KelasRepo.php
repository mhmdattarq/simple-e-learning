<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KelasRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(): Builder
    {
        return Course::query()
            ->with(['category', 'creator'])
            ->withCount(['chapters', 'lessons', 'registrations']);
    }

    /**
     * Find course by ID.
     */
    public static function getById($id): Course
    {
        return Course::with(['category', 'creator'])->findOrFail($id);
    }

    /**
     * Create a new course.
     */
    public static function create(array $data): ?Course
    {
        try {
            if (empty($data['slug']) && ! empty($data['title'])) {
                $slug = Str::slug($data['title']);
                $originalSlug = $slug;
                $count = 1;
                while (Course::where('slug', $slug)->exists()) {
                    $slug = $originalSlug.'-'.$count++;
                }
                $data['slug'] = $slug;
            }

            return Course::create($data);
        } catch (\Exception $e) {
            Log::error('Insert data kelas gagal', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update existing course.
     */
    public static function update($id, array $data): bool
    {
        try {
            $course = self::getById($id);

            if (! empty($data['title']) && empty($data['slug'])) {
                $slug = Str::slug($data['title']);
                $originalSlug = $slug;
                $count = 1;
                while (Course::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug.'-'.$count++;
                }
                $data['slug'] = $slug;
            }

            // Handle file replacement cleanup if thumbnail or tor_file changed
            if (isset($data['thumbnail']) && $course->thumbnail && $data['thumbnail'] !== $course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            if (isset($data['tor_file']) && $course->tor_file && $data['tor_file'] !== $course->tor_file) {
                Storage::disk('public')->delete($course->tor_file);
            }

            $course->update($data);

            return true;
        } catch (\Exception $e) {
            Log::error('Update data kelas gagal', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Check if a course can be deleted (only if no students are registered).
     */
    public static function canBeDeleted(Course $course): bool
    {
        return $course->registrations()->count() === 0;
    }

    /**
     * Soft-delete course and cascade soft-delete chapters and lessons.
     * Note: File assets are preserved on soft-delete to retain history and integrity.
     */
    public static function delete($id): bool
    {
        try {
            $course = self::getById($id);

            if (! self::canBeDeleted($course)) {
                Log::warning('Hapus kelas ditolak karena sudah memiliki peserta terdaftar', ['id' => $id]);

                return false;
            }

            foreach ($course->chapters as $chapter) {
                $chapter->lessons()->delete();
                $chapter->delete();
            }

            $course->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Delete data kelas gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Submit draft course to leadership (Ajukan ke Pimpinan).
     */
    public static function submitToLeader(int|string $id): bool
    {
        try {
            $course = self::getById($id);
            if ($course->status !== CourseStatus::Draft) {
                return false;
            }
            $course->update(['status' => CourseStatus::Submitted]);

            return true;
        } catch (\Exception $e) {
            Log::error('Ajukan kelas ke pimpinan gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Arsipkan kelas (Diarsipkan / Archived).
     */
    public static function archiveCourse(int|string $id): bool
    {
        try {
            $course = self::getById($id);
            $course->update(['status' => CourseStatus::Archived]);

            return true;
        } catch (\Exception $e) {
            Log::error('Arsipkan kelas gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Resolve category ID from name string (case-insensitive find or create) or numeric ID.
     */
    public static function resolveCategoryId(int|string $categoryInput): int
    {
        if (is_numeric($categoryInput) && $cat = Category::find($categoryInput)) {
            return $cat->id;
        }

        $categoryName = trim((string) $categoryInput);
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();

        if (! $category) {
            $baseSlug = Str::slug($categoryName) ?: 'kategori';
            $slug = $baseSlug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$count++;
            }

            $category = Category::create([
                'name' => $categoryName,
                'slug' => $slug,
            ]);
        }

        return $category->id;
    }
}
