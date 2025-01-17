<?php

use Google\Client;
use Google\Service\Drive;

class DriveFun {

    private $client;
    private $driveService;

    public function __construct()
    {
        if (!headers_sent()) {
            ob_start();
        }

        $this->client = new Client();
        $this->client->setAuthConfig('client_secret.json');
        $this->client->addScope(Drive::DRIVE);

        if (isset($_SESSION['access_token'])) {
            $accessToken = $_SESSION['access_token'];
            $this->client->setAccessToken($accessToken);
        } elseif (isset($_COOKIE['Google-Drive'])) {
            $accessToken = base64_decode($_COOKIE['Google-Drive']);
            $accessToken = json_decode($accessToken, true);
            
            $this->client->setAccessToken($accessToken['access_token']);
        } else {
            echo "Access token not found in session or cookie!";
            exit;
        }

        if ($this->client->isAccessTokenExpired()) {
            if (isset($_COOKIE['Google-Drive'])) {
                $accessToken = base64_decode($_COOKIE['Google-Drive']);
                $accessToken = json_decode($accessToken, true);
            }

            $newAccessToken = $this->client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);

            $_SESSION['access_token'] = $newAccessToken['access_token'];

            if(!isset($_COOKIE['Google-Drive']))
            {
                setcookie('Google-Drive', base64_encode(json_encode($newAccessToken)), time() + (86400 * 30), '/');
            }
            $this->client->setAccessToken($newAccessToken['access_token']);
        }

        $this->driveService = new Google\Service\Drive($this->client);

        if (ob_get_level() > 0) {
            ob_end_flush();
        }
    }

    public function searchFiles()
    {
        try {
            $files = array();
            $pageToken = null;

            do {
                $sFolderid = "root";
                if(isset($_GET["Folderid"]) && $_GET["Folderid"] != "") $sFolderid = $_GET["Folderid"];
                
                $response = $this->driveService->files->listFiles([
                    'q' => "'$sFolderid' in parents",
                    'spaces' => 'drive',
                    'pageToken' => $pageToken,
                    'fields' => 'nextPageToken, files(id, name, mimeType)',
                    'orderBy' => 'folder, name'
                ]);

                $files = array_merge($files, $response->files);
                $pageToken = $response->nextPageToken;
            } while ($pageToken != null);

            return $files;

        } catch (Exception $e) {
            echo "Error Message: " . $e->getMessage();
        }
    }

    public function upload($file)
    {
        try {
            
            $fileName = $file['name'];
            $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName);
            $randomPrefix = rand(10000, 99999);
            // $ext = pathinfo($fileName);

            $fileName = $randomPrefix . "_" . $fileName;

            $fileMetadata = new Drive\DriveFile(array(
            'name' => $fileName));
            $content = file_get_contents($file['tmp_name']);
            $file = $this->driveService->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => $file['type'],
                'uploadType' => 'multipart',
                'fields' => 'id'));

            return $file->id;

        } catch(Exception $e) {
            echo "Error Message: ".$e;
        }
    }

    public function searchSingleFile()
    {
        try
        {
            if (!isset($_GET['Fileid']) || empty($_GET['Fileid'])) {
                throw new Exception('File ID is required!');
            }

            $fileId = $_GET['Fileid'];

            $file = $this->driveService->files->get($fileId, [
                'fields' => 'id, name, mimeType'
            ]);

            

            return $file;

        } catch (Exception $e) {
            echo "Error Message: " . $e->getMessage();
            return null;
        }
    }

    function downloadFile($fileId)
    {
        try {

            $response = $this->driveService->files->get($fileId, array(
                'alt' => 'media'));
            $content = $response->getBody()->getContents();
            return $content;

        } catch(Exception $e) {
            echo "Error Message: ".$e;
        }

    }


}


?>