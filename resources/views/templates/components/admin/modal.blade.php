<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $modalId = 'modalDelete';

    public $data = [];

    public $chapterForm = [
        'id' => null,
        'course_id' => null,
        'title' => '',
        'order' => 1,
    ];

    public function mount($modalId = 'modalDelete')
    {
        $this->modalId = $modalId;
    }

    #[On('modal-delete-setDeleteId')]
    public function setDeleteId($data)
    {
        $this->data = is_array($data) && isset($data['data']) ? $data['data'] : $data;
    }

    public function process($id = null)
    {
        $dtHook = [
            'id' => $id,
            'payload' => $this->data['payload'] ?? null,
        ];
        $this->dispatch($this->data['dispatch'] ?? 'ModulData-delete', $dtHook);
    }

    #[On('modal-chapter-set')]
    public function setChapterData($data)
    {
        $data = is_array($data) && isset($data['data']) ? $data['data'] : $data;
        $this->chapterForm = [
            'id' => $data['id'] ?? null,
            'course_id' => $data['course_id'] ?? null,
            'title' => $data['title'] ?? '',
            'order' => $data['order'] ?? 1,
        ];
        $this->resetErrorBag();
    }

    public function saveChapter()
    {
        $this->validate(
            [
                'chapterForm.title' => 'required|string|max:255',
                'chapterForm.order' => 'required|integer|min:1',
            ],
            [
                'chapterForm.title.required' => 'Judul bab silabus kurikulum wajib diisi.',
                'chapterForm.order.required' => 'Nomor urut bab wajib ditentukan.',
            ],
        );

        $this->dispatch('MateriDetail-saveChapter', $this->chapterForm);
    }
};
?>

