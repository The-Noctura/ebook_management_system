// Tampilkan / sembunyikan password
document.querySelectorAll("[data-toggle-password]").forEach((btn) => {
    const input = btn.parentElement.querySelector("input");

    btn.addEventListener("click", () => {
        const show = input.type === "password";
        input.type = show ? "text" : "password";
        btn.setAttribute(
            "aria-label",
            show ? "Sembunyikan password" : "Tampilkan password",
        );
        btn.setAttribute("aria-pressed", String(show));
        btn.querySelector("[data-icon-show]").classList.toggle("hidden", show);
        btn.querySelector("[data-icon-hide]").classList.toggle("hidden", !show);
        input.focus();
    });
});

// Saat form dikirim: password kembali tersembunyi, tombol berubah jadi loading
function resetLoading(btn) {
    if (btn.dataset.original === undefined) return;
    btn.innerHTML = btn.dataset.original;
    btn.disabled = false;
    delete btn.dataset.original;
}

document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", () => {
        form.querySelectorAll("[data-toggle-password]").forEach((btn) => {
            const input = btn.parentElement.querySelector("input");
            if (input.type === "text") btn.click();
        });

        const btn = form.querySelector("[data-loading]");
        if (!btn || btn.disabled) return;

        // Ditunda satu tick agar pengiriman form tidak terganggu
        setTimeout(() => {
            btn.dataset.original = btn.innerHTML;
            btn.innerHTML =
                '<span class="size-4 animate-spin rounded-full border-2 border-current border-t-transparent" aria-hidden="true"></span>' +
                `<span>${btn.dataset.loading}</span>`;
            btn.disabled = true;
        }, 0);
    });
});

// Tombol Back browser: kembalikan tombol dari keadaan loading
window.addEventListener("pageshow", (e) => {
    if (e.persisted)
        document.querySelectorAll("[data-loading]").forEach(resetLoading);
});

// Snackbar hilang otomatis setelah 4 detik (dokumen 06 bagian 7.8)
document.querySelectorAll("[data-snackbar]").forEach((el) => {
    setTimeout(() => {
        el.style.transition = "opacity 200ms cubic-bezier(0.4, 0, 1, 1)";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 200);
    }, 4000);
});

// Sorotan "ebook baru": latar tint memudar ke surface (dokumen 06 bagian 6.4 dan 10.5)
document.querySelectorAll("[data-new]").forEach((el) => {
    el.style.transitionDuration = "300ms";
    requestAnimationFrame(() =>
        requestAnimationFrame(() => el.removeAttribute("data-new")),
    );
});

// Tombol dengan data-focus="id": fokus ke elemen itu (mis. "Coba Lagi" di Upload)
document.querySelectorAll("[data-focus]").forEach((btn) => {
    btn.addEventListener("click", () => {
        document.getElementById(btn.dataset.focus)?.focus();
    });
});

// ===== Halaman Upload (C11, wireframe Upload bagian 6.4) =====
// Semua opsional: tanpa JavaScript form tetap berfungsi.
// Kunci tombol saat submit sudah ditangani oleh data-loading di atas.
const MAX_UPLOAD_BYTES = 20 * 1024 * 1024; // sama dengan max:20480 KB di server

const uploadForm = document.querySelector("[data-upload-form]");

if (uploadForm) {
    const fileInput = uploadForm.querySelector("#file");
    const titleInput = uploadForm.querySelector("#title");
    const fileError = uploadForm.querySelector("[data-file-error]");
    const fileErrorText = uploadForm.querySelector("[data-file-error-text]");
    const serverError = uploadForm.querySelector("#file-msg"); // bisa null
    const offlineAlert = uploadForm.querySelector("[data-offline-alert]");
    const defaultPlaceholder = titleInput.placeholder;

    function clearFileError() {
        fileError.hidden = true;
        fileInput.removeAttribute("aria-invalid");
        fileInput.classList.remove("border-error", "animate-shake");
        fileInput.classList.add("border-outline-strong");
    }

    function showFileError(message) {
        serverError?.setAttribute("hidden", "");
        fileErrorText.textContent = message;
        fileError.hidden = false;
        fileInput.setAttribute("aria-invalid", "true");
        fileInput.classList.remove("border-outline-strong");
        fileInput.classList.add("border-error");
        // Mulai ulang animasi getar
        fileInput.classList.remove("animate-shake");
        void fileInput.offsetWidth;
        fileInput.classList.add("animate-shake");
    }

    // 2. Placeholder Judul mengikuti nama file; 3. tolak file di atas 20 MB
    fileInput.addEventListener("change", () => {
        clearFileError();
        serverError?.setAttribute("hidden", "");
        offlineAlert.hidden = true;

        const file = fileInput.files[0];

        if (!file) {
            titleInput.placeholder = defaultPlaceholder;
            return;
        }

        if (file.size > MAX_UPLOAD_BYTES) {
            fileInput.value = "";
            titleInput.placeholder = defaultPlaceholder;
            showFileError("Ukuran file maksimal 20 MB.");
            return;
        }

        titleInput.placeholder = file.name.replace(/\.[^.]+$/, "");
    });

    // 3 dan 4. Cek ukuran dan koneksi sebelum dikirim.
    // Fase capture di document agar berjalan SEBELUM handler data-loading;
    // bila submit dibatalkan, tombol tidak ikut terkunci.
    document.addEventListener(
        "submit",
        (e) => {
            if (e.target !== uploadForm) return;

            const block = () => {
                e.preventDefault();
                e.stopImmediatePropagation();
            };

            const file = fileInput.files[0];
            if (file && file.size > MAX_UPLOAD_BYTES) {
                block();
                showFileError("Ukuran file maksimal 20 MB.");
                return;
            }

            if (!navigator.onLine) {
                block();
                offlineAlert.hidden = false;
                return;
            }

            offlineAlert.hidden = true;
        },
        true,
    );
}
