<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";

try {
    $db_conn = new PDO("mysql:host=" . DB_HOST . "; dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
}

function getFileBySHA($file_sha256, $version, $variant)
{
    try {
        global $db_conn;

        $query = $db_conn->prepare("SELECT * FROM `{$version}_files_{$variant}` where file_sha256='{$file_sha256}'");
        $query->execute();
        $results = $query->setFetchMode(PDO::FETCH_OBJ);

        $file = false;

        foreach ($query->fetchAll() as $row) {
            $file = $row;
        }

        return $file;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}

function getFileUrl($file_sha256, $version, $variant)
{
    try {
        global $db_conn;

        $query = $db_conn->prepare("SELECT * FROM `{$version}_file_urls_{$variant}` where file_sha256='{$file_sha256}' and ip_address='" . $_SERVER['REMOTE_ADDR'] . "' and time_before_expire >= '" . time() . "'");

        $query->execute();
        $results = $query->setFetchMode(PDO::FETCH_OBJ);

        $file_url = false;

        foreach ($query->fetchAll() as $row) {
            $file_url = $row;
        }

        return $file_url;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}

function getFileUrlByToken($token_identifier, $version, $variant)
{
    try {
        global $db_conn;

        $ip_whitelist = explode(",", IP_WHITELIST);
        if (in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) {
            $query = $db_conn->prepare("SELECT * FROM `{$version}_file_urls_{$variant}` where token_identifier='{$token_identifier}'");
        } else {
            $query = $db_conn->prepare("SELECT * FROM `{$version}_file_urls_{$variant}` where token_identifier='{$token_identifier}' and ip_address='" . $_SERVER['REMOTE_ADDR'] . "'");
        }
        $query->execute();
        $results = $query->setFetchMode(PDO::FETCH_OBJ);

        $file_url = false;

        foreach ($query->fetchAll() as $row) {
            $file_url = $row;
        }

        return $file_url;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}

function dropFileUrlByToken($token_identifier, $version, $variant)
{
    try {
        global $db_conn;

        $query = $db_conn->prepare("DELETE FROM `{$version}_file_urls_{$variant}` where token_identifier='{$token_identifier}'");
        $query->execute();
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}

function randStr($length = 60)
{
    return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
}

function insertFileUrl($file_sha256, $version, $variant)
{
    try {
        global $db_conn;

        $ip_whitelist = explode(",", IP_WHITELIST);
        if (in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) {
            $time_before_expire = (time() + 6969 * 60 * 60);
        } else {
            $time_before_expire = (time() + TIME_BEFORE_EXPIRE * 60 * 60);
        }

        $data = [
            'file_sha256' => $file_sha256,
            'token_identifier' => randStr(),
            'time_before_expire' => $time_before_expire,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ];

        $sql = "INSERT INTO `{$version}_file_urls_{$variant}` (file_sha256, token_identifier, time_before_expire, ip_address) VALUES (:file_sha256, :token_identifier, :time_before_expire, :ip_address)";
        $query = $db_conn->prepare($sql);
        $query->execute($data);

        return $data;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}

function insertNewFile($file_sha256, $filename, $version, $variant)
{
    try {
        global $db_conn;

        $data = [
            'file_sha256' => $file_sha256,
            'filename' => $filename
        ];

        $sql = "INSERT INTO `{$version}_files_{$variant}` (file_sha256, filename) VALUES (:file_sha256, :filename)";
        $query = $db_conn->prepare($sql);
        if ($query->execute($data)) return true;
        else return false;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        http_response_code(500);
    }
}
