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
