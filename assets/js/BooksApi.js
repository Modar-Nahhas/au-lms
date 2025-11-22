async function getBookDetails(bookId) {
    return await fetch("/actions/admin-book-details-action.php?id=" + bookId + "")
        .then(function (response) {
            return response.json();
        });
}