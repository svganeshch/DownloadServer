$(document).ready(function () {
    // Datatable
    $('.dataTable').DataTable({
        'order': [
            [1, 'desc']
        ],
        'sDom': "<'row'<'col s6 right'f>>" +
            "<'row'<tr>>",
        'pagingType': 'simple',
        'columnDefs': [{
            'targets': '_all',
            'className': 'dt-center',
        }],
    });

    var arrowMirror = '';
    $('body').on('click', '#table-row-file', function () {
        filepath = $(this).data('filepath');
        userip = $(this).data('userip');

        $.ajax({
            type: 'POST',
            data: {
                'filepath': filepath,
                'userip': userip
            },
            url: 'getFile.php',
            success: function (data) {
                arrowMirror = data;
            },
            complete: function (xhr) {
                if (xhr.status === 200) {
                    if (arrowMirror != null && arrowMirror != '') {
                        window.location.href = arrowMirror;
                    } else {
                        alert('Failed to fetch mirror!');
                    }
                }
            }
        });
    })
});