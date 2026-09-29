<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
class KelasIndex extends Component
{
    #[Url]
    public string $type = 'all';

    public string $search = '';

    public string $selectedCategory = 'all';

    public function mount(?string $type = null): void
    {
        if ($type) {
            $this->type = $type;
        } elseif (request()->routeIs('landing.kelas.batch') || request()->is('kelas-batch*')) {
            $this->type = 'batch';
        } elseif (request()->routeIs('landing.kelas.permanen') || request()->is('kelas-permanen*')) {
            $this->type = 'permanent';
        } elseif (request()->routeIs('landing.kelas.berbayar') || request()->is('kelas-berbayar*')) {
            $this->type = 'paid';
        } elseif (request()->has('type')) {
            $this->type = (string) request()->query('type');
        } else {
            $this->type = 'all';
        }
    }

    public function filterCategory(string $categorySlug): void
    {
        $this->selectedCategory = $categorySlug;
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
    }

    public function render()
    {
        $user = Auth::user();
        $tablesExist = Schema::hasTable('courses');

        $config = match ($this->type) {
            'batch' => [
                'badge' => 'Kelas Terjadwal (Batch)',
                'icon' => 'ri-calendar-event-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Kelas Batch',
                'subtitle' => 'Program diklat dan kelas kedinasan berjadwal dengan kuota dan periode registrasi berkala untuk ASN BKPSDM Kabupaten Aceh Timur.',
                'page_title' => 'Katalog Kelas Batch – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Kelas Batch Tersedia',
                'empty_desc' => 'Program diklat batch berjadwal akan segera dibuka. Pantau terus halaman ini.',
            ],
            'permanent' => [
                'badge' => 'Belajar Mandiri (Self-Paced)',
                'icon' => 'ri-infinite-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Kelas Permanen',
                'subtitle' => 'Kelas digital fleksibel tanpa batas waktu pendaftaran. Pelajari materi secara mandiri kapan saja untuk akselerasi kompetensi ASN.',
                'page_title' => 'Katalog Kelas Permanen – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Kelas Permanen Tersedia',
                'empty_desc' => 'Kelas mandiri fleksibel sedang disiapkan oleh pengampu.',
            ],
            'paid' => [
                'badge' => 'Kelas Spesialisasi & Sertifikasi',
                'icon' => 'ri-money-dollar-circle-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Kelas Berbayar',
                'subtitle' => 'Program sertifikasi keahlian khusus dan kelas profesi lanjutan yang diselenggarakan bersama lembaga diklat terakreditasi resmi.',
                'page_title' => 'Katalog Kelas Berbayar – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Kelas Berbayar Tersedia',
                'empty_desc' => 'Program sertifikasi berbayar sedang disiapkan.',
            ],
            default => [
                'badge' => 'Program Kelas ASN',
                'icon' => 'ri-book-open-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Kelas',
                'subtitle' => 'Daftar lengkap program pengembangan kompetensi dan kelas digital aparatur sipil negara BKPSDM Kabupaten Aceh Timur.',
                'page_title' => 'Katalog Kelas – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Kelas Tersedia',
                'empty_desc' => 'Kelas sedang disiapkan oleh tim BKPSDM Aceh Timur.',
            ],
        };

        $categories = collect();
        $courses = collect();
        $userRegistrations = collect();

        if ($tablesExist) {
            $categories = Category::orderBy('name')->get();

            $query = Course::with(['category', 'chapters.lessons'])
                ->withCount(['chapters', 'lessons', 'registrations'])
                ->where('status', 'published');

            if ($this->type === 'batch') {
                $query->where('type', 'batch');
            } elseif ($this->type === 'permanent') {
                $query->where('type', 'permanent');
            } elseif ($this->type === 'paid') {
                $query->whereIn('type', ['paid', 'berbayar']);
            }

            $trimmedSearch = trim($this->search);
            if ($trimmedSearch !== '') {
                $query->where('title', 'like', '%'.$trimmedSearch.'%');
            }

            if ($this->selectedCategory !== 'all' && $this->selectedCategory !== '') {
                $query->whereHas('category', fn ($q) => $q->where('slug', $this->selectedCategory));
            }

            $courses = $query->latest('id')->get();

            if ($user && $courses->isNotEmpty() && Schema::hasTable('course_user')) {
                $userRegistrations = CourseUser::where('user_id', $user->id)
                    ->whereIn('course_id', $courses->pluck('id'))
                    ->get()
                    ->keyBy('course_id');
            }
        }

        return view('mods.landing.kelas-index', compact(
            'config',
            'categories',
            'courses',
            'userRegistrations'
        ))->title($config['page_title']);
    }
}
