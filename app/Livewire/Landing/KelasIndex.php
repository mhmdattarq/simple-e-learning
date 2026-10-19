<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseExternal;
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
            $this->type = match ($type) {
                'batch' => 'bulanan',
                'permanent' => 'bangkom',
                'paid', 'berbayar' => 'bangkom_external',
                default => $type,
            };
        } elseif (request()->routeIs(['landing.kelas.bulanan', 'landing.kelas.batch']) || request()->is(['bulanan*', 'kelas-batch*'])) {
            $this->type = 'bulanan';
        } elseif (request()->routeIs(['landing.kelas.bangkom_external', 'landing.kelas.berbayar']) || request()->is(['bangkom-external*', 'kelas-berbayar*'])) {
            $this->type = 'bangkom_external';
        } elseif (request()->routeIs(['landing.kelas.bangkom', 'landing.kelas.permanen']) || request()->is(['bangkom', 'bangkom/*', 'kelas-permanen*'])) {
            $this->type = 'bangkom';
        } elseif (request()->has('type')) {
            $rawType = (string) request()->query('type');
            $this->type = match ($rawType) {
                'batch' => 'bulanan',
                'permanent' => 'bangkom',
                'paid', 'berbayar' => 'bangkom_external',
                default => $rawType,
            };
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
            'bulanan', 'batch' => [
                'badge' => 'Bulanan (Terjadwal)',
                'icon' => 'ri-calendar-event-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Bulanan',
                'subtitle' => 'Program diklat dan kelas kedinasan bulanan dengan kuota dan periode registrasi berkala untuk ASN BKPSDM Kabupaten Aceh Timur.',
                'page_title' => 'Katalog Bulanan – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Bulanan Tersedia',
                'empty_desc' => 'Program bulanan berjadwal akan segera dibuka. Pantau terus halaman ini.',
            ],
            'bangkom', 'permanent' => [
                'badge' => 'Bangkom (Mandiri 24/7)',
                'icon' => 'ri-infinity-line',
                'title_prefix' => 'Katalog',
                'title_highlight' => 'Bangkom',
                'subtitle' => 'Program pengembangan kompetensi mandiri fleksibel tanpa batas waktu pendaftaran. Pelajari materi kapan saja untuk akselerasi kompetensi ASN.',
                'page_title' => 'Katalog Bangkom – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Bangkom Tersedia',
                'empty_desc' => 'Program Bangkom mandiri fleksibel sedang disiapkan oleh pengampu.',
            ],
            'bangkom_external', 'paid', 'berbayar' => [
                'badge' => 'Bangkom Eksternal',
                'icon' => 'ri-external-link-line',
                'title_prefix' => 'Direktori',
                'title_highlight' => 'Bangkom Eksternal',
                'subtitle' => 'Direktori link rujukan portal dan platform pengembangan kompetensi ASN terpercaya dari berbagai kementerian dan lembaga pemerintah.',
                'page_title' => 'Direktori Bangkom Eksternal – SIMPEL BKPSDM Aceh Timur',
                'empty_title' => 'Belum Ada Pelatihan Eksternal Tersedia',
                'empty_desc' => 'Daftar rujukan portal pelatihan eksternal sedang dipersiapkan oleh pengampu.',
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
        $externals = collect();
        $userRegistrations = collect();

        if ($tablesExist) {
            $categories = Category::orderBy('name')->get();
            $trimmedSearch = trim($this->search);

            if (in_array($this->type, ['bangkom_external', 'paid', 'berbayar'], true)) {
                $externalsQuery = CourseExternal::with('category')
                    ->where('status', 'published');

                if ($trimmedSearch !== '') {
                    $externalsQuery->where(function ($q) use ($trimmedSearch) {
                        $q->where('name', 'like', '%'.$trimmedSearch.'%')
                            ->orWhere('description', 'like', '%'.$trimmedSearch.'%');
                    });
                }

                if ($this->selectedCategory !== 'all' && $this->selectedCategory !== '') {
                    $externalsQuery->whereHas('category', fn ($q) => $q->where('slug', $this->selectedCategory));
                }

                $externals = $externalsQuery->latest('id')->get();
            } else {
                $query = Course::with(['category', 'chapters.lessons'])
                    ->withCount(['chapters', 'lessons', 'registrations'])
                    ->where('status', 'published');

                if (in_array($this->type, ['bulanan', 'batch'], true)) {
                    $query->whereIn('type', ['bulanan', 'batch']);
                } elseif (in_array($this->type, ['bangkom', 'permanent'], true)) {
                    $query->whereIn('type', ['bangkom', 'permanent']);
                }

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
        }

        return view('mods.landing.kelas-index', compact(
            'config',
            'categories',
            'courses',
            'externals',
            'userRegistrations'
        ));
    }
}
