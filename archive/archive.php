<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";
include_once "$path" . "/utils/fileFuncs.php";
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
      <a href="#!" class="left brand-logo"><img class="navlogo" src="assets/images/ArrowLogo.svg"
          alt="Arrow Logo" /></a>
      <a class="logo-text">&nbsp;ArrowOS Archive Download</a>
    </div>
  </nav>
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (
      isset($_GET['token']) && !empty($_GET['token'])
      && isset($_GET['version']) && !empty($_GET['version'])
      && isset($_GET['variant']) && !empty($_GET['variant'])
    ) {

      //$device = $_GET['device'];
      $file_token = $_GET['token'];
      $file_version = $_GET['version'];
      $file_variant = $_GET['variant'];
      $file_url = getFileUrlByToken($file_token, $file_version, $file_variant);

      if (!$file_url) header("Location: /error404.html");

      if ($file_url->time_before_expire < time()) {
        header("Location: /error404.html");
      }

      $file_path = '../' . BUILD_FILES_DIRECTORY . '/' . $file_version . '/' . $file_variant . '/';
      $build_files = array_diff(scandir($file_path), array('.', '..'));
  ?>
  <main>
    <div class="row">
      <div class="container">
        <div class="col s12">
          <div class="card grey darken-4">
            <div class="card-content white-text">
              <table class="dataTable display compact">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Last modified</th>
                    <th>Size</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($build_files as $file) { ?>
                  <tr id="table-row-file" data-sha256="<?php echo hash_file("sha256", $file_path . $file) ?>"
                    data-version="<?php echo $file_version ?>" data-variant="<?php echo $file_variant ?>"
                    data-filename="<?php echo $file ?>">
                    <td><?php echo $file; ?></td>
                    <td><?php echo date("d-m-y h:i", filemtime($file_path . $file)) ?></td>
                    <td><?php echo number_format((float)filesize($file_path . $file) / 1000000, 2, '.', '') . " MB" ?>
                    </td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <?php }
} ?>
  <footer class="page-footer grey darken-4">
    <div class="container">
      <div class="center">
        <a class="btn-floating pulse btn-small waves-effect waves-light white" href="https://t.me/arrowos"><i
            class="mdi mdi-telegram grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button"
          href="https://github.com/arrowos"><i class="mdi mdi-github grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button"
          href="https://review.arrowos.net"><i class="mdi mdi-git grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button"
          href="https://crowdin.com/project/arrowos"><i class="mdi mdi-translate grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button"
          href="https://stats.arrowos.net"><i class="mdi mdi-chart-box-outline grey-text text-darken-3"></i></a>
        <a class="btn-floating pulse btn-small waves-effect waves-light white footer-button"
          href="https://blog.arrowos.net"><i class="mdi mdi-blogger grey-text text-darken-3"></i></a>
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