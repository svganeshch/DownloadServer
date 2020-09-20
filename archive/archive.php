<?php
$path = $_SERVER['DOCUMENT_ROOT'];
include_once "$path" . "/config/dbconn.php";
include_once "$path" . "/utils/fileFuncs.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>ArrowOS | Downloads</title>

    <!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="/archive/css/materialize.min.css" media="screen,projection" />
</head>

<body>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" class="brand-logo center">ArrowOS archive</a>
            </div>
        </nav>
    </div>

    <main>
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
                <div class="container z-depth-2">
                    <div class="row">
                        <div class="col s12">
                            <table class="highlight responsive-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Last modified</th>
                                        <th>Size</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($build_files as $file) { ?>
                                        <tr id="table-row-file"
                                            data-sha256="<?php echo hash_file("sha256", $file_path . $file)?>"
                                            data-version="<?php echo $file_version ?>"
                                            data-variant="<?php echo $file_variant ?>"
                                            data-filename="<?php echo $file ?>"
                                        >
                                            <td><?php echo $file; ?></td>
                                            <td><?php echo date("d-m-y h:i", filemtime($file_path . $file)) ?></td>
                                            <td><?php echo number_format((float)filesize($file_path . $file) / 1000000, 2, '.', '') . " MB" ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        <?php }
        } ?>
    </main>

    <!--JavaScript at end of body for optimized loading-->
    <script type="text/javascript" src="/archive/js/jquery.min.js"></script>
    <script type="text/javascript" src="/archive/js/materialize.min.js"></script>
    <script type="text/javascript" src="/archive/js/archive.js"></script>
</body>
</html>