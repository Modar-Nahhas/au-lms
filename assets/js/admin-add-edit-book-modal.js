const alpha30 = /^[A-Za-z ]{1,30}$/;

function clearValidationStatusForAddEditBookModal() {
    const isbn = document.getElementById("bookISBN");
    const title = document.getElementById("bookTitle");
    const author = document.getElementById("bookAuthor");
    const pub = document.getElementById("bookPublisher");
    const lang = document.getElementById("bookLanguage");
    const cat = document.getElementById("bookCategory");
    const description = document.getElementById("descriptionInput");

    [isbn, title, author, pub, lang, cat, description].forEach(i => i && clearInvalid(i));
    return {isbn, title, author, pub, lang, cat, description};
}

function isValidISBN(isbn) {
    isbn = isbn.replace(/[-\s]/g, "");

    // ISBN-10
    if (/^\d{9}[\dXx]$/.test(isbn)) {
        let sum = 0;
        for (let i = 0; i < 9; i++) sum += (isbn[i] * (10 - i));
        let check = isbn[9].toUpperCase() === "X" ? 10 : Number(isbn[9]);
        return (sum + check) % 11 === 0;
    }

    // ISBN-13
    if (/^\d{13}$/.test(isbn)) {
        let sum = 0;
        for (let i = 0; i < 13; i++) {
            sum += Number(isbn[i]) * (i % 2 === 0 ? 1 : 3);
        }
        return sum % 10 === 0;
    }

    return false;
}


function validateBookForm() {
    let valid = true;
    const {
        isbn,
        title,
        author,
        pub,
        lang,
        cat,
        description
    } = clearValidationStatusForAddEditBookModal();

    // ----- ISBN -----
    let isbnValue = isbn.value.trim();
    if (!isbnValue) {
        setInvalid(isbn, "ISBN is required.");
        valid = false;
    }
    // else if (!isValidISBN(isbnValue)) {
    //     setInvalid(isbn, "Invalid ISBN-10 or ISBN-13.");
    //     valid = false;
    // }

    // ----- TITLE -----
    let titleValue = title.value.trim();
    if (!titleValue) {
        setInvalid(title, "Title is required.");
        valid = false;
    } else if (!alpha30.test(titleValue)) {
        setInvalid(title, "Title must be letters/spaces, 1–30 chars.");
        valid = false;
    }

    // ----- AUTHOR -----
    let authorValue = author.value.trim();
    if (!authorValue) {
        setInvalid(author, "Author is required.");
        valid = false;
    } else if (!alpha30.test(authorValue)) {
        setInvalid(author, "Author must be letters/spaces, 1–30 chars.");
        valid = false;
    }

    // ----- PUBLISHER -----
    let publisherValue = pub.value.trim();
    if (!publisherValue) {
        setInvalid(pub, "Publisher is required.");
        valid = false;
    } else if (!alpha30.test(publisherValue)) {
        setInvalid(pub, "Publisher must be letters/spaces, 1–30 chars.");
        valid = false;
    }

    // ----- LANGUAGE -----
    let langValue = lang.value.trim();
    if (!langValue) {
        setInvalid(lang, "Please select a language.");
        valid = false;
    }

    // ----- CATEGORY -----
    let catValue = cat.value.trim();
    if (!catValue) {
        setInvalid(cat, "Please select a category.");
        valid = false;
    }

    return valid;
}

let isEditing = false;


function getFormInputsHtmlElements() {
    const idInput = document.getElementById("book-id");
    const isbnInput = document.getElementById("bookISBN");
    const titleInput = document.getElementById("bookTitle");
    const authorInput = document.getElementById("bookAuthor");
    const pubInput = document.getElementById("bookPublisher");
    const catSelect = document.getElementById("bookCategory");
    const langSelect = document.getElementById("bookLanguage");
    const statusSelect = document.getElementById("bookStatus");
    const descriptionInput = document.getElementById("bookDescription");
    const label = document.getElementById("bookModalLabel");
    return {
        idInput,
        isbnInput,
        titleInput,
        authorInput,
        pubInput,
        catSelect,
        langSelect,
        statusSelect,
        descriptionInput,
        label
    };
}

function openAddBookModal() {
    removeBookImage();
    isEditing = false;

    const {
        idInput,
        isbnInput,
        titleInput,
        authorInput,
        pubInput,
        catSelect,
        langSelect,
        statusSelect,
        descriptionInput,
        label
    } = getFormInputsHtmlElements();

    if (label) label.textContent = "Add Book";
    if (idInput) idInput.value = "";
    if (isbnInput) isbnInput.value = "";
    if (titleInput) titleInput.value = "";
    if (authorInput) authorInput.value = "";
    if (pubInput) pubInput.value = "";
    if (catSelect) catSelect.value = "";
    if (langSelect) langSelect.value = "";
    if (descriptionInput) descriptionInput.value = "";
    if (statusSelect) statusSelect.value = "Available";

    clearValidationStatusForAddEditBookModal();

    $('#bookModal').modal('show');
}

