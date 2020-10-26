<?php
$filepath = explode('/', $_POST['filepath']);
$userip = $_POST['userip'];
$mirror1 = $_POST['mirror1'];

$filename = end($filepath);
$device = $filepath[(count($filepath) - 1) - 1];
$variant = $filepath[(count($filepath) - 1) - 2];
$version = $filepath[(count($filepath) - 1) - 3];
$file_sha256 = hash_file("sha256", $_POST['filepath']);

$mirror_curl = curl_init();
curl_setopt($mirror_curl, CURLOPT_URL, $mirror1);
curl_setopt($mirror_curl, CURLOPT_POST, true);
curl_setopt(
    $mirror_curl,
    CURLOPT_POSTFIELDS,
    http_build_query(array(
        'device' => $device,
        'file_sha256' => $file_sha256,
        'version' => $version,
        'variant' => $variant,
        'filename' => $filename
    ),),
);
curl_setopt($mirror_curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($mirror_curl, CURLOPT_HEADER, true);
curl_setopt($mirror_curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($mirror_curl, CURLOPT_MAXREDIRS, 5);

$mirror_content = curl_exec($mirror_curl);
$mirror_resp_code = curl_getinfo($mirror_curl, CURLINFO_RESPONSE_CODE);
$headerSize = curl_getinfo($mirror_curl, CURLINFO_HEADER_SIZE);
$mirror_url = substr($mirror_content, $headerSize);
curl_close($mirror_curl);

if ($mirror_resp_code == 200)
    exit($mirror_url);
else
    exit();
