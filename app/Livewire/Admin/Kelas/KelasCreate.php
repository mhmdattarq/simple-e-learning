<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Category;
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
            'type' => 'batch',
            'price' => 0,
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
        if ($step === 1) {
            return [
                'form.title' => 'required|string|min:3|max:255',
                'form.description' => 'required|string|min:10|max:5000',
                'form.category_id' => 'required_without:form.category_name|nullable|exists:categories,id',
                'form.category_name' => 'required_without:form.category_id|nullable|string|max:100',
            ];
        }

        if ($step === 2) {
            $rules = [
                'form.type' => 'required|in:permanent,batch,paid,berbayar',
            ];

            if (($this->form['type'] ?? '') === 'batch') {
                $rules['form.start_date'] = 'required|date|after_or_equal:today';
                $rules['form.end_date'] = 'required|date|after:form.start_date';
            }

            if (in_array($this->form['type'] ?? '', ['paid', 'berbayar'])) {
                $rules['form.price'] = 'required|numeric|min:0';
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
            $this->resetErrorBag(['form.start_date', 'form.end_date', 'form.price']);
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
        $rules = array_merge(
            $this->getRulesForStep(1),
            $this->getRulesForStep(2),
            $this->getRulesForStep(3),
        );

        if (($this->form['type'] ?? '') !== 'batch') {
            $rules['form.start_date'] = 'nullable|date';
            $rules['form.end_date'] = 'nullable|date';
        }

        if (! in_array($this->form['type'] ?? '', ['paid', 'berbayar'])) {
            $rules['form.price'] = 'nullable|numeric|min:0';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'form.title.required' => 'Nama kelas wajib diisi.',
            'form.title.string' => 'Nama kelas harus berupa teks.',
            'form.title.min' => 'Nama kelas minimal 3 karakter.',
            'form.title.max' => 'Nama kelas maksimal 255 karakter.',
            'form.description.required' => 'Deskripsi kelas wajib diisi.',
            'form.description.string' => 'Deskripsi kelas harus berupa teks.',
            'form.description.min' => 'Deskripsi kelas minimal 10 karakter.',
            'form.description.max' => 'Deskripsi kelas maksimal 5.000 karakter.',
            'form.category_id.required' => 'Kategori kelas wajib dipilih.',
            'form.category_id.required_without' => 'Kategori kelas wajib dipilih.',
            'form.category_id.exists' => 'Kategori kelas yang dipilih tidak valid.',
            'form.category_name.required_without' => 'Kategori kelas wajib dipilih.',
            'form.category_name.string' => 'Kategori kelas harus berupa teks.',
            'form.category_name.max' => 'Kategori kelas maksimal 100 karakter.',
            'form.type.required' => 'Jenis kelas wajib dipilih.',
            'form.type.in' => 'Jenis kelas harus berupa Batch, Permanen, atau Berbayar.',
            'form.price.required' => 'Biaya kelas wajib ditentukan untuk kelas berbayar.',
            'form.price.numeric' => 'Biaya kelas harus berupa nilai angka.',
            'form.price.min' => 'Biaya kelas minimal Rp 0.',
            'form.start_date.required' => 'Tanggal & waktu mulai wajib diisi untuk kelas bertipe Batch.',
            'form.start_date.date' => 'Format tanggal & waktu mulai tidak valid.',
            'form.start_date.after_or_equal' => 'Tanggal & waktu mulai tidak boleh sebelum tanggal hari ini.',
            'form.end_date.required' => 'Tanggal & waktu selesai wajib diisi untuk kelas bertipe Batch.',
            'form.end_date.date' => 'Format tanggal & waktu selesai tidak valid.',
            'form.end_date.after' => 'Tanggal & waktu selesai harus setelah tanggal & waktu mulai (tidak boleh sama atau lebih awal).',
            'form.status.required' => 'Status kelas wajib dipilih.',
            'thumbnailFile.image' => 'Berkas sampul harus berupa gambar.',
            'thumbnailFile.mimes' => 'Format sampul harus berupa berkas JPG, JPEG, atau PNG.',
            'thumbnailFile.max' => 'Ukuran berkas sampul maksimal 2 MB.',
        ];
    }

    public array $validationAttributes = [
        'form.title' => 'Nama Kelas',
        'form.description' => 'Deskripsi Kelas',
        'form.category_id' => 'Kategori Kelas',
        'form.category_name' => 'Kategori Kelas',
        'form.type' => 'Jenis Kelas',
        'form.price' => 'Biaya Kelas',
        'form.start_date' => 'Tanggal & Waktu Mulai',
        'form.end_date' => 'Tanggal & Waktu Selesai',
        'form.status' => 'Status Kelas',
        'thumbnailFile' => 'Poster / Sampul Kelas',
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

        $isBatch = $this->form['type'] === 'batch';
        $isPaid = in_array($this->form['type'], ['paid', 'berbayar']);

        $startDate = null;
        $endDate = null;
        if ($isBatch && ! empty($this->form['start_date'])) {
            $startDate = Carbon::parse($this->form['start_date'])->format('Y-m-d H:i:s');
        }
        if ($isBatch && ! empty($this->form['end_date'])) {
            $endDate = Carbon::parse($this->form['end_date'])->format('Y-m-d H:i:s');
        }

        $payload = [
            'title' => trim($this->form['title']),
            'description' => ! empty($this->form['description']) ? trim($this->form['description']) : null,
            'category_id' => $resolvedCategoryId,
            'type' => $this->form['type'],
            'price' => $isPaid ? (float) ($this->form['price'] ?? 0) : 0,
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
