<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";
include_once "$path" . "/utils/fileFuncs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['device']) && isset($_POST['file_sha256']) && isset($_POST['version']) && isset($_POST['variant']) && isset($_POST['filename'])) {
        $device = $_POST['device'];
        $file_sha256 = $_POST['file_sha256'];
        $file_version = $_POST['version'];
        $file_variant = $_POST['variant'];
        $filename = $_POST['filename'];

        $localFile = BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $device . '/' . $filename;
        if (!file_exists($localFile)) http_response_code(404);

        $file = getFileBySHA($file_sha256, $file_version, $file_variant);

        if ($file) {
            if ($data = getFileUrl($file->file_sha256, $file_version, $file_variant)) {
                $token_identifier = $data->token_identifier;
            } else {
                $insertedData = insertFileUrl($file->file_sha256, $file_version, $file_variant);
                $token_identifier = $insertedData['token_identifier'];
            }
            exit(SERVER_DOWN_URL . "download.php?token={$token_identifier}&version={$file_version}&variant={$file_variant}&device={$device}");
        } else {
            $file = BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $device . '/' . $filename;
            if (file_exists($file) && $file_sha256 === hash_file("sha256", $file)) {
                if (insertNewFile($file_sha256, $filename, $file_version, $file_variant)) {
                    $insertedData = insertFileUrl($file_sha256, $file_version, $file_variant);
                    $token_identifier = $insertedData['token_identifier'];

                    exit(SERVER_DOWN_URL . "download.php?token={$token_identifier}&version={$file_version}&variant={$file_variant}&device={$device}");
                } else {
                    http_response_code(404);
                }
            } else {
                http_response_code(404);
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (
        isset($_GET['token']) && !empty($_GET['token'])
        && isset($_GET['version']) && !empty($_GET['version'])
        && isset($_GET['variant']) && !empty($_GET['variant'])
        && isset($_GET['device']) && !empty($_GET['device'])
    ) {

        $file_token = $_GET['token'];
        $file_version = $_GET['version'];
        $file_variant = $_GET['variant'];
        $device = $_GET['device'];
        $file_url = getFileUrlByToken($file_token, $file_version, $file_variant);

        if (!$file_url) header("Location: /error404.html");

        if ($file_url->time_before_expire < time()) {
            //dropFileUrlByToken($file_token, $file_version, $file_variant);
            header("Location: /error404.html");
        }

        $file = getFileBySHA($file_url->file_sha256, $file_version, $file_variant);

        if (!empty($file->filename) && file_exists(BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $device . '/' . $file->filename)) {
            $filename = urldecode($file->filename);
            $filepath = BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/' . $device . '/' . $filename;

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('X-Sendfile: ' . $filepath);
            exit;
        } else {
            http_response_code(404);
        }
    }
}
