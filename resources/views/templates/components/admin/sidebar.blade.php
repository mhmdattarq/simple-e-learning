<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div wire:key="admin-sidebar-root" wire:ignore>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <i class="ri-close-line"></i>
        </button>
        <div class="brand-container">
            <a href="{{ route('admin.dashboard') }}" class="brand text-decoration-none">
                <div class="seal">S</div>
                <div>
                    <strong>SIMPEL</strong>
                    <small>BKPSDM Aceh Timur</small>
                </div>
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu"
                x-data="{
                    activeDropdown: '{{ request()->routeIs('kategori.*') ? 'kategori' : (request()->routeIs(['kelas.*', 'materi.*']) ? 'kelas' : (request()->routeIs('evaluasi.*') ? 'evaluasi' : '')) }}',
                    toggle(name) {
                        this.activeDropdown = (this.activeDropdown === name) ? '' : name;
                    },
                    init() {
                        document.addEventListener('livewire:navigated', () => {
                            const path = window.location.pathname;
                            if (path.includes('/kategori')) this.activeDropdown = 'kategori';
                            else if (path.includes('/kelas') || path.includes('/materi')) this.activeDropdown = 'kelas';
                            else if (path.includes('/evaluasi')) this.activeDropdown = 'evaluasi';
                            else this.activeDropdown = '';
                        });
                    }
                }">
                <li class="sidebar-menu-group-title">MENU UTAMA</li>

                {{-- Beranda --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active-page' : '' }}"
                        href="{{ route('admin.dashboard') }}" title="Beranda" wire:navigate>
                        <i class="ri-home-2-line menu-icon"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                {{-- Kategori Kelas --}}
                <li class="dropdown {{ request()->routeIs('kategori.*') ? 'open is-active-module' : '' }}"
                    :class="{
                        'open': activeDropdown === 'kategori',
                        'is-active-module': {{ request()->routeIs('kategori.*') ? 'true' : 'false' }}
                    }">
                    <a href="javascript:void(0)" @click.prevent.stop="toggle('kategori')" title="Kategori Kelas">
                        <i class="ri-folder-3-line menu-icon"></i>
                        <span>Kategori</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="{{ request()->routeIs('kategori.create') ? 'active-page' : '' }}">
                            <a href="{{ route('kategori.create') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Tambah Kategori</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('kategori.data') ? 'active-page' : '' }}">
                            <a href="{{ route('kategori.data') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Data Kategori</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Kelas --}}
                <li class="dropdown {{ request()->routeIs(['kelas.*', 'materi.*']) ? 'open is-active-module' : '' }}"
                    :class="{
                        'open': activeDropdown === 'kelas',
                        'is-active-module': {{ request()->routeIs(['kelas.*', 'materi.*']) ? 'true' : 'false' }}
                    }">
                    <a href="javascript:void(0)" @click.prevent.stop="toggle('kelas')" title="Kelas">
                        <i class="ri-file-list-3-line menu-icon"></i>
                        <span>Kelola Kelas</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="{{ request()->routeIs('kelas.create') ? 'active-page' : '' }}">
                            <a href="{{ route('kelas.create') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Tambah Kelas</span>
                            </a>
                        </li>
                        <li class="{{ (request()->routeIs('kelas.data') || request()->routeIs('materi.*')) ? 'active-page' : '' }}">
                            <a href="{{ route('kelas.data') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Data Kelas</span>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Evaluasi & Kuis (placeholder) --}}
                <li class="dropdown {{ request()->routeIs('evaluasi.*') ? 'open is-active-module' : '' }}"
                    :class="{
                        'open': activeDropdown === 'evaluasi',
                        'is-active-module': {{ request()->routeIs('evaluasi.*') ? 'true' : 'false' }}
                    }">
                    <a href="javascript:void(0)" @click.prevent.stop="toggle('evaluasi')" title="Evaluasi & Kuis">
                        <i class="ri-star-smile-line menu-icon"></i>
                        <span>Evaluasi &amp; Kuis</span>
                    </a>
                    <ul class="sidebar-submenu">
                        <li class="{{ request()->routeIs('evaluasi.create') ? 'active-page' : '' }}">
                            <a href="{{ route('evaluasi.create') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Tambah Evaluasi &amp; Kuis</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('evaluasi.data') ? 'active-page' : '' }}">
                            <a href="{{ route('evaluasi.data') }}" wire:navigate>
                                <i class="ri-circle-fill circle-icon"></i> <span>Data Evaluasi &amp; Kuis</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="sidebar-foot">
            <div class="small motto-text">
                Aksi Perubahan Kinerja 2026<br>
                <strong class="text-light-emphasis">Efektif · Efisien · Transparan · Akuntabel</strong>
            </div>
        </div>
    </aside>

    <style>
        /* =========================================================
           SIMPEL Admin Sidebar - Scoped Enhancements & Fixes
           ========================================================= */

        /* Top-level menu items */
        .sidebar-menu > li > a {
            color: #cfdaea !important;
            border-radius: 10px !important;
            margin: 1px 4px !important;
            padding: 9px 12px !important;
            height: 40px !important;
            line-height: 1.2 !important;
            font-weight: 500 !important;
            font-size: 13.5px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            transition: background 0.15s ease, color 0.15s ease !important;
            cursor: pointer !important;
        }

        .sidebar-menu > li > a .menu-icon {
            font-size: 18px !important;
            flex-shrink: 0 !important;
            color: #8da2c0 !important;
            transition: color 0.15s ease !important;
            margin: 0 !important;
            width: auto !important;
        }

        /* Active Page (e.g. Beranda) */
        .sidebar-menu > li > a.active-page,
        .sidebar-menu > li.active-page > a {
            background: #f3bc42 !important;
            color: #071a33 !important;
            font-weight: 700 !important;
        }

        .sidebar-menu > li > a.active-page .menu-icon,
        .sidebar-menu > li.active-page > a .menu-icon {
            color: #071a33 !important;
        }

        /* Active Module Parent (Solid Simple-Gold when on that module's page) */
        .sidebar-menu > li.dropdown.is-active-module > a {
            background-color: #f3bc42 !important;
            background: #f3bc42 !important;
            color: #071a33 !important;
            font-weight: 700 !important;
        }

        .sidebar-menu > li.dropdown.is-active-module > a .menu-icon,
        .sidebar-menu > li.dropdown.is-active-module > a::after {
            color: #071a33 !important;
        }

        /* Dropdown Parent when merely Open (Browsing, not active module page) */
        .sidebar-menu > li.dropdown.open:not(.is-active-module) > a {
            background: rgba(243, 188, 66, 0.14) !important;
            color: #f3bc42 !important;
            font-weight: 600 !important;
        }

        .sidebar-menu > li.dropdown.open:not(.is-active-module) > a .menu-icon,
        .sidebar-menu > li.dropdown.open:not(.is-active-module) > a::after {
            color: #f3bc42 !important;
        }

        /* Arrow indicator rotation when open */
        .sidebar-menu > li.dropdown.open > a::after {
            transform: translateY(-50%) rotate(90deg) !important;
        }

        .sidebar-menu > li.dropdown > a::after {
            color: #8da2c0 !important;
            transition: transform 0.2s ease, color 0.2s ease !important;
        }

        /* Top-level hover */
        .sidebar-menu > li > a:hover {
            background: rgba(243, 188, 66, 0.12) !important;
            color: #f3bc42 !important;
        }

        .sidebar-menu > li > a:hover .menu-icon,
        .sidebar-menu > li > a:hover::after {
            color: #f3bc42 !important;
        }

        /* Submenu Tree Container - Silky Smooth CSS Accordion */
        .sidebar-menu .sidebar-submenu {
            display: block !important;
            list-style: none !important;
            margin: 0 6px 0 18px !important;
            padding-left: 10px !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            padding-right: 0 !important;
            border-left: 2px solid rgba(243, 188, 66, 0.25) !important;
            background: transparent !important;
            max-height: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.2s ease, margin 0.2s ease, padding 0.2s ease, visibility 0.25s !important;
        }

        .sidebar-menu li.dropdown.open > .sidebar-submenu {
            max-height: 250px !important;
            opacity: 1 !important;
            visibility: visible !important;
            margin: 4px 6px 8px 18px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }

        .sidebar-menu .sidebar-submenu li {
            margin: 2px 0 !important;
            padding: 0 !important;
        }

        /* Submenu Child Links (Clean & well-proportioned) */
        .sidebar-menu .sidebar-submenu li a {
            height: auto !important;
            min-height: 32px !important;
            padding: 6px 10px !important;
            margin: 1px 0 !important;
            border-radius: 6px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            line-height: 1.3 !important;
            color: #94a3b8 !important;
            background: transparent !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
        }

        .sidebar-menu .sidebar-submenu li a:hover {
            color: #f3bc42 !important;
            background: rgba(243, 188, 66, 0.08) !important;
        }

        /* Submenu Active Link (Subtle pill, no giant button) */
        .sidebar-menu .sidebar-submenu li.active-page > a,
        .sidebar-menu .sidebar-submenu li > a.active,
        .sidebar-menu .sidebar-submenu li > a.active-page {
            color: #f3bc42 !important;
            background: rgba(243, 188, 66, 0.16) !important;
            font-weight: 600 !important;
        }

        /* Submenu Circle Bullet Icon (Exact alignment and size) */
        .sidebar-menu .sidebar-submenu li a i.circle-icon,
        .sidebar-menu .sidebar-submenu li a .circle-icon {
            width: 6px !important;
            min-width: 6px !important;
            max-width: 6px !important;
            height: 6px !important;
            font-size: 6px !important;
            line-height: 1 !important;
            margin: 0 !important;
            margin-inline-end: 0 !important;
            padding: 0 !important;
            color: #64748b !important;
            transition: color 0.15s ease !important;
            flex-shrink: 0 !important;
            display: inline-block !important;
        }

        .sidebar-menu .sidebar-submenu li a:hover i.circle-icon,
        .sidebar-menu .sidebar-submenu li a:hover .circle-icon,
        .sidebar-menu .sidebar-submenu li.active-page > a i.circle-icon,
        .sidebar-menu .sidebar-submenu li.active-page > a .circle-icon,
        .sidebar-menu .sidebar-submenu li > a.active i.circle-icon,
        .sidebar-menu .sidebar-submenu li > a.active .circle-icon,
        .sidebar-menu .sidebar-submenu li > a.active-page i.circle-icon,
        .sidebar-menu .sidebar-submenu li > a.active-page .circle-icon {
            color: #f3bc42 !important;
        }
    </style>
</div>

