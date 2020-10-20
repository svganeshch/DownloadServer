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
        alert('ola');
        fileSHA = $(this).data('sha256');
        version = $(this).data('version');
        variant = $(this).data('variant');
        filename = $(this).data('filename');

        $.ajax({
            type: 'POST',
            data: {
                'file_sha256': fileSHA,
                'version': version,
                'variant': variant,
                'filename': filename
            },
            url: 'http://localhost/download.php',
            success: function (data) {
                arrowMirror = data;
                alert(arrowMirror);
            },
            complete: function (xhr) {
                alert('yomomma');
                if (xhr.status === 200) {
                    alert('yomomma in');
                    if (arrowMirror != null && arrowMirror != '') {
                        alert('mehhhhhhhhhh')
                        window.location.href = arrowMirror;
                    }
                }
            }
        });
    })
});