const photoInput = document.getElementById("photo");
const photoPreview = document.getElementById("photoPreview");
const optionModal = document.getElementById("memberOptionModal");
const optionForm = document.getElementById("memberOptionForm");
const optionName = document.getElementById("memberOptionName");
const optionTitle = document.getElementById("memberOptionTitle");
const optionError = document.getElementById("memberOptionError");

let activeOptionButton = null;

if (photoInput && photoPreview) {
    photoInput.addEventListener("change", (event) => {
        const file = event.target.files?.[0];

        if (!file) {
            return;
        }

        const reader = new FileReader();

        reader.addEventListener("load", (loadEvent) => {
            photoPreview.innerHTML = `<img src="${loadEvent.target.result}" alt="Preview Foto">`;
        });

        reader.readAsDataURL(file);
    });
}

function closeOptionModal() {
    if (!optionModal) {
        return;
    }

    optionModal.hidden = true;
    optionForm?.reset();
    if (optionError) {
        optionError.hidden = true;
        optionError.textContent = "";
    }
}

document.querySelectorAll("[data-option-type]").forEach((button) => {
    button.addEventListener("click", () => {
        activeOptionButton = button;
        optionTitle.textContent =
            button.dataset.optionType === "position"
                ? "Tambah Jabatan Lapangan"
                : "Tambah Spesialisasi";
        optionModal.hidden = false;
        optionName.focus();
    });
});

document.querySelectorAll("[data-option-close]").forEach((button) => {
    button.addEventListener("click", closeOptionModal);
});

optionModal?.addEventListener("click", (event) => {
    if (event.target === optionModal) {
        closeOptionModal();
    }
});

optionForm?.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!activeOptionButton) {
        return;
    }

    const response = await fetch(activeOptionButton.dataset.optionUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                .value,
        },
        body: JSON.stringify({ name: optionName.value.trim() }),
    });

    if (!response.ok) {
        const result = await response.json().catch(() => ({}));
        optionError.textContent =
            result.message || "Pilihan tidak dapat ditambahkan.";
        optionError.hidden = false;
        return;
    }

    const option = await response.json();
    const select = activeOptionButton
        .closest(".form-group")
        .querySelector("select");
    select.add(new Option(option.name, option.name, true, true));
    closeOptionModal();
});
