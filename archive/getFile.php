<?php
$file_info = array();
$filepath = explode('/', $_POST['filepath']);

$file_info['filename'] = end($filepath);
$file_info['device'] = $filepath[(count($filepath) - 1) - 1];
$file_info['variant'] = $filepath[(count($filepath) - 1) - 2];
$file_info['version'] = $filepath[(count($filepath) - 1) - 3];
$file_info['file_sha256'] = hash_file("sha256", $_POST['filepath']);

exit(json_encode($file_info));
