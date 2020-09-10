<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";
include_once "$path" . "/utils/fileFuncs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_sha256']) && isset($_POST['version']) && isset($_POST['variant']) && isset($_POST['filename'])) {
        $file_sha256 = $_POST['file_sha256'];
        $file_version = $_POST['version'];
        $file_variant = $_POST['variant'];
        $filename = $_POST['filename'];

        $file = getFileBySHA($file_sha256, $file_version, $file_variant);

        if ($file) {
            if ($data = getFileUrl($file->file_sha256)) {
                $token_identifier = $data->token_identifier;
            } else {
                $insertedData = insertFileUrl($file->file_sha256);
                $token_identifier = $insertedData['token_identifier'];
            }
            exit(SERVER_DOWN_URL . "download.php?token={$token_identifier}&version={$file_version}&variant={$file_variant}");
        } else {
            $file = BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $filename;
            if (file_exists($file) && $file_sha256 === hash_file("sha256", $file)) {
                if (insertNewFile($file_sha256, $filename, $file_version, $file_variant)) {
                    $insertedData = insertFileUrl($file_sha256);
                    $token_identifier = $insertedData['token_identifier'];

                    exit(SERVER_DOWN_URL . "download.php?token={$token_identifier}&version={$file_version}&variant={$file_variant}");
                } else {
                    exit("No such file exists");
                }
            } else {
                exit("No longer available");
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (
        isset($_GET['token']) && !empty($_GET['token'])
        && isset($_GET['version']) && !empty($_GET['version'])
        && isset($_GET['variant']) && !empty($_GET['variant'])
    ) {

        $file_token = $_GET['token'];
        $file_version = $_GET['version'];
        $file_variant = $_GET['variant'];
        $file_url = getFileUrlByToken($file_token);

        if (!$file_url) exit("There are no downloads available for this token!");

        if ($file_url->time_before_expire < time()) {
            dropFileUrlByToken($file_token);
            exit("Token expired, please generate a new link from " . DOWNLOAD_PAGE_URL);
        }

        $file = getFileBySHA($file_url->file_sha256, $file_version, $file_variant);

        if (!empty($file->filename) && file_exists(BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $file->filename)) {
            $filename = urldecode($file->filename);
            $filepath = BUILD_FILES_DIRECTORY . '/' . $filename;

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('X-Sendfile: ' . $filepath);
            exit;
        }
    }
}
