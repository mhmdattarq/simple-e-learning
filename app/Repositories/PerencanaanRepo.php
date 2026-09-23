<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Update existing course.
     */
    public static function update($id, array $data): bool
    {
        try {
            $course = self::getById($id);

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
     * Delete course and associated files.
     */
    public static function delete($id): bool
    {
        try {
            $course = self::getById($id);

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
            $course->update(['status' => 'submitted']);

            return true;
        } catch (\Exception $e) {
            Log::error('Ajukan perencanaan diklat ke pimpinan gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
