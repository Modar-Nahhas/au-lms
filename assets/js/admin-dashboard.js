let bookTable = null;

function statusBadge(status) {
    let cls = '';
    switch (status) {
        case "Available":
            cls = 'success';
            break;
        case "Onloan":
            cls = 'warning';
            break;
        case 'Deleted':
            cls = 'danger';
            break;

    }
    return `<span class="badge badge-${cls} p-2">${status}</span>`;
}

function drawTable() {
    bookTable = $('#books-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/actions/admin-books-data-action.php',
            type: 'GET'
        },
        lengthMenu: [[3, 5, 10, 500], [3, 5, 10, "All"]],
        pageLength: 3, // how many rows per page
        ordering: false,
        columns: [
            {data: 'id', visible: false},
            {data: 'isbn'},
            {data: 'title'},
            {data: 'author'},
            {data: 'category'},
            {data: 'language'},
            {
                data: 'status',
                render: function (data, type, row) {
                    return statusBadge(row.status);
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    const disabledReturn = row.status !== 'Onloan' ? 'disabled' : '';
                    const disabledDelete = row.status === 'Deleted' || row.status === 'Onloan' ? 'disabled' : '';

                    return `
                        <div class="d-flex flex-wrap" style="gap:0.1rem;">
                            <button class="btn btn-sm btn-primary mr-1 btn-view" data-id="${row.id}">
                                View
                            </button>
                            <button class="btn btn-sm btn-warning mr-1 btn-edit" data-id="${row.id}">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-info mr-1 btn-return" data-id="${row.id}" ${disabledReturn}>
                                Return
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id}" ${disabledDelete}>
                                Delete
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // VIEW
    $('#books-table').on('click', '.btn-view', function () {
        const id = $(this).data('id');
        window.location.href = 'book-details.php?id=' + id;
    });
    // EDIT
    $('#books-table').on('click', '.btn-edit', async function () {
        const id = $(this).data('id');
        const book = await getBookDetailsApi(id);
        openEditBookModal(book);
        if (!book) return;
        return;
    });
    // Return
    $('#books-table').on('click', '.btn-return', async function () {
        const id = $(this).data('id');
        const res = await returnBookApi(id);
        if (!res) return;
        if (res.success) {
            bookTable.ajax.reload(null, false);
        }
        return;
    });
    // Delete
    $('#books-table').on('click', '.btn-delete', async function () {
        const id = $(this).data('id');
        if (!confirm("Are you sure you want to delete this book?")) return;
        const res = await deleteBookApi(id);
        if (!res) return;
        if (res.success) {
            bookTable.ajax.reload(null, false);
        } else {
            showAdminMessage(res.message, 'warning')
        }
        return;
    });
}

$(document).ready(function () {
    drawTable();

});
