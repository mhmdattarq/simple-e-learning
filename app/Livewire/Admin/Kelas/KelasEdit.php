<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\Category;
use App\Models\Course;
use App\Repositories\KelasRepo;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Edit Kelas - SIMPEL BKPSDM')]
class KelasEdit extends Component
{
    use WithFileUploads;

    public int $id;

    public Course $course;

    public array $form = [];

    public $thumbnailFile = null;

    public $torFile = null;

    public function mount($id): void
    {
        $this->id = (int) $id;
        $this->course = KelasRepo::getById($id);

        $this->form = [
            'title' => $this->course->title,
            'description' => $this->course->description ?? '',
            'category_id' => $this->course->category_id ?? '',
            'category_name' => $this->course->category?->name ?? '',
            'type' => $this->course->type,
            'price' => (float) ($this->course->price ?? 0),
            'start_date' => $this->course->start_date ? $this->course->start_date->format('Y-m-d') : '',
            'end_date' => $this->course->end_date ? $this->course->end_date->format('Y-m-d') : '',
            'status' => $this->course->status->value ?? (string) $this->course->status,
        ];
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'form.type') {
            $this->resetErrorBag(['form.start_date', 'form.end_date', 'form.price']);
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
        $rules = [
            'form.title' => 'required|string|min:3|max:255',
            'form.description' => 'nullable|string|max:5000',
            'form.category_id' => 'required_without:form.category_name|nullable|exists:categories,id',
            'form.category_name' => 'required_without:form.category_id|nullable|string|max:100',
            'form.type' => 'required|in:permanent,batch,paid,berbayar',
            'form.status' => 'required|string',
            'thumbnailFile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'torFile' => 'nullable|file|mimes:pdf|max:10240',
        ];

        if (($this->form['type'] ?? '') === 'batch') {
            $rules['form.start_date'] = 'required|date';
            $rules['form.end_date'] = 'required|date|after_or_equal:form.start_date';
        } else {
            $rules['form.start_date'] = 'nullable|date';
            $rules['form.end_date'] = 'nullable|date';
        }

        if (in_array($this->form['type'] ?? '', ['paid', 'berbayar'])) {
            $rules['form.price'] = 'required|numeric|min:0';
        } else {
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
            'form.category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'form.category_name.string' => 'Kategori kelas harus berupa teks.',
            'form.category_name.max' => 'Kategori kelas maksimal 100 karakter.',
            'form.type.required' => 'Jenis kelas wajib dipilih.',
            'form.type.in' => 'Jenis kelas harus berupa Batch, Permanen, atau Berbayar.',
            'form.price.required' => 'Biaya kelas wajib ditentukan untuk kelas berbayar.',
            'form.price.numeric' => 'Biaya kelas harus berupa angka.',
            'form.price.min' => 'Biaya kelas minimal Rp 0.',
            'form.start_date.required' => 'Tanggal mulai wajib diisi untuk kelas bertipe Batch.',
            'form.start_date.date' => 'Format tanggal mulai tidak valid.',
            'form.end_date.required' => 'Tanggal selesai wajib diisi untuk kelas bertipe Batch.',
            'form.end_date.date' => 'Format tanggal selesai tidak valid.',
            'form.end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'thumbnailFile.image' => 'Berkas sampul harus berupa gambar.',
            'thumbnailFile.mimes' => 'Format sampul harus berupa berkas JPG, JPEG, atau PNG.',
            'thumbnailFile.max' => 'Ukuran berkas sampul maksimal 2 MB.',
            'torFile.file' => 'Berkas KAK harus berupa file dokumen valid.',
            'torFile.mimes' => 'Berkas KAK harus berupa dokumen PDF.',
            'torFile.max' => 'Ukuran berkas KAK maksimal 10 MB.',
        ];
    }

    public array $validationAttributes = [
        'form.title' => 'Nama Kelas',
        'form.description' => 'Deskripsi Kelas',
        'form.category_id' => 'Kategori Kelas',
        'form.category_name' => 'Kategori Kelas',
        'form.type' => 'Jenis Kelas',
        'form.price' => 'Biaya Kelas',
        'form.start_date' => 'Tanggal Mulai',
        'form.end_date' => 'Tanggal Selesai',
        'thumbnailFile' => 'Poster Kelas',
        'torFile' => 'Dokumen KAK / TOR',
    ];

    public function formSubmit()
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

        $payload = [
            'title' => trim($this->form['title']),
            'description' => ! empty($this->form['description']) ? trim($this->form['description']) : null,
            'category_id' => $resolvedCategoryId,
            'type' => $this->form['type'],
            'price' => $isPaid ? (float) ($this->form['price'] ?? 0) : 0,
            'start_date' => $isBatch ? $this->form['start_date'] : null,
            'end_date' => $isBatch ? $this->form['end_date'] : null,
            'status' => $this->form['status'] ?? 'published',
        ];

        // Store file uploads if present
        if ($this->thumbnailFile) {
            $payload['thumbnail'] = $this->thumbnailFile->store('courses/thumbnails', 'public');
        }

        if ($this->torFile) {
            $payload['tor_file'] = $this->torFile->store('courses/tors', 'public');
        }

        $process = KelasRepo::update($this->id, $payload);

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Kelas berhasil diperbarui.',
            ]);

            return $this->redirectRoute('kelas.data', navigate: true);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat memperbarui data kelas.',
        ]);
    }

    public function render()
    {
        $categories = Category::orderBy('name', 'asc')->get();

        return view('mods.admin.kelas.kelas-edit', compact('categories'));
    }
}
