<?php
if (isset($_GET["Fileid"]) && $_GET["Fileid"] != "") {
    require_once 'vendor/autoload.php';
    require_once 'clsDriveFun.php';

    $objDriveFun = new DriveFun();
    $singleFile = $objDriveFun->searchSingleFile($_GET["Fileid"]);
    
    $mimeType = $singleFile->mimeType;
    $fileName = $singleFile->name;
    $fileId = $singleFile->id;

    $getFileContent = $objDriveFun->downloadFile($fileId);

    $fileLink = 'data:' . $mimeType . ';base64,' . base64_encode($getFileContent);

    $documentMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];
    
    if (strpos($mimeType, 'image/') === 0) {
        echo "<img src='" . htmlspecialchars($fileLink) . "' alt='" . htmlspecialchars($fileName) . "' style='max-width:80%; height:auto;'>";
    } elseif (strpos($mimeType, 'video/') === 0) {
        echo "<video controls style='max-width:100%; height:auto;'>
                <source src='" . htmlspecialchars($fileLink) . "' type='" . htmlspecialchars($mimeType) . "'>
                Your browser does not support the video tag.
              </video>";
    } elseif (strpos($mimeType, 'audio/') === 0) {
        echo "<audio controls style='width:100%;'>
                <source src='" . htmlspecialchars($fileLink) . "' type='" . htmlspecialchars($mimeType) . "'>
                Your browser does not support the audio tag.
              </audio>";
    } elseif (in_array($mimeType, $documentMimeTypes)) {
        echo "<iframe src='" . htmlspecialchars($fileLink) . "' style='width:100%; height:600px; border:none;'></iframe>";
    } else {
        echo "<p>Unsupported file type. <a href='" . htmlspecialchars($fileLink) . "' target='_blank'>Open File</a></p>";
    }
    
}
elseif (isset($_GET["download"]) && $_GET["download"] != "")
{
    require_once 'vendor/autoload.php';
    require_once 'clsDriveFun.php';

    $objDriveFun = new DriveFun();

    $getFileContent = $objDriveFun->downloadFile($_GET["download"]);

    if(file_put_contents("img_and_docs/".$_GET["fileName"], $getFileContent)) $response = 1;
    else $response = 0;

    echo $response;
}
elseif (isset($_GET["delete"]) && $_GET["delete"] != "")
{
    require_once 'vendor/autoload.php';
    require_once 'clsDriveFun.php';

    $objDriveFun = new DriveFun();

    $res = $objDriveFun->deleteFile($_GET["delete"]);

    if($res) $response = 2;
    else $response = 0;

    echo $response;
}

?>