<?php

namespace App\Livewire\Admin\Perencanaan;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Repositories\PerencanaanRepo;
use Livewire\Component;
use Livewire\WithFileUploads;

class PerencanaanEdit extends Component
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
        $this->course = PerencanaanRepo::getById($id);

        // Guard: Hanya pelatihan berstatus Draft yang boleh diedit
        if ($this->course->status !== CourseStatus::Draft) {
            session()->flash('alert-show', [
                'type' => 'warning',
                'title' => 'Akses Dibatasi',
                'message' => 'Hanya program pelatihan berstatus Draft yang dapat diubah.',
            ]);

            $this->redirectRoute('perencanaan.data', navigate: true);

            return;
        }

        $this->form = [
            'code' => $this->course->code,
            'title' => $this->course->title,
            'category_name' => $this->course->category?->name ?? '',
            'type' => $this->course->type,
            'start_date' => $this->course->start_date ? $this->course->start_date->format('Y-m-d') : '',
            'end_date' => $this->course->end_date ? $this->course->end_date->format('Y-m-d') : '',
            'method' => $this->course->method,
            'location' => $this->course->location ?? '',
            'quota' => $this->course->quota,
            'target_audience' => $this->course->target_audience ?? '',
            'budget_source' => $this->course->budget_source ?? '',
            'competencies' => $this->course->competencies ?? '',
            'description' => $this->course->description ?? '',
            'status' => 'draft',
        ];
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'form.type') {
            $this->resetErrorBag(['form.start_date', 'form.end_date']);
        }

        $this->validateOnly($propertyName);
    }

    public function rules(): array
    {
        $rules = [
            'form.code' => 'required|string|max:50|unique:courses,code,'.$this->id,
            'form.title' => 'required|string|min:3|max:255',
            'form.category_name' => 'required|string|max:100',
            'form.type' => 'required|in:permanent,batch',
            'form.method' => 'required|in:luring,daring,hybrid',
            'form.location' => 'required|string|max:255',
            'form.quota' => 'required|integer|min:1|max:10000',
            'form.target_audience' => 'required|string|max:255',
            'form.budget_source' => 'required|string|max:255',
            'form.competencies' => 'required|string|max:2000',
            'form.description' => 'required|string|max:5000',
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

        return $rules;
    }

    public function messages(): array
    {
        return [
            'form.code.required' => 'Kode pelatihan wajib diisi.',
            'form.code.string' => 'Kode pelatihan harus berupa teks.',
            'form.code.max' => 'Kode pelatihan maksimal 50 karakter.',
            'form.code.unique' => 'Kode pelatihan sudah terdaftar untuk program lain.',
            'form.title.required' => 'Nama pelatihan wajib diisi.',
            'form.title.string' => 'Nama pelatihan harus berupa teks.',
            'form.title.min' => 'Nama pelatihan minimal 3 karakter.',
            'form.title.max' => 'Nama pelatihan maksimal 255 karakter.',
            'form.category_name.required' => 'Kategori pelatihan wajib diisi.',
            'form.category_name.string' => 'Kategori pelatihan harus berupa teks.',
            'form.category_name.max' => 'Kategori pelatihan maksimal 100 karakter.',
            'form.type.required' => 'Tipe pelatihan wajib dipilih.',
            'form.type.in' => 'Tipe pelatihan harus bernilai Permanen atau Batch.',
            'form.start_date.required' => 'Tanggal mulai wajib diisi untuk pelatihan bertipe Batch.',
            'form.start_date.date' => 'Format tanggal mulai tidak valid.',
            'form.end_date.required' => 'Tanggal selesai wajib diisi untuk pelatihan bertipe Batch.',
            'form.end_date.date' => 'Format tanggal selesai tidak valid.',
            'form.end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'form.method.required' => 'Metode pelaksanaan wajib dipilih.',
            'form.method.in' => 'Metode pelaksanaan harus Daring, Luring, atau Hybrid.',
            'form.quota.required' => 'Kuota peserta wajib diisi.',
            'form.quota.integer' => 'Kuota peserta harus berupa angka bulat.',
            'form.quota.min' => 'Kuota minimal 1 peserta.',
            'form.quota.max' => 'Kuota maksimal 10.000 peserta.',
            'form.location.required' => 'Ruangan fisik atau tautan kelas online wajib diisi.',
            'form.location.max' => 'Ruangan fisik / tautan maksimal 255 karakter.',
            'form.target_audience.required' => 'Sasaran peserta pelatihan wajib diisi.',
            'form.target_audience.max' => 'Sasaran peserta maksimal 255 karakter.',
            'form.budget_source.required' => 'Sumber dana / anggaran pelatihan wajib diisi.',
            'form.budget_source.max' => 'Sumber dana / anggaran maksimal 255 karakter.',
            'form.competencies.required' => 'Target kompetensi aparatur wajib diisi.',
            'form.competencies.max' => 'Target kompetensi maksimal 2.000 karakter.',
            'form.description.required' => 'Deskripsi pelatihan wajib diisi.',
            'form.description.max' => 'Deskripsi pelatihan maksimal 5.000 karakter.',
            'thumbnailFile.image' => 'Berkas sampul harus berupa gambar.',
            'thumbnailFile.mimes' => 'Format sampul harus berupa berkas JPG, JPEG, atau PNG.',
            'thumbnailFile.max' => 'Ukuran berkas sampul maksimal 2 MB.',
            'torFile.file' => 'Berkas KAK harus berupa file dokumen valid.',
            'torFile.mimes' => 'Berkas KAK harus berupa dokumen PDF.',
            'torFile.max' => 'Ukuran berkas KAK maksimal 10 MB.',
        ];
    }

    public array $validationAttributes = [
        'form.code' => 'Kode Pelatihan',
        'form.title' => 'Nama Pelatihan',
        'form.category_name' => 'Kategori Pelatihan',
        'form.type' => 'Tipe Pelatihan',
        'form.start_date' => 'Tanggal Mulai',
        'form.end_date' => 'Tanggal Selesai',
        'form.method' => 'Metode Pelatihan',
        'form.quota' => 'Kuota Peserta',
        'form.location' => 'Ruangan / Lokasi',
        'form.target_audience' => 'Sasaran Peserta',
        'form.budget_source' => 'Sumber Dana / Anggaran',
        'form.competencies' => 'Target Kompetensi',
        'form.description' => 'Deskripsi Pelatihan',
        'thumbnailFile' => 'Poster Pelatihan',
        'torFile' => 'Dokumen KAK / TOR',
    ];

    public function formSubmit()
    {
        $this->validate();

        $payload = [
            'code' => trim($this->form['code']),
            'title' => trim($this->form['title']),
            'category_id' => PerencanaanRepo::resolveCategoryId($this->form['category_name']),
            'type' => $this->form['type'],
            'start_date' => $this->form['type'] === 'batch' ? $this->form['start_date'] : null,
            'end_date' => $this->form['type'] === 'batch' ? $this->form['end_date'] : null,
            'method' => $this->form['method'],
            'location' => trim($this->form['location'] ?: '') ?: null,
            'quota' => (int) $this->form['quota'],
            'target_audience' => trim($this->form['target_audience'] ?: '') ?: null,
            'budget_source' => trim($this->form['budget_source'] ?: '') ?: null,
            'competencies' => trim($this->form['competencies'] ?: '') ?: null,
            'description' => trim($this->form['description'] ?: '') ?: null,
        ];

        if ($this->thumbnailFile) {
            $payload['thumbnail'] = $this->thumbnailFile->store('courses/thumbnails', 'public');
        }

        if ($this->torFile) {
            $payload['tor_file'] = $this->torFile->store('courses/tors', 'public');
        }

        $process = PerencanaanRepo::update($this->id, $payload);

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Pelatihan Berhasil Diperbarui.',
            ]);

            return $this->redirectRoute('perencanaan.data', navigate: true);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat memperbarui data pelatihan.',
        ]);
    }

    public function submitToLeader()
    {
        $this->formSubmit();

        $process = PerencanaanRepo::submitToLeader($this->id);

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Program pelatihan berhasil diajukan ke Pimpinan untuk persetujuan.',
            ]);

            return $this->redirectRoute('perencanaan.data', navigate: true);
        }
    }

    public function render()
    {
        return view('mods.admin.perencanaan.perencanaan-edit');
    }
}
