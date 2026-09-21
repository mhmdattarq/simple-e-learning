// 1. Listener Menutup Modal Bootstrap
window.addEventListener("closeModal", (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        if (el && typeof bootstrap !== "undefined") {
            const modal =
                bootstrap.Modal.getInstance(el) ||
                bootstrap.Modal.getOrCreateInstance(el);
            modal.hide();
        } else if (typeof $ !== "undefined") {
            $("#" + id).modal("hide");
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
window.addEventListener("showModal", (param) => {
    const id =
        param.detail?.id ??
        (Array.isArray(param.detail) ? param.detail[0]?.id : param.detail);
    if (id) {
        const el = document.getElementById(id);
        // Clean up any stray orphaned backdrops before showing
        if (typeof $ !== "undefined") {
            $(".modal-backdrop").not(".show").remove();
        }
        if (el && typeof bootstrap !== "undefined") {
            const modal = bootstrap.Modal.getOrCreateInstance(el);
            modal.show();
        } else if (typeof $ !== "undefined") {
            $("#" + id).modal("show");
        }
    }
});

// 3. Listener Reload DataTables Reaktif Tanpa Refresh Halaman
window.addEventListener("reloadDT", (param) => {
    const dtName =
        param.detail?.data ??
        (Array.isArray(param.detail) ? param.detail[0]?.data : param.detail);
    try {
        if (window[dtName]) {
            window[dtName].ajax.reload(null, false);
        } else if (window.dtTable) {
            window.dtTable.ajax.reload(null, false);
        } else {
            eval(dtName).ajax.reload(null, false);
        }
    } catch (e) {
        if (typeof $ !== "undefined") {
            $(".table.dataTable").each(function () {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().ajax.reload(null, false);
                }
            });
        }
    }
});

// 4. Lifecycle Livewire Navigation: Bersihkan state modal dan backdrop yatim
document.addEventListener("livewire:navigated", () => {
    if (typeof $ !== "undefined") {
        $(".modal-backdrop").remove();
        $("body")
            .removeClass("modal-open")
            .css("overflow", "")
            .css("padding-right", "");
    }
});
