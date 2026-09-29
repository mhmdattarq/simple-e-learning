<?php

namespace App\Repositories;

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

            Category::create([
                'name' => trim($data['name']),
                'slug' => $slug,
                'description' => ! empty($data['description']) ? trim($data['description']) : null,
            ]);

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

            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = $originalSlug.'-'.$count++;
            }

            $category->update([
                'name' => trim($data['name']),
                'slug' => $slug,
                'description' => ! empty($data['description']) ? trim($data['description']) : null,
            ]);

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
