<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include "$path"."/config/dbconn.php";
include "$path"."/utils/fileFuncs.php";

if (isset($_POST['file_sha256']) && isset($_POST['version']) && isset($_POST['variant'])) {
    $file_sha256 = $_POST['file_sha256'];
    $file_version = $_POST['version'];
    $file_variant = $_POST['variant'];

    $file = getFileBySHA($file_sha256, $file_version, $file_variant);

    if ($file) {
        if ($data = getFileUrl($file->file_sha256)) {
            $token_identifier = $data->token_identifier;

            exit("'{$SERVER_DOWN_URL}'download.php?token='{$token_identifier}'?version='{$file_version}'?variant='{$file_variant}'");
        } else {
            $insertedData = insertFileUrl($file->file_sha256);
            $token_identifier = $insertedData['token_identifier'];

            exit("'{$SERVER_DOWN_URL}'download.php?token='{$token_identifier}'?version='{$file_version}'?variant='{$file_variant}'");
        }
    } else {
        exit("No file exists or no longer available");
    }
}

if (isset($_GET['token']) && !empty($_GET['token'])
    && isset($_GET['version']) && !empty($_GET['version'])
    && isset($_GET['variant']) && !empty($_GET['variant'])) {

    $file_token = $_GET['token'];
    $file_version = $_GET['version'];
    $file_variant = $_GET['variant'];
    $file_url = getFileUrlByToken($file_token);

    if (!$file_url) exit("There are no downloads available for this token!");
    if ($file_url->time_before_expire < time()) exit("Link expired!");

    $file = getFileBySHA($file_url->file_sha256, $file_version, $file_variant);

    if (!empty($file->filename) && file_exists(BUILD_FILES_DIRECTORY.'/'.$file->filename)) {
        $filename = urldecode($file->filename);
        $filepath = BUILD_FILES_DIRECTORY.'/'.$filename;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
        readfile($filepath);
        exit;
    }
}
