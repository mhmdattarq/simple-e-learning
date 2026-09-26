<?php

use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $isShow = false;

    public $type = 'success';

    public $msg = '';

    public function mount()
    {
        if (session()->has('alert-show')) {
            $this->show(session('alert-show'));
        }
    }

    #[On('alert-show')]
    #[On('alert')]
    public function show($data = [], ?string $type = null, ?string $message = null)
    {
        if (is_array($data)) {
            $this->type = $data['type'] ?? ($type ?? 'success');
            $this->msg = $data['message'] ?? ($data['msg'] ?? ($message ?? ''));
        } else {
            $this->msg = (string) $data;
            if ($type) {
                $this->type = $type;
            }
        }

        if ($this->type === 'error') {
            $this->type = 'danger';
        }

        $this->isShow = true;
    }
};
?>

<div>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        @if ($isShow)
            <div class="toast show align-items-center text-white bg-{{ $type }} border-0 shadow-lg" role="alert"
                style="border-radius: 12px; min-width: 300px;" x-data="{ show: true }" x-init="setTimeout(() => {
                    show = false;
                    $wire.set('isShow', false);
                }, 4000)"
                x-show="show" x-transition>
                <div class="d-flex align-items-center justify-content-between p-2">
                    <div class="toast-body d-flex align-items-center gap-2 py-1 px-2">
                        @php
                            $toastIcon = match($type) {
                                'danger', 'error' => 'ri-error-warning-fill',
                                'warning' => 'ri-alert-fill',
                                'info' => 'ri-information-fill',
                                default => 'ri-checkbox-circle-fill',
                            };
                        @endphp
                        <i class="{{ $toastIcon }} fs-5 ms-3"></i>
                        <span class="fs-7 fw-medium">{{ $msg }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-3" wire:click="$set('isShow', false)"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    (function() {
        function registerLivewireErrorInterceptor() {
            if (typeof Livewire !== 'undefined' && Livewire.hook) {
                if (window._livewire413HookRegistered) return;
                window._livewire413HookRegistered = true;

                Livewire.hook('request', ({ fail }) => {
                    fail(({ status, preventDefault }) => {
                        if (status === 413) {
                            preventDefault();
                            Livewire.dispatch('alert-show', {
                                data: {
                                    type: 'danger',
                                    message: 'Ukuran berkas atau data melebihi batas maksimal server (413 Request Entity Too Large).'
                                }
                            });
                        }
                    });
                });
            }
        }

        if (typeof Livewire !== 'undefined') {
            registerLivewireErrorInterceptor();
        } else {
            document.addEventListener('livewire:init', registerLivewireErrorInterceptor);
        }
    })();
</script>
