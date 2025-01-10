<?php
if (isset($_GET["Fileid"]))
{
    require_once 'vendor/autoload.php';
    require_once 'clsDriveFun.php';

    $objDriveFun = new DriveFun();

    $singleFile = $objDriveFun->searchSingleFile();
    
    $mimeType = $singleFile->mimeType;
    $fileName = $singleFile->name;
    $fileLink = 'https://drive.google.com/uc?id=' . $singleFile->id; // Direct link format
    
    if (strpos($mimeType, 'image/') === 0) {
        // Image preview
        echo "<img src='" . htmlspecialchars($fileLink) . "' alt='" . htmlspecialchars($fileName) . "' style='max-width:80%; height:auto;'>";
    } elseif (strpos($mimeType, 'video/') === 0) {
        // Video preview
        echo "<video controls style='max-width:100%; height:auto;'>
                <source src='" . htmlspecialchars($fileLink) . "' type='" . htmlspecialchars($mimeType) . "'>
                Your browser does not support the video tag.
              </video>";
    } elseif (strpos($mimeType, 'audio/') === 0) {
        // Audio preview
        echo "<audio controls>
                <source src='" . htmlspecialchars($fileLink) . "' type='" . htmlspecialchars($mimeType) . "'>
                Your browser does not support the audio tag.
              </audio>";
    } elseif ($mimeType === 'application/pdf') {
        // PDF preview
        echo "<iframe src='" . htmlspecialchars($fileLink) . "' style='width:100%; height:600px; border:none;'></iframe>";
    } else {
        // Default download link for unsupported file types
        echo "<p>Unsupported file type. <a href='" . htmlspecialchars($fileLink) . "' download>Download File</a></p>";
    }

}

?>