<?php

$sTableView = "<center><div id='TableContainer'><table class='mt-5 table table-dark table-striped' border='1' cellspacing='0' style='width: 80%;'>
    <tr align='center'>
        <th>S.No</th>
        <th>Files</th>
        <th>Actions</th>
    </tr>";

    $Sno = 1;
    foreach ($files as $file) {
        if ($file->mimeType === 'application/vnd.google-apps.folder') {
            $sName = '<a href="gdrive.php?Folderid='.$file->id.'" class="btn btn-primary">'.$file->name.'</a>';
            $sDownload = "";
            $sDelete = "<a class='btn btn-danger' href='#'>Delete</a>";
        } else {
            $sName = '<a href="ViewFile.php?Fileid='.$file->id.'" target="_blank" style="color: white; text-decoration: none;">'.$file->name.'</a>';
            $sDownload = '<a class="btn btn-success action" href="ViewFile.php?download='.$file->id.'&fileName='.$file->name.'" >Download</a>';
            $sDelete = '<a class="btn btn-danger action" href="ViewFile.php?delete='.$file->id.'">Delete</a>';
        }

        $sTableView .= "<tr align='center'>
            <td>$Sno</td>
            <td>$sName</td>
            <td>
                $sDownload $sDelete
            </td>
        </tr>";

        $Sno++;
    }

$sTableView .= "</table></div></center>";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <form method="POST" enctype="multipart/form-data">
        <div class="container">
            <div class="row mt-5">
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#myModalUpload">
                        Upload File
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                        Create Folder
                    </button>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>

        <!-- The Modal -->
        <div class="modal fade" id="myModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Create Folder</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <input class="form-control" type="text" name="folderName" placeholder="Folder Name"> <br>
                        <input class="btn btn-success float-end" type="submit" name="create" value="Create">
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="myModalUpload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Upload File</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <input class="form-control" type="file" name="uploadFile"> <br>
                        <input class="btn btn-success float-end" type="submit" name="upload" value="Upload">
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?= $sTableView ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.15.10/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function () {
            $(document).on('click', '.action', function (e) {
                e.preventDefault();
                actionUrl = $(this).attr('href');

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while the file is being processed.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    success: async function (response) {
                        console.log(response)
                        Swal.close();

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            background: '#fff',
                            color: '#000',
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });

                        if (response == 1) {
                            Toast.fire({
                                icon: "success",
                                title: "Success",
                                text: "File Downloaded Successfully..."
                            });
                        }
                        else if (response == 2) {
                            Toast.fire({
                                icon: "success",
                                title: "Success",
                                text: "File Deleted Successfully..."
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "Error",
                                text: "Oops! Something went wrong..."
                            });
                        }

                        $('#TableContainer').load(" #TableContainer>*");
                    },
                    error: function (xhr, status, error) {
                        Swal.close();
                        console.error('Error:', error);
                        alert('An error occurred while submitting the form.');
                        console.log('Response:', xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>