<div>
    <style>
        .simpel-modal-dialog {
            max-width: 440px !important;
            margin: 1.75rem auto !important;
        }

        .simpel-modal-content {
            border: none !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            background-color: #ffffff !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
            position: relative !important;
        }

        .simpel-modal-body {
            padding: 28px 24px !important;
            text-align: center !important;
            box-sizing: border-box !important;
        }

        .simpel-modal-icon-badge {
            width: 64px !important;
            height: 64px !important;
            border-radius: 50% !important;
            background-color: #fef2f2;
            color: #dc2626;
            font-size: 28px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin-bottom: 14px !important;
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.15);
        }

        .simpel-modal-title {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 0 8px 0 !important;
            letter-spacing: -0.3px !important;
            line-height: 1.3 !important;
        }

        .simpel-modal-msg-box {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 14px 16px !important;
            margin-top: 14px !important;
            box-sizing: border-box !important;
            min-height: auto !important;
            height: auto !important;
        }

        .simpel-modal-msg {
            color: #64748b !important;
            font-size: 13.5px !important;
            line-height: 1.55 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .simpel-modal-actions {
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            margin-top: 20px !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        .simpel-modal-btn-cancel {
            flex: 1 1 50% !important;
            width: 50% !important;
            padding: 10px 16px !important;
            border: 1px solid #d1d5db !important;
            background-color: #ffffff !important;
            color: #1e293b !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            font-size: 13.5px !important;
            line-height: 1.4 !important;
            text-align: center !important;
            transition: all 0.15s ease !important;
            box-shadow: none !important;
            cursor: pointer !important;
            text-decoration: none !important;
            display: inline-block !important;
            box-sizing: border-box !important;
        }

        .simpel-modal-btn-cancel:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .simpel-modal-btn-confirm,
        .simpel-modal-btn-success {
            flex: 1 1 50% !important;
            width: 100% !important;
            padding: 10px 16px !important;
            font-weight: 600 !important;
            border-radius: 10px !important;
            font-size: 13.5px !important;
            line-height: 1.4 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.15s ease !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
        }

        .simpel-modal-btn-confirm {
            border: 1px solid #dc2626 !important;
            background-color: #dc2626 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
        }

        .simpel-modal-btn-confirm:hover {
            background-color: #b91c1c !important;
            border-color: #b91c1c !important;
            color: #ffffff !important;
        }

        .simpel-modal-btn-success {
            border: 1px solid #16a34a !important;
            background-color: #16a34a !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25) !important;
        }

        .simpel-modal-btn-success:hover {
            background-color: #15803d !important;
            border-color: #15803d !important;
            color: #ffffff !important;
        }

        .simpel-modal-form {
            flex: 1 1 50% !important;
            width: 50% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
    </style>

    {{-- Modal Delete / Universal Action --}}
    <div class="modal fade" id="{{ $data['modalId'] ?? $modalId }}" tabindex="-1" role="dialog" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered simpel-modal-dialog" role="document">
            <div class="modal-content simpel-modal-content">

                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close" style="z-index: 10; font-size: 11px; cursor: pointer;"></button>

                {{-- Modal Body --}}
                <div class="modal-body simpel-modal-body">
                    {{-- Warning Icon Badge --}}
                    <div class="simpel-modal-icon-badge {{ $data['iconBadgeClass'] ?? '' }}"
                        @if (!empty($data['iconBadgeStyle'])) style="{{ $data['iconBadgeStyle'] }}" @endif>
                        <i class="{{ $data['icon'] ?? 'ri-delete-bin-line' }}"></i>
                    </div>

                    {{-- Title --}}
                    <h5 class="simpel-modal-title">
                        {{ $data['title'] ?? 'Konfirmasi Hapus' }}
                    </h5>

                    {{-- Message with soft highlight box --}}
                    <div class="simpel-modal-msg-box text-start {{ $data['msgBoxClass'] ?? '' }}">
                        <div class="simpel-modal-msg">
                            {!! nl2br(e($data['msg'] ?? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) !!}
                        </div>
                    </div>

                    {{-- Action Buttons (50/50 Balanced) --}}
                    <div class="simpel-modal-actions">
                        <button type="button" class="btn {{ $data['btnCancelClass'] ?? 'simpel-modal-btn-cancel' }}" data-bs-dismiss="modal">
                            {{ $data['btnCancelText'] ?? 'Batal' }}
                        </button>
                        <button type="button" class="btn {{ $data['btnConfirmClass'] ?? 'simpel-modal-btn-confirm' }}"
                            @if (!empty($data['btnConfirmStyle'])) style="{{ $data['btnConfirmStyle'] }}" @endif
                            data-bs-dismiss="modal"
                            wire:click="process({{ $data['id'] ?? 0 }})">
                            <i class="{{ $data['btnConfirmIcon'] ?? 'ri-delete-bin-line' }}"></i> {{ $data['btnConfirmText'] ?? 'Ya, Hapus' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah / Edit Bab Kurikulum --}}
    <div class="modal fade" id="modalChapter" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered simpel-modal-dialog" role="document">
            <div class="modal-content simpel-modal-content">

                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close" style="z-index: 10; font-size: 11px; cursor: pointer;"></button>

                {{-- Modal Body --}}
                <div class="modal-body p-24">
                    {{-- Header Icon & Title --}}
                    <div class="d-flex align-items-center gap-3 mb-3 text-start">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-simple-gold-subtle text-simple-gold"
                            style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="ri-folder-2-line fs-4"></i>
                        </div>
                        <div>
                            <h5 class="simpel-modal-title mb-1">
                                {{ !empty($chapterForm['id']) ? 'Edit Bab Kurikulum' : 'Tambah Bab Baru' }}
                            </h5>
                            <p class="text-muted fs-8 mb-0">Tentukan nomor urut dan judul bab dalam kurikulum kelas.
                            </p>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form wire:submit.prevent="saveChapter">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold text-dark fs-8 mb-1">Nomor Urut Bab <span
                                    class="text-danger">*</span></label>
                            <input type="number" min="1" wire:model="chapterForm.order"
                                class="form-control radius-8 py-2" placeholder="Contoh: 1">
                            @error('chapterForm.order')
                                <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-4 text-start">
                            <label class="form-label fw-semibold text-dark fs-8 mb-1">Judul Bab Pembelajaran <span
                                    class="text-danger">*</span></label>
                            <input type="text" wire:model="chapterForm.title" class="form-control radius-8 py-2"
                                placeholder="Contoh: Bab 1: Dasar Regulasi & Pengantar">
                            @error('chapterForm.title')
                                <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="simpel-modal-actions">
                            <button type="button" class="btn simpel-modal-btn-cancel" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit"
                                class="btn btn-simple-gold w-50 py-10 radius-10 fw-semibold text-white">
                                <i class="ri-check-line me-1"></i> Simpan Bab
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Logout --}}
    <div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered simpel-modal-dialog" role="document">
            <div class="modal-content simpel-modal-content">

                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"
                    aria-label="Close" style="z-index: 10; font-size: 11px; cursor: pointer;"></button>

                {{-- Modal Body --}}
                <div class="modal-body simpel-modal-body">
                    {{-- Warning Icon Badge --}}
                    <div class="simpel-modal-icon-badge">
                        <i class="ri-logout-box-r-line"></i>
                    </div>

                    {{-- Title --}}
                    <h5 class="simpel-modal-title">
                        Konfirmasi Keluar
                    </h5>

                    {{-- Message with soft highlight box --}}
                    <div class="simpel-modal-msg-box text-center">
                        <p class="simpel-modal-msg">
                            Apakah Anda yakin ingin keluar?
                        </p>
                    </div>

                    {{-- Action Buttons (50/50 Balanced) --}}
                    <div class="simpel-modal-actions">
                        <button type="button" class="btn simpel-modal-btn-cancel" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="simpel-modal-form">
                            @csrf
                            <button type="submit" class="btn simpel-modal-btn-confirm">
                                <i class="ri-logout-box-r-line"></i> Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
