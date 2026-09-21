<?php

namespace App\Livewire\Admin\Perencanaan;

use App\Models\Category;
use App\Models\Course;
use App\Repositories\PerencanaanRepo;
use Illuminate\Support\Collection;
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

    public Collection $categories;

    public function mount($id): void
    {
        $this->id = (int) $id;
        $this->course = PerencanaanRepo::getById($id);
        $this->categories = Category::all();

        $this->form = [
            'code' => $this->course->code,
            'title' => $this->course->title,
            'category_id' => $this->course->category_id,
            'type' => $this->course->type,
            'start_date' => $this->course->start_date ? $this->course->start_date->format('Y-m-d') : '',
            'end_date' => $this->course->end_date ? $this->course->end_date->format('Y-m-d') : '',
            'method' => $this->course->method,
            'location' => $this->course->location ?? '',
            'quota' => $this->course->quota,
            'target_audience' => $this->course->target_audience ?? '',
            'budget_source' => $this->course->budget_source ?? '',
            'description' => $this->course->description ?? '',
            'status' => $this->course->status,
        ];
    }

    public function rules(): array
    {
        $rules = [
            'form.code' => 'required|string|max:50|unique:courses,code,'.$this->id,
            'form.title' => 'required|string|max:255',
            'form.category_id' => 'required|exists:categories,id',
            'form.type' => 'required|in:permanent,batch',
            'form.method' => 'required|in:luring,daring,hybrid',
            'form.location' => 'nullable|string|max:255',
            'form.quota' => 'required|integer|min:1',
            'form.target_audience' => 'nullable|string|max:255',
            'form.budget_source' => 'nullable|string|max:255',
            'form.description' => 'nullable|string',
            'form.status' => 'required|in:draft,published,archived',
            'thumbnailFile' => 'nullable|image|max:2048',
            'torFile' => 'nullable|mimes:pdf|max:10240',
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
            'form.code.unique' => 'Kode pelatihan sudah terdaftar untuk program lain.',
            'form.title.required' => 'Nama pelatihan wajib diisi.',
            'form.category_id.required' => 'Kategori pelatihan wajib dipilih.',
            'form.type.required' => 'Tipe pelatihan wajib dipilih.',
            'form.start_date.required' => 'Tanggal mulai wajib diisi untuk pelatihan bertipe Batch.',
            'form.end_date.required' => 'Tanggal selesai wajib diisi untuk pelatihan bertipe Batch.',
            'form.end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'form.quota.required' => 'Kuota peserta wajib diisi.',
            'form.quota.min' => 'Kuota minimal 1 peserta.',
            'thumbnailFile.image' => 'Berkas sampul harus berupa gambar (JPG, PNG).',
            'thumbnailFile.max' => 'Ukuran sampul maksimal 2 MB.',
            'torFile.mimes' => 'Berkas KAK harus berupa dokumen PDF.',
            'torFile.max' => 'Ukuran berkas KAK maksimal 10 MB.',
        ];
    }

    public array $validationAttributes = [
        'form.code' => 'Kode Pelatihan',
        'form.title' => 'Nama Pelatihan',
        'form.category_id' => 'Kategori Pelatihan',
        'form.type' => 'Tipe Pelatihan',
        'form.start_date' => 'Tanggal Mulai',
        'form.end_date' => 'Tanggal Selesai',
        'form.method' => 'Metode Pelatihan',
        'form.quota' => 'Kuota Peserta',
    ];

    public function formSubmit()
    {
        $this->validate();

        $payload = [
            'code' => trim($this->form['code']),
            'title' => trim($this->form['title']),
            'category_id' => $this->form['category_id'],
            'type' => $this->form['type'],
            'start_date' => $this->form['type'] === 'batch' ? $this->form['start_date'] : null,
            'end_date' => $this->form['type'] === 'batch' ? $this->form['end_date'] : null,
            'method' => $this->form['method'],
            'location' => trim($this->form['location'] ?: '') ?: null,
            'quota' => (int) $this->form['quota'],
            'target_audience' => trim($this->form['target_audience'] ?: '') ?: null,
            'budget_source' => trim($this->form['budget_source'] ?: '') ?: null,
            'description' => trim($this->form['description'] ?: '') ?: null,
            'status' => $this->form['status'],
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

    public function render()
    {
        return view('mods.admin.perencanaan.perencanaan-edit');
    }
}
