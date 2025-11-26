async function getBookDetailsApi(bookId) {
    return await fetch("/actions/admin-book-details-action.php?id=" + bookId + "")
        .then(function (response) {
            return response.json();
        });
}

async function returnBookApi(bookId) {
    const formData = new FormData()
    formData.set('id', bookId)
    const response = await fetch("/actions/admin-return-book-action.php", {
        method: "POST",
        headers: {
            "accept": "application/json"
        },
        body: formData
    });

    return await response.json();
}

async function deleteBookApi(bookId) {
    const response = await fetch("/actions/admin-delete-book-action.php?id=" + bookId, {
        method: "DELETE",
        headers: {
            "accept": "application/json"
        }
    });

    return await response.json();
}
