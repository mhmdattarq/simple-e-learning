<?php

namespace App\Livewire\Pimpinan\Persetujuan;

use App\Enums\Role;
use App\Models\Course;
use App\Repositories\PimpinanRepo;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PersetujuanReview extends Component
{
    public int $id;

    public Course $course;

    public string $decisionNotes = '';

    public function mount($id): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, [Role::Pimpinan, Role::Admin], true)) {
            abort(403, 'Akses terbatas hanya untuk peran Pimpinan dan Eksekutif.');
        }

        $this->id = (int) $id;
        $this->course = PimpinanRepo::getById($id);
    }

    public function approve(): mixed
    {
        $this->authorize('approve', $this->course);

        try {
            $result = PimpinanRepo::approvePlan($this->course->id, $this->decisionNotes);

            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Rencana Pelatihan Disetujui',
                'message' => $result['message'],
            ]);

            return $this->redirectRoute('pimpinan.persetujuan.data', navigate: true);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($key, $msg);
                }
            }
        } catch (\Exception $e) {
            session()->flash('alert-show', [
                'type' => 'danger',
                'title' => 'Gagal Menyetujui',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem saat memproses persetujuan.',
            ]);
        }

        return null;
    }

    public function reject(): mixed
    {
        $this->authorize('approve', $this->course);

        $this->validate([
            'decisionNotes' => 'required|min:5',
        ], [
            'decisionNotes.required' => 'Catatan revisi wajib diisi agar Admin Diklat mengetahui poin perbaikan yang diperlukan.',
            'decisionNotes.min' => 'Catatan revisi minimal berisi 5 karakter penjelasan.',
        ]);

        try {
            $result = PimpinanRepo::rejectPlan($this->course->id, $this->decisionNotes);

            session()->flash('alert-show', [
                'type' => 'warning',
                'title' => 'Usulan Dikembalikan untuk Revisi',
                'message' => $result['message'],
            ]);

            return $this->redirectRoute('pimpinan.persetujuan.data', navigate: true);
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($key, $msg);
                }
            }
        } catch (\Exception $e) {
            session()->flash('alert-show', [
                'type' => 'danger',
                'title' => 'Gagal Mengembalikan Usulan',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem saat memproses pengembalian revisi.',
            ]);
        }

        return null;
    }

    public function render()
    {
        return view('mods.pimpinan.persetujuan.persetujuan-review');
    }
}
