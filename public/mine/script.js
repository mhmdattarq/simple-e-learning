// 1. Listener Menutup Modal Bootstrap
window.addEventListener("closeModal", (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        if (el) {
            let closed = false;
            if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
                try {
                    const modal =
                        (typeof bootstrap.Modal.getInstance === "function" ? bootstrap.Modal.getInstance(el) : null) ||
                        (typeof bootstrap.Modal.getOrCreateInstance === "function" ? bootstrap.Modal.getOrCreateInstance(el) : null);
                    if (modal) {
                        modal.hide();
                        closed = true;
                    }
                } catch (e) {
                    console.warn("Bootstrap modal hide error:", e);
                }
            }
            if (!closed && typeof $ !== "undefined") {
                try {
                    $("#" + id).modal("hide");
                } catch (e) {
                    console.warn("jQuery modal hide error:", e);
                }
            }
        }
        setTimeout(() => {
            if (typeof $ !== "undefined") {
                if (!$(".modal.show").length) {
                    $(".modal-backdrop").remove();
                    $("body")
                        .removeClass("modal-open")
                        .css("overflow", "")
                        .css("padding-right", "");
                }
            } else {
                const backdrops = document.querySelectorAll(".modal-backdrop");
                backdrops.forEach((b) => b.remove());
                document.body.classList.remove("modal-open");
                document.body.style.overflow = "";
                document.body.style.paddingRight = "";
            }
        }, 150);
    }
});

// 2. Listener Membuka Modal Bootstrap
const handleShowModal = (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        // Clean up any stray orphaned backdrops before showing
        if (typeof $ !== "undefined") {
            $(".modal-backdrop").not(".show").remove();
        }
        let shown = false;
        if (el && typeof bootstrap !== "undefined" && bootstrap.Modal) {
            try {
                const modal =
                    (typeof bootstrap.Modal.getOrCreateInstance === "function"
                        ? bootstrap.Modal.getOrCreateInstance(el)
                        : (typeof bootstrap.Modal.getInstance === "function"
                            ? bootstrap.Modal.getInstance(el)
                            : null) || new bootstrap.Modal(el));
                if (modal) {
                    modal.show();
                    shown = true;
                }
            } catch (e) {
                console.warn("Bootstrap modal show error:", e);
            }
        }
        if (!shown && typeof $ !== "undefined") {
            try {
                $("#" + id).modal("show");
            } catch (e) {
                console.warn("jQuery modal show error:", e);
            }
        }
    }
};
window.addEventListener("showModal", handleShowModal);
window.addEventListener("openModal", handleShowModal);

// 3. Listener Reload DataTables Reaktif Tanpa Refresh Halaman (Robust multi-table & SPA safe)
window.addEventListener("reloadDT", (param) => {
    let reloaded = false;

    // 3a. Reload semua tabel DataTables yang sedang aktif di halaman saat ini
    if (typeof $ !== "undefined" && $.fn.DataTable) {
        $(".table.dataTable, table.dataTable, table[id^='table']").each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                try {
                    $(this).DataTable().ajax.reload(null, false);
                    reloaded = true;
                } catch (e) {
                    console.warn("Gagal reload DataTable aktif:", e);
                }
            }
        });
    }

    // 3b. Fallback target spesifik jika ada
    if (!reloaded) {
        const dtName =
            param.detail?.data ??
            (Array.isArray(param.detail) ? param.detail[0]?.data : param.detail);
        try {
            if (window[dtName] && typeof window[dtName].ajax?.reload === "function") {
                window[dtName].ajax.reload(null, false);
            } else if (window.dtTable && typeof window.dtTable.ajax?.reload === "function") {
                window.dtTable.ajax.reload(null, false);
            }
        } catch (e) {
            console.warn("Fallback reloadDT gagal:", e);
        }
    }
});


