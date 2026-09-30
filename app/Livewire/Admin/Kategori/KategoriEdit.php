<?php

namespace App\Livewire\Admin\Kategori;

use App\Repositories\KategoriRepo;
use Livewire\Component;

class KategoriEdit extends Component
{
    public int $categoryId;

    public array $form = [
        'name' => '',
        'description' => '',
    ];

    public function mount(int $id): void
    {
        $this->categoryId = $id;
        $category = KategoriRepo::getById($id);

        $this->form = [
            'name' => $category->name,
            'description' => $category->description ?? '',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function rules(): array
    {
        return [
            'form.name' => 'required|string|min:3|max:100|unique:categories,name,'.$this->categoryId,
            'form.description' => 'required|string|min:5|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required' => 'Nama kategori wajib diisi.',
            'form.name.string' => 'Nama kategori harus berupa teks.',
            'form.name.min' => 'Nama kategori minimal 3 karakter.',
            'form.name.max' => 'Nama kategori maksimal 100 karakter.',
            'form.name.unique' => 'Nama kategori ini sudah digunakan oleh kategori lain.',
            'form.description.required' => 'Deskripsi kategori wajib diisi.',
            'form.description.string' => 'Deskripsi harus berupa teks.',
            'form.description.min' => 'Deskripsi kategori minimal 5 karakter.',
            'form.description.max' => 'Deskripsi kategori maksimal 1.000 karakter.',
        ];
    }

    public array $validationAttributes = [
        'form.name' => 'Nama Kategori',
        'form.description' => 'Deskripsi Kategori',
    ];

    public function formSubmit()
    {
        $this->validate();

        $process = KategoriRepo::update($this->categoryId, $this->form);

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Kategori Kelas berhasil diperbarui.',
            ]);

            return $this->redirectRoute('kategori.data', navigate: true);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat memperbarui kategori.',
        ]);
    }

    public function render()
    {
        return view('mods.admin.kategori.kategori-edit');
    }
}
