<?php

namespace App\Livewire\Peserta\Pendaftaran;

use App\Models\Course;
use App\Models\CourseUser;
use App\Repositories\PendaftaranRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('templates.layouts.landing')]
#[Title('Formulir Pendaftaran Pelatihan ASN - SIMPEL BKPSDM')]
class PendaftaranCreate extends Component
{
    use WithFileUploads;

    public Course $course;

    public array $form = [];

    /**
     * Uploaded recommendation letter file (PDF).
     */
    public $recommendationLetter = null;

    public bool $alreadyRegistered = false;

    public ?CourseUser $existingRegistration = null;

    public bool $registrationSuccess = false;

    public string $newRegistrationNumber = '';

    public function mount(int|string $id): void
    {
        $this->course = Course::with('category')->findOrFail($id);

        $user = Auth::user();

        // Check if user has already enrolled in this course
        $existing = CourseUser::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->first();

        if ($existing) {
            $this->alreadyRegistered = true;
            $this->existingRegistration = $existing;
        }

        $this->form = [
            'course_id' => $this->course->id,
            'nip' => $user->nip ?? '',
            'name' => $user->name ?? '',
            'opd_agency' => $user->opd_agency ?? '',
            'position' => $user->position ?? '',
            'rank_class' => $user->rank_class ?? '',
            'phone_number' => $user->phone_number ?? '',
            'email' => $user->email ?? '',
            'agreement' => false,
        ];
    }

    public function rules(): array
    {
        return [
            'form.nip' => ['required', 'digits:18'],
            'form.name' => ['required', 'string', 'max:255'],
            'form.opd_agency' => ['required', 'string', 'max:255'],
            'form.position' => ['required', 'string', 'max:255'],
            'form.rank_class' => ['required', 'string', 'max:100'],
            'form.phone_number' => ['required', 'string', 'min:9', 'max:20'],
            'form.email' => ['required', 'email', 'max:255'],
            'form.agreement' => ['accepted'],
            'recommendationLetter' => ['required', 'mimes:pdf', 'max:10240'], // Max 10MB PDF
        ];
    }

    public function messages(): array
    {
        return [
            'form.nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'form.nip.digits' => 'NIP ASN harus terdiri tepat 18 digit angka.',
            'form.name.required' => 'Nama lengkap dan gelar wajib diisi.',
            'form.opd_agency.required' => 'Instansi / Asal OPD wajib diisi.',
            'form.position.required' => 'Nama jabatan saat ini wajib diisi.',
            'form.rank_class.required' => 'Pangkat / Golongan ruang wajib diisi.',
            'form.phone_number.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'form.email.required' => 'Alamat email wajib diisi.',
            'form.email.email' => 'Format email tidak valid.',
            'form.agreement.accepted' => 'Anda wajib menyetujui pakta integritas dan ketentuan pelatihan.',
            'recommendationLetter.required' => 'Surat usulan / rekomendasi atasan (PDF) wajib diunggah.',
            'recommendationLetter.mimes' => 'Berkas surat usulan harus berformat PDF.',
            'recommendationLetter.max' => 'Ukuran berkas surat usulan maksimal 10 MB.',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'form.nip' => 'NIP',
            'form.name' => 'Nama Lengkap',
            'form.opd_agency' => 'Asal Instansi/OPD',
            'form.position' => 'Jabatan',
            'form.rank_class' => 'Pangkat/Golongan',
            'form.phone_number' => 'Nomor WhatsApp',
            'form.email' => 'Alamat Email',
            'form.agreement' => 'Persetujuan Ketentuan',
            'recommendationLetter' => 'Surat Rekomendasi',
        ];
    }

    public function submit(): void
    {
        if ($this->alreadyRegistered) {
            return;
        }

        $this->validate();

        try {
            $registered = PendaftaranRepo::register(
                userId: Auth::id(),
                courseId: $this->course->id,
                userData: $this->form,
                letterFile: $this->recommendationLetter
            );

            if ($registered) {
                $this->registrationSuccess = true;
                $this->newRegistrationNumber = $registered->registration_number;
                $this->alreadyRegistered = true;
                $this->existingRegistration = $registered;

                session()->flash('success_message', 'Pendaftaran pelatihan Anda berhasil dikirim! Menunggu verifikasi berkas oleh admin.');
            } else {
                $this->addError('general', 'Terjadi kesalahan sistem saat memproses pendaftaran. Silakan coba beberapa saat lagi.');
            }
        } catch (\DomainException $e) {
            $this->addError('general', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('general', 'Terjadi kesalahan sistem saat memproses pendaftaran. Silakan coba beberapa saat lagi.');
        }
    }

    public function render()
    {
        return view('mods.peserta.pendaftaran.pendaftaran-create');
    }
}