// 5. Lifecycle Livewire Navigation: Bersihkan state modal, dropdown, dan sinkronisasi navigasi
document.addEventListener("livewire:navigated", () => {
    if (typeof $ !== "undefined") {
        $(".modal-backdrop").remove();
        $("body")
            .removeClass("modal-open")
            .removeClass("locked")
            .css("overflow", "")
            .css("padding-right", "");

        // Tutup sidebar dan mobile drawer yang mungkin aktif
        $(".sidebar").removeClass("sidebar-open");
        $("body").removeClass("overlay-active");
        $(".mobile-nav__wrapper").removeClass("expanded");
        $("body").removeClass("locked");

        // Tutup dropdown yang mungkin tertinggal dari halaman sebelumnya
        $(".dropdown-menu.show").removeClass("show");
        $('[data-bs-toggle="dropdown"].show')
            .removeClass("show")
            .attr("aria-expanded", "false");
            
        // Sinkronisasi sticky header & mobile nav jika ada
        const mainMenu = document.querySelector(".main-header .main-menu");
        const stickyContent = document.querySelector(".sticky-header__content");
        if (mainMenu && stickyContent) {
            stickyContent.innerHTML = mainMenu.innerHTML;
        }

        const mainMenuList = document.querySelector(".main-header .main-menu__list");
        const mobileContainer = document.querySelector(".mobile-nav__container");
        if (mainMenuList && mobileContainer) {
            mobileContainer.innerHTML = mainMenuList.outerHTML;
        }
    }
});

// Sinkronisasi mobile nav saat halaman pertama kali dimuat
document.addEventListener("DOMContentLoaded", () => {
    const mainMenuList = document.querySelector(".main-header .main-menu__list");
    const mobileContainer = document.querySelector(".mobile-nav__container");
    if (mainMenuList && mobileContainer && !mobileContainer.innerHTML.trim()) {
        mobileContainer.innerHTML = mainMenuList.outerHTML;
    }
});

// 6. Bridge Event jQuery ke Livewire / Native DOM (misal niceSelect)
if (typeof $ !== "undefined") {
    $(document).on("change", "select", function (e) {
        if (!e.originalEvent && typeof this.dispatchEvent === "function") {
            this.dispatchEvent(new Event("input", { bubbles: true }));
            this.dispatchEvent(new Event("change", { bubbles: true }));
        }
    });

    // Delegasi klik mobile nav toggler agar selalu responsif setelah navigasi SPA
    $(document).on("click", ".mobile-nav__toggler", function (e) {
        e.preventDefault();
        $(".mobile-nav__wrapper").toggleClass("expanded");
        $("body").toggleClass("locked");
    });

    // Auto-tutup drawer navigasi saat tautan di dalamnya diklik
    $(document).on("click", ".mobile-nav__container a, .mobile-nav__content a:not(.mobile-nav__toggler)", function () {
        $(".mobile-nav__wrapper").removeClass("expanded");
        $("body").removeClass("locked");
    });

    // Auto-tutup sidebar admin saat menu diklik di perangkat mobile (< 1200px)
    $(document).on("click", ".sidebar-menu a", function () {
        if (window.innerWidth < 1200) {
            $(".sidebar").removeClass("sidebar-open");
            $("body").removeClass("overlay-active");
        }
    });

    // Tutup sidebar saat klik backdrop overlay di admin
    $(document).on("click", ".sidebar-overlay", function () {
        $(".sidebar").removeClass("sidebar-open");
        $("body").removeClass("overlay-active");
    });

    // Delegasi klik bootstrap dropdown fallback jika listener native terlepas
    $(document).on("click", '[data-bs-toggle="dropdown"]', function (e) {
        // Abaikan jika sudah dikelola oleh Alpine (x-data)
        if (this.closest('[x-data]')) {
            return;
        }
        if (typeof bootstrap !== "undefined" && bootstrap.Dropdown) {
            const dropdown = bootstrap.Dropdown.getOrCreateInstance(this);
            if (!this.classList.contains("show")) {
                dropdown.show();
            }
        }
    });
}


