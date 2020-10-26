$(document).ready(function () {
    //$('#progress-modal').modal();
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

    var fileInfo = '';
    $('body').on('click', '#table-row-file:not(.clicked)', function () {
        $('#table-row-file').addClass('clicked');
        filepath = $(this).data('filepath');
        userip = $(this).data('userip');

        $.ajax({
            type: 'POST',
            data: {
                'filepath': filepath
            },
            url: 'getFile.php',
            beforeSend: function () {
                //$('#progress-modal').modal('open');
            },
            success: function (data) {
                fileInfo = data;
            },
            complete: function (xhr) {
                if (xhr.status === 200) {
                    fileInfo = $.parseJSON(fileInfo);
                    if (!$.isEmptyObject(fileInfo)) {
                        $.ajax({
                            type: 'POST',
                            data: {
                                'device': fileInfo.device,
                                'file_sha256': fileInfo.file_sha256,
                                'version': fileInfo.version,
                                'variant': fileInfo.variant,
                                'filename': fileInfo.filename
                            },
                            url: '/download.php',
                            success: function (data) {
                                arrowMirrorUrl = data;
                            },
                            complete: function (xhr) {
                                if (xhr.status === 200)
                                    window.location.href = arrowMirrorUrl;
                                else
                                    alert('Failed to fetch url for file');
                            }
                        });
                    } else {
                        alert('Failed to fetch file info!');
                    }
                }
                //$('#progress-modal').modal('close');
                $('#table-row-file').removeClass('clicked');
            }
        });
    })
});