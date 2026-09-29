<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KategoriRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     */
    public static function getDt(): Builder
    {
        return Category::query()->withCount('courses');
    }

    /**
     * Find category by ID.
     */
    public static function getById(int $id): Category
    {
        return Category::withCount('courses')->findOrFail($id);
    }

    /**
     * Create a new category.
     */
    public static function create(array $data): bool
    {
        try {
            $slug = Str::slug($data['name']);

            // Ensure unique slug
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }

            $category = Category::create([
                'name' => trim($data['name']),
                'slug' => $slug,
                'description' => ! empty($data['description']) ? trim($data['description']) : null,
            ]);

            AuditLog::log(
                action: 'category.created',
                auditable: $category,
                newValues: ['name' => $category->name, 'slug' => $category->slug],
                notes: 'Admin menambahkan kategori kelas baru: '.$category->name
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Insert data kategori kelas gagal', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Update existing category.
     */
    public static function update(int $id, array $data): bool
    {
        try {
            $category = self::getById($id);

            $oldValues = [
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ];

            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }

            $newValues = [
                'name' => trim($data['name']),
                'slug' => $slug,
                'description' => ! empty($data['description']) ? trim($data['description']) : null,
            ];

            $category->update($newValues);

            AuditLog::log(
                action: 'category.updated',
                auditable: $category,
                oldValues: $oldValues,
                newValues: $newValues,
                notes: 'Admin memperbarui data kategori: '.$category->name
            );

            return true;
        } catch (\Exception $e) {
            Log::error('Update data kategori kelas gagal', [
                'id' => $id,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete category with course constraint check.
     *
     * @return array{status: bool, message: string}
     */
    public static function delete(int $id): array
    {
        try {
            $category = self::getById($id);

            if ($category->courses_count > 0) {
                return [
                    'status' => false,
                    'message' => 'Kategori "'.$category->name.'" tidak dapat dihapus karena masih menaungi '.$category->courses_count.' kelas.',
                ];
            }

            AuditLog::log(
                action: 'category.deleted',
                auditable: $category,
                oldValues: ['name' => $category->name],
                notes: 'Admin menghapus kategori kelas: '.$category->name
            );

            $category->delete();

            return [
                'status' => true,
                'message' => 'Data Kategori berhasil dihapus.',
            ];
        } catch (\Exception $e) {
            Log::error('Hapus data kategori kelas gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'message' => 'Terjadi kesalahan sistem saat menghapus data kategori.',
            ];
        }
    }
}
