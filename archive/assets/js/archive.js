$(document).ready(function () {
  // Datatable
    $('.dataTable').DataTable({
        'order': [
            [1, 'desc']
        ],
        'sDom': "<'row'<tr>>",
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
        //Block page while creating link 
        $.blockUI({
            message: '<h1><i class="mdi mdi-loading mdi-spin mdi-48px"> </i>Wait</h1>',
            overlayCSS: {
                backgroundColor: '#FFF',
                opacity: 0.7,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
        });
        $.ajax({
            type: 'POST',
            data: {
                'filepath': filepath
            },
            url: 'getFile.php',
            beforeSend: function () {
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
                $.unblockUI();
                $('#table-row-file').removeClass('clicked');
            }
        });
    })
});