function openEditBookModal(book) {
    isEditing = true;

    const {
        idInput,
        isbnInput,
        titleInput,
        authorInput,
        pubInput,
        catSelect,
        langSelect,
        statusSelect,
        descriptionInput,
        label
    } = getFormInputsHtmlElements();

    if (label) label.textContent = "Edit Book";
    if (idInput) idInput.value = book.id;
    if (isbnInput) isbnInput.value = book.isbn;
    if (titleInput) titleInput.value = book.title;
    if (authorInput) authorInput.value = book.author;
    if (pubInput) pubInput.value = book.publisher || "";
    if (catSelect) catSelect.value = book.category || "";
    if (langSelect) langSelect.value = book.language || "";
    if (descriptionInput) descriptionInput.value = book.description || "";
    if (statusSelect) statusSelect.value = book.status || "Available";

    displayBookImage(book);

    clearValidationStatusForAddEditBookModal();

    $('#bookModal').modal('show');
}

function submitBookAddEditFormHandler() {

    // ============================================================
    //   SUBMIT HANDLER FOR CREATE / UPDATE
    // ============================================================
    const form = document.getElementById("book-form");
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        validateBookForm()
        if (!validateBookForm()) return;
        const saveBtn = document.getElementById('save-book-btn');
        const formData = new FormData(this);

        saveBtn.setAttribute('disabled', true);
        saveBtn.textContent = 'Saving...';

        fetch('/actions/admin-add-edit-book-action.php', {
            method: 'POST',
            body: formData

        })
            .then(res => res.json())
            .then(data => {
                saveBtn.removeAttribute('disabled');
                saveBtn.textContent = 'Save Book';

                if (!data.success) {
                    // Validation errors from PHP
                    showErrors(data.errors);
                    return;
                }

                // SUCCESS — Refresh table & close modal
                bookTable.ajax.reload(null, false);
                $('#bookModal').modal('hide');
                form.reset();
            })
            .catch(err => {
                console.error(err);
                saveBtn.removeAttribute('disabled');
                saveBtn.textContent = 'Save Book';
                alert('An unexpected error occurred.');
            });
    });


}

function addOpenAddModalEventHandler() {
    const addBtn = document.getElementById("btn-add-book");
    if (!addBtn) return;
    addBtn.addEventListener("click", openAddBookModal);
}


// ============================================================
//   ERROR DISPLAY HELPERS
// ============================================================

function showErrors(errors) {
    const form = document.getElementById("book-form");

    for (const field in errors) {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');

            const errorDiv = input.parentElement.querySelector('.invalid-feedback') ?? input.closest('.form-group')?.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.textContent = errors[field];
                errorDiv.classList.remove('d-none');
            }
        }
    }
}

function clearErrors() {
    const form = document.getElementById("book-form");

    form.querySelectorAll('.is-invalid').forEach(el => {
        el.classList.remove('is-invalid');
    });

    form.querySelectorAll('.invalid-feedback').forEach(el => {
        el.classList.add('d-none');
    });
}

function displayBookImage(book) {
    const previewWrapper = document.getElementById('coverPreviewWrapper');
    const previewImg = document.getElementById('coverPreview');
    previewImg.src = '/assets/img/' + book.coverImage;
    previewWrapper.classList.remove('d-none');
}

function removeBookImage() {
    const previewWrapper = document.getElementById('coverPreviewWrapper');
    const previewImg = document.getElementById('coverPreview');
    previewImg.src = '';
    previewWrapper.classList.add('d-none');
}

function previewUploadedCoverImage() {
    const fileInput = document.getElementById('bookCover');
    const label = document.querySelector('label.custom-file-label[for="bookCover"]');
    const previewWrapper = document.getElementById('coverPreviewWrapper');
    const previewImg = document.getElementById('coverPreview');

    if (!fileInput || !label || !previewWrapper || !previewImg) {
        return;
    }

    fileInput.addEventListener('change', function () {
        const file = fileInput.files && fileInput.files[0];

        // Update label text
        if (file) {
            label.textContent = file.name;
        } else {
            label.textContent = 'Choose cover image...';
        }

        // Update preview
        if (file) {
            const url = URL.createObjectURL(file);
            previewImg.src = url;
            previewWrapper.classList.remove('d-none');
        } else {
            previewWrapper.classList.add('d-none');
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    addOpenAddModalEventHandler();
    submitBookAddEditFormHandler();
    //Book cover preview
    previewUploadedCoverImage();
});

