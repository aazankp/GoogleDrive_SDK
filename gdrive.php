<?php

require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setClientId('863234079523-vki9n3bk4sns9apiatto7kni5osednjk.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/GoogleDrive/access_token.php');
$client->addScope(Google\Service\Drive::DRIVE);
session_start();

date_default_timezone_set("Asia/Karachi");

if (isset($_GET['code'])) {

    $access_token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($access_token['error'])) {
        echo "Error fetching token: " . $access_token['error_description'];
    } else {
        $client->setAccessToken($access_token['access_token']);
        setcookie('Google-Drive', base64_encode(json_encode($access_token)), time() + (86400 * 30), '/');
        header('location: gdrive.php');
    }

} else {
    require_once 'clsDriveFun.php';

    ob_start();

    $objDriveFun = new DriveFun;
    $files = $objDriveFun->searchFiles();

    require_once 'view_list.php';

    $sFolderid = "root";
    if(isset($_GET["Folderid"]) && $_GET["Folderid"] != "") $sFolderid = $_GET["Folderid"];
    
    if (isset($_POST['upload']))
    {
        $uploadFile = $_FILES['uploadFile'];
        $uploadFile = $objDriveFun->upload($uploadFile, $sFolderid);

        if(isset($_GET["Folderid"]) && $_GET["Folderid"] != "")
        {
            header("location: gdrive.php?Folderid=$sFolderid");
        }
        else {
            header('location: gdrive.php');
        }
    }

    if (isset($_POST['create']))
    {
        $folderName = $_POST['folderName'];
        $createFolder = $objDriveFun->createFolder($folderName, $sFolderid);

        if(isset($_GET["Folderid"]) && $_GET["Folderid"] != "")
        {
            header("location: gdrive.php?Folderid=$sFolderid");
        }
        else {
            header('location: gdrive.php');
        }
    }

    ob_end_flush();
}

?>