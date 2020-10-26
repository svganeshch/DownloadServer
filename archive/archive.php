<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";
include_once "$path" . "/utils/fileFuncs.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['device'])) {
    $device = $_POST['device'];
    $archive_token = getArchivetoken();

    if ($archive_token) {
      $token = $archive_token->token_identifier;
    } else {
      $insertNewToken = insertArchiveToken();
      $token = $insertNewToken['token_identifier'];
    }
    exit(ARCHIVE_SERVER_URL . "archive.php?token={$token}&device={$device}");
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  if (
    isset($_GET['token']) && !empty($_GET['token'])
    && isset($_GET['device']) && !empty($_GET['device'])
  ) {

    $device_files = array();
    $device = $_GET['device'];
    $archive_token = $_GET['token'];

    $archive_token = checkArchiveToken($archive_token);

    if (!$archive_token) header("Location: /error404.html");

    if ($archive_token->time_before_expire < time()) {
      header("Location: /error404.html");
    }

    foreach ($VERSIONS as $version) {
      foreach ($VARIANTS as $variant) {
        $builds_path = BUILD_FILES_DIRECTORY . '/' . $version . '/' . $variant . '/' . $device;
        $device_files[$version][$variant] = getBuilds($builds_path);
      }
    }
  }
}
if (!isset($device_files) && empty($device_files)) {
  exit(http_response_code(500));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="assets/css/materialize.min.css" />
  <link rel="stylesheet" href="assets/css/materialdesignicons.min.css" />
  <link rel="stylesheet" href="assets/datatables/datatables.min.css" />
  <link rel="stylesheet" href="assets/css/datatableTheme.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <title>ArrowOS | Archive</title>
</head>

<body>
  <nav class="navbar z-depth-0 center" role="navigation">
    <div class="nav-wrapper container">
      <a href="#!" class="left brand-logo"><img class="navlogo" src="assets/images/ArrowLogo.svg" alt="Arrow Logo" /></a>
      <a class="logo-text">&nbsp;ArrowOS Archive Download</a>
    </div>
  </nav>
  <main>
    <div class="row">
      <div class="container">
        <div class="col s12">
          <div class="card grey darken-4">
            <div class="card-content white-text">
              <?php foreach ($device_files as $version => $variants) {
                foreach ($variants as $variant => $builds) {
                  if (empty($builds)) continue;
                  echo "<h5>".ucfirst($variant)."</h5>"; ?>
                  <table class="dataTable display compact highlight">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Last modified</th>
                        <th>Size</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($builds as $build) {
                        $filename = explode('/', $build);
                        $filename = end($filename); ?>
                        <tr id="table-row-file" data-filepath="<?php echo $build ?>" data-userip="<?php echo $_SERVER['REMOTE_ADDR'] ?>">
                          <td><?php echo $filename; ?></td>
                          <td><?php echo date("d-m-y h:i", filemtime($build)) ?></td>
                          <td><?php echo number_format((float)filesize($build) / 1000000, 2, '.', '') . " MB" ?>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
              <?php }
              } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer class="page-footer grey darken-4">
    <div class="container">
      <div class="center">
        <a class="btn-floating pulse btn-small waves-effect waves-light white" href="https://t.me/arrowos"><i class="mdi mdi-telegram grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button" href="https://github.com/arrowos"><i class="mdi mdi-github grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button" href="https://review.arrowos.net"><i class="mdi mdi-git grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button" href="https://crowdin.com/project/arrowos"><i class="mdi mdi-translate grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button" href="https://stats.arrowos.net"><i class="mdi mdi-chart-box-outline grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button" href="https://blog.arrowos.net"><i class="mdi mdi-blogger grey-text text-darken-3"></i></a>
      </div>
      <br />
    </div>
    <div class="footer-copyright">
      <div class="container footer-center">
        Designed by
        <b><a style="font-size: medium" class="white-text" href="https://t.me/harshv23/">HarshV23
          </a></b><br />Copyright © 2020 ArrowOS<br /><br />
      </div>
    </div>
  </footer>

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/materialize.min.js"></script>
  <script src="assets/datatables/datatables.min.js"></script>
  <script src="assets/js/archive.js"></script>
</body>

</html>