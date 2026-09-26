<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PerencanaanRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(): Builder
    {
        return Course::query()->with(['category', 'creator']);
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
    public static function create(array $data): bool
    {
        try {
            Course::create($data);

            return true;
        } catch (\Exception $e) {
            Log::error('Insert data perencanaan diklat gagal', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update existing course (Only allowed if status is Draft).
     */
    public static function update($id, array $data): bool
    {
        try {
            $course = self::getById($id);

            // Guard: Hanya status Draft yang boleh diedit
            if ($course->status !== CourseStatus::Draft) {
                return false;
            }

            // Do not allow manual status overwrite from update payload
            unset($data['status']);

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
            Log::error('Update data perencanaan diklat gagal', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete course and associated files (Only allowed if status is Draft).
     */
    public static function delete($id): bool
    {
        try {
            $course = self::getById($id);

            // Guard: Hanya status Draft yang boleh dihapus
            if ($course->status !== CourseStatus::Draft) {
                return false;
            }

            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            if ($course->tor_file) {
                Storage::disk('public')->delete($course->tor_file);
            }

            $course->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Delete data perencanaan diklat gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate standard course code: PLT-YYYY-XXX
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $prefix = "PLT-{$year}-";

        $lastCourse = Course::where('code', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $lastCourse) {
            return "{$prefix}001";
        }

        $lastNumber = (int) substr($lastCourse->code, strlen($prefix));
        $nextNumber = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);

        return "{$prefix}{$nextNumber}";
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
            Log::error('Ajukan perencanaan diklat ke pimpinan gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Arsipkan pelatihan (Diarsipkan / Archived).
     */
    public static function archiveCourse(int|string $id): bool
    {
        try {
            $course = self::getById($id);
            $course->update(['status' => CourseStatus::Archived]);

            return true;
        } catch (\Exception $e) {
            Log::error('Arsipkan pelatihan gagal', [
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
