<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\CourseExternal;
use App\Repositories\KelasRepo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class KelasCreate extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    public int $totalSteps = 3;

    public array $form = [];

    /**
     * File upload temporary properties
     */
    public $thumbnailFile = null;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->currentStep = 1;
        $this->form = [
            'title' => '',
            'description' => '',
            'category_id' => '',
            'category_name' => '',
            'type' => 'bulanan',
            'url' => '',
            'external_type' => 'gratis',
            'benefit' => 'Sertifikat',
            'price' => 0,
            'jam_pelajaran' => '24',
            'start_date' => '',
            'end_date' => '',
            'status' => 'published',
        ];

        $this->thumbnailFile = null;
    }

    public function nextStep(): void
    {
        $this->validate($this->getRulesForStep($this->currentStep), $this->messages(), $this->validationAttributes);

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > $this->totalSteps) {
            return;
        }

        if ($step < $this->currentStep) {
            $this->currentStep = $step;

            return;
        }

        // Validate each step before jumping forward
        for ($i = 1; $i < $step; $i++) {
            $this->validate($this->getRulesForStep($i), $this->messages(), $this->validationAttributes);
        }

        $this->currentStep = $step;
    }

    public function getRulesForStep(int $step): array
    {
        $isExternal = ($this->form['type'] ?? '') === 'bangkom_external';

        if ($step === 1) {
            $titleMax = $isExternal ? 150 : 255;
            $descMax = $isExternal ? 255 : 5000;

            return [
                'form.title' => "required|string|min:3|max:{$titleMax}",
                'form.description' => "required|string|min:10|max:{$descMax}",
                'form.category_id' => 'required_without:form.category_name|nullable|exists:categories,id',
                'form.category_name' => 'required_without:form.category_id|nullable|string|max:100',
            ];
        }

        if ($step === 2) {
            if ($isExternal) {
                return [
                    'form.type' => 'required|in:bulanan,bangkom,bangkom_external,permanent,batch,paid,berbayar',
                    'form.url' => 'required|url|max:500',
                    'form.external_type' => 'required|in:gratis,berbayar',
                    'form.benefit' => 'nullable|string|max:100',
                ];
            }

            $rules = [
                'form.type' => 'required|in:bulanan,bangkom,bangkom_external,permanent,batch,paid,berbayar',
                'form.jam_pelajaran' => 'required|string|max:50',
            ];

            if (in_array($this->form['type'] ?? '', ['bulanan', 'batch'], true)) {
                $rules['form.start_date'] = 'required|date|after_or_equal:today';
                $rules['form.end_date'] = 'required|date|after:form.start_date';
            }

            return $rules;
        }

        return [
            'form.status' => 'required|string',
            'thumbnailFile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'form.type') {
            $this->resetErrorBag(['form.start_date', 'form.end_date', 'form.price', 'form.url', 'form.external_type', 'form.benefit']);
        }

        if ($propertyName === 'form.start_date' && ! empty($this->form['end_date'])) {
            $this->validateOnly('form.end_date');
        }

        if ($propertyName === 'form.category_id' && ! empty($this->form['category_id'])) {
            $this->form['category_name'] = Category::find($this->form['category_id'])?->name ?? '';
        }

        if ($propertyName === 'form.category_name' && ! empty($this->form['category_name'])) {
            $cat = Category::whereRaw('LOWER(name) = ?', [strtolower(trim($this->form['category_name']))])->first();
            if ($cat) {
                $this->form['category_id'] = $cat->id;
            }
        }

        $this->validateOnly($propertyName);
    }

    public function rules(): array
    {
        $isExternal = ($this->form['type'] ?? '') === 'bangkom_external';

        $rules = array_merge(
            $this->getRulesForStep(1),
            $this->getRulesForStep(2),
            $this->getRulesForStep(3),
        );

        if (! $isExternal) {
            if (! in_array($this->form['type'] ?? '', ['bulanan', 'batch'], true)) {
                $rules['form.start_date'] = 'nullable|date';
                $rules['form.end_date'] = 'nullable|date';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'form.title.required' => 'Nama kelas wajib diisi.',
            'form.title.string' => 'Nama kelas harus berupa teks.',
            'form.title.min' => 'Nama kelas minimal 3 karakter.',
            'form.title.max' => 'Nama kelas maksimal 255 karakter (atau 150 karakter untuk Bangkom Eksternal).',
            'form.description.required' => 'Deskripsi kelas wajib diisi.',
            'form.description.string' => 'Deskripsi kelas harus berupa teks.',
            'form.description.min' => 'Deskripsi kelas minimal 10 karakter.',
            'form.description.max' => 'Deskripsi kelas maksimal 5.000 karakter (atau 255 karakter untuk Bangkom Eksternal).',
            'form.category_id.required' => 'Kategori kelas wajib dipilih.',
            'form.category_id.required_without' => 'Kategori kelas wajib dipilih.',
            'form.category_id.exists' => 'Kategori kelas yang dipilih tidak valid.',
            'form.category_name.required_without' => 'Kategori kelas wajib dipilih.',
            'form.category_name.string' => 'Kategori kelas harus berupa teks.',
            'form.category_name.max' => 'Kategori kelas maksimal 100 karakter.',
            'form.type.required' => 'Jenis kelas wajib dipilih.',
            'form.type.in' => 'Jenis kelas harus berupa Bulanan, Bangkom, atau Bangkom External.',
            'form.url.required' => 'Link URL rujukan pelatihan eksternal wajib diisi.',
            'form.url.url' => 'Format tautan URL tidak valid (harus diawali http:// atau https://).',
            'form.url.max' => 'Tautan URL maksimal 500 karakter.',
            'form.external_type.required' => 'Tipe biaya pelatihan eksternal wajib dipilih.',
            'form.external_type.in' => 'Tipe biaya harus Gratis atau Berbayar.',
            'form.benefit.max' => 'Benefit pelatihan maksimal 100 karakter.',
            'form.jam_pelajaran.required' => 'Jam pelajaran (JP) wajib diisi.',
            'form.jam_pelajaran.string' => 'Jam pelajaran (JP) harus berupa teks atau angka.',
            'form.jam_pelajaran.max' => 'Jam pelajaran (JP) maksimal 50 karakter.',
            'form.start_date.required' => 'Tanggal & waktu mulai wajib diisi untuk kelas bertipe Bulanan.',
            'form.start_date.date' => 'Format tanggal & waktu mulai tidak valid.',
            'form.start_date.after_or_equal' => 'Tanggal & waktu mulai tidak boleh sebelum tanggal hari ini.',
            'form.end_date.required' => 'Tanggal & waktu selesai wajib diisi untuk kelas bertipe Bulanan.',
            'form.end_date.date' => 'Format tanggal & waktu selesai tidak valid.',
            'form.end_date.after' => 'Tanggal & waktu selesai harus setelah tanggal & waktu mulai (tidak boleh sama atau lebih awal).',
            'form.status.required' => 'Status kelas wajib dipilih.',
            'thumbnailFile.image' => 'Berkas sampul harus berupa gambar.',
            'thumbnailFile.mimes' => 'Format sampul harus berupa berkas JPG, JPEG, atau PNG.',
            'thumbnailFile.max' => 'Ukuran berkas sampul maksimal 2 MB.',
        ];
    }

    public array $validationAttributes = [
        'form.title' => 'Nama Kelas / Platform',
        'form.description' => 'Deskripsi Kelas',
        'form.category_id' => 'Kategori Kelas',
        'form.category_name' => 'Kategori Kelas',
        'form.type' => 'Jenis Kelas',
        'form.url' => 'Link URL Bangkom Eksternal',
        'form.external_type' => 'Tipe Biaya Pelatihan',
        'form.benefit' => 'Benefit Pelatihan',
        'form.jam_pelajaran' => 'Jam Pelajaran (JP)',
        'form.price' => 'Biaya Kelas',
        'form.start_date' => 'Tanggal & Waktu Mulai',
        'form.end_date' => 'Tanggal & Waktu Selesai',
        'form.status' => 'Status Kelas',
        'thumbnailFile' => 'Poster / Logo Sampul',
    ];

    public function formSubmit(string $target = 'materi')
    {
        $this->validate();

        $resolvedCategoryId = null;
        if (! empty($this->form['category_id'])) {
            $resolvedCategoryId = (int) $this->form['category_id'];
        }
        if (! empty($this->form['category_name'])) {
            $currentCat = $resolvedCategoryId ? Category::find($resolvedCategoryId) : null;
            if (! $currentCat || strtolower(trim($currentCat->name)) !== strtolower(trim($this->form['category_name']))) {
                $resolvedCategoryId = KelasRepo::resolveCategoryId($this->form['category_name']);
            }
        }

        // Branching untuk Bangkom External: simpan ke tabel courses_externals
        if (($this->form['type'] ?? '') === 'bangkom_external') {
            $logoPath = null;
            if ($this->thumbnailFile) {
                $logoPath = $this->thumbnailFile->store('courses_externals/logos', 'public');
            }

            $courseExternal = CourseExternal::create([
                'name' => trim($this->form['title']),
                'category_id' => $resolvedCategoryId,
                'description' => trim($this->form['description']),
                'url' => trim($this->form['url']),
                'type' => $this->form['external_type'] ?? 'gratis',
                'benefit' => ! empty($this->form['benefit']) ? trim($this->form['benefit']) : 'Sertifikat',
                'logo' => $logoPath,
                'status' => $this->form['status'] ?? 'published',
                'created_by' => Auth::id(),
            ]);

            AuditLog::log(
                action: 'course_external.created',
                auditable: $courseExternal,
                newValues: [
                    'name' => $courseExternal->name,
                    'url' => $courseExternal->url,
                    'type' => $courseExternal->type,
                    'benefit' => $courseExternal->benefit,
                    'status' => $courseExternal->status,
                ],
                notes: 'Admin membuat rujukan bangkom eksternal: '.$courseExternal->name
            );

            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Pelatihan Eksternal Berhasil Disimpan',
                'message' => 'Rujukan pelatihan eksternal "'.$courseExternal->name.'" berhasil ditambahkan ke katalog.',
            ]);

            return $this->redirectRoute('kelas.data', navigate: true);
        }

        $isBulanan = in_array($this->form['type'], ['bulanan', 'batch'], true);

        $startDate = null;
        $endDate = null;
        if ($isBulanan && ! empty($this->form['start_date'])) {
            $startDate = Carbon::parse($this->form['start_date'])->format('Y-m-d H:i:s');
        }
        if ($isBulanan && ! empty($this->form['end_date'])) {
            $endDate = Carbon::parse($this->form['end_date'])->format('Y-m-d H:i:s');
        }

        $normalizedType = match ($this->form['type']) {
            'batch' => 'bulanan',
            'permanent' => 'bangkom',
            default => $this->form['type'],
        };

        $payload = [
            'title' => trim($this->form['title']),
            'description' => ! empty($this->form['description']) ? trim($this->form['description']) : null,
            'category_id' => $resolvedCategoryId,
            'type' => $normalizedType,
            'price' => 0,
            'jam_pelajaran' => trim((string) ($this->form['jam_pelajaran'] ?? '24')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->form['status'] ?? 'published',
            'created_by' => Auth::id(),
        ];

        // Store file uploads if present
        if ($this->thumbnailFile) {
            $payload['thumbnail'] = $this->thumbnailFile->store('courses/thumbnails', 'public');
        }

        $course = KelasRepo::create($payload);

        if ($course) {
            if ($target === 'index' || $target === 'draft') {
                session()->flash('alert-show', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Kelas Baru Berhasil Disimpan.',
                ]);

                return $this->redirectRoute('kelas.data', navigate: true);
            }

            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Kelas Berhasil Disimpan',
                'message' => 'Kelas telah dibuat. Silakan mulai menyusun bab dan materi pembelajaran.',
            ]);

            return $this->redirectRoute('materi.detail', ['id' => $course->id], navigate: true);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat menyimpan data kelas.',
        ]);
    }

    public function render()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('mods.admin.kelas.kelas-create', compact('categories'));
    }
}
