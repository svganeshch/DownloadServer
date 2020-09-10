<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path"."/config/dbconn.php";

try {
    $db_conn = new PDO("mysql:host=" . DB_HOST . "; dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit($e->getMessage());
}

function getFileBySHA($file_sha256, $version, $variant) {
    global $db_conn;

    $query = $db_conn->prepare("SELECT * FROM `{$version}_files_{$variant}` where file_sha256='{$file_sha256}'");
    $query->execute();
    $results = $query->setFetchMode(PDO::FETCH_OBJ);

    $file = false;

    foreach($query->fetchAll() as $row) {
        $file = $row;
    }

    return $file;
}

function getFileUrl($file_sha256, $version, $variant)
{
    global $db_conn;

    $query = $db_conn->prepare("SELECT * FROM `{$version}_file_urls_{$variant}` where file_sha256='{$file_sha256}' and ip_address='" . $_SERVER['REMOTE_ADDR'] . "' and time_before_expire >= '" . time() . "'");

    $query->execute();
    $results = $query->setFetchMode(PDO::FETCH_OBJ);

    $file_url = false;

    foreach ($query->fetchAll() as $row) {
        $file_url = $row;
    }

    return $file_url;
}

function getFileUrlByToken($token_identifier, $version, $variant) {
    global $db_conn;

    $query = $db_conn->prepare("SELECT * FROM `{$version}_file_urls_{$variant}` where token_identifier='{$token_identifier}'");
    $query->execute();
    $results = $query->setFetchMode(PDO::FETCH_OBJ);

    $file_url = false;

    foreach($query->fetchAll() as $row) {
        $file_url = $row;
    }

    return $file_url;
}

function dropFileUrlByToken($token_identifier, $version, $variant) {
    global $db_conn;

    $query = $db_conn->prepare("DELETE FROM `{$version}_file_urls_{$variant}` where token_identifier='{$token_identifier}'");
    $query->execute();
}

function randStr($length = 60) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function insertFileUrl($file_sha256, $version, $variant) {
    global $db_conn;

    $data = [
        'file_sha256' => $file_sha256,
        'token_identifier' => randStr(),
        'time_before_expire' => (time() + TIME_BEFORE_EXPIRE*60*60),
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ];

    $sql = "INSERT INTO `{$version}_file_urls_{$variant}` (file_sha256, token_identifier, time_before_expire, ip_address) VALUES (:file_sha256, :token_identifier, :time_before_expire, :ip_address)";
    $query = $db_conn->prepare($sql);
    $query->execute($data);

    return $data;
}

function insertNewFile($file_sha256, $filename, $version, $variant) {
    global $db_conn;

    $data = [
        'file_sha256' => $file_sha256,
        'filename' => $filename
    ];

    $sql = "INSERT INTO `{$version}_files_{$variant}` (file_sha256, filename) VALUES (:file_sha256, :filename)";
    $query = $db_conn->prepare($sql);
    if ($query->execute($data)) return true;
    else return false;
}
?>