<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public $modalId = 'modalDelete';

    public $data = [];

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
};
?>

<div>
    <div class="modal fade" id="{{ $data['modalId'] ?? $modalId }}" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg position-relative" style="border-radius: 20px; overflow: hidden; background-color: #ffffff;">

                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; font-size: 11px;"></button>

                {{-- Modal Body (Clean, Centered, Elegant) --}}
                <div class="modal-body p-28 p-md-32 text-center">
                    {{-- Warning Icon Badge --}}
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background-color: #fef2f2; color: #dc2626; font-size: 28px; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.15);">
                        <i class="ri-delete-bin-line"></i>
                    </div>

                    {{-- Title --}}
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 18px; letter-spacing: -0.3px;">
                        {{ $data['title'] ?? 'Konfirmasi Hapus' }}
                    </h5>

                    {{-- Message with soft highlight box --}}
                    <div class="p-16 rounded-12 mt-3 text-start" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <p class="text-secondary mb-0" style="font-size: 13px; line-height: 1.55;">
                            {{ $data['msg'] ?? 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.' }}
                        </p>
                    </div>

                    {{-- Action Buttons (50/50 Balanced) --}}
                    <div class="d-flex align-items-center gap-2 mt-24">
                        <button type="button" class="btn btn-light text-dark fw-semibold w-50 py-10" data-bs-dismiss="modal"
                            style="border: 1px solid #d1d5db; border-radius: 10px; font-size: 13.5px; transition: all 0.15s ease;">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger fw-semibold w-50 py-10 d-flex align-items-center justify-content-center gap-2"
                            data-bs-dismiss="modal" wire:click="process({{ $data['id'] ?? 0 }})"
                            style="background-color: #dc2626; border-color: #dc2626; border-radius: 10px; font-size: 13.5px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); transition: all 0.15s ease;">
                            <i class="ri-delete-bin-line"></i> Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Logout --}}
    <div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg position-relative" style="border-radius: 20px; overflow: hidden; background-color: #ffffff;">

                {{-- Close Button --}}
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10; font-size: 11px;"></button>

                {{-- Modal Body --}}
                <div class="modal-body p-28 p-md-32 text-center">
                    {{-- Warning Icon Badge --}}
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px; background-color: #fef2f2; color: #dc2626; font-size: 28px; box-shadow: 0 8px 24px rgba(220, 38, 38, 0.15);">
                        <i class="ri-logout-box-r-line"></i>
                    </div>

                    {{-- Title --}}
                    <h5 class="fw-bold text-dark mb-2" style="font-size: 18px; letter-spacing: -0.3px;">
                        Konfirmasi Keluar
                    </h5>

                    {{-- Message with soft highlight box --}}
                    <div class="p-16 rounded-12 mt-3 text-center" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <p class="text-secondary mb-0" style="font-size: 13.5px; line-height: 1.55;">
                            Apakah Anda yakin ingin keluar?
                        </p>
                    </div>

                    {{-- Action Buttons (50/50 Balanced) --}}
                    <div class="d-flex align-items-center gap-2 mt-24">
                        <button type="button" class="btn btn-light text-dark fw-semibold w-50 py-10" data-bs-dismiss="modal"
                            style="border: 1px solid #d1d5db; border-radius: 10px; font-size: 13.5px; transition: all 0.15s ease;">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="w-50 m-0 p-0">
                            @csrf
                            <button type="submit" class="btn btn-danger fw-semibold w-100 py-10 d-flex align-items-center justify-content-center gap-2"
                                style="background-color: #dc2626; border-color: #dc2626; border-radius: 10px; font-size: 13.5px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); transition: all 0.15s ease;">
                                <i class="ri-logout-box-r-line"></i> Ya, Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